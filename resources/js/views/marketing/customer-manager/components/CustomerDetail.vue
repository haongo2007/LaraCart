<template>
  <el-row :gutter="20">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="18" :offset="3">
      <el-form ref="dataForm" :model="dataTemp" label-position="top" :rules="dataRules" class="form-container" label-width="150px">

        <el-row class="cus-el-main-form">
          <el-col :span="12">
            <el-form-item :label="$t('form.firstname')" prop="first_name">
              <el-input v-model="dataTemp.first_name" :placeholder="$t('form.firstname')" />
            </el-form-item>

            <el-form-item :label="$t('form.lastname')" prop="last_name">
              <el-input v-model="dataTemp.last_name" :placeholder="$t('form.lastname')" />
            </el-form-item>

            <el-form-item :label="$t('form.email')" prop="email">
              <el-input v-model="dataTemp.email" :placeholder="$t('form.email')" />
            </el-form-item>

            <el-form-item :label="$t('form.phone')" prop="phone">
              <el-input v-model="dataTemp.phone" :placeholder="$t('form.phone')" />
            </el-form-item>

            <el-form-item :label="$t('form.password')" prop="password">
              <el-input v-model="dataTemp.password" :placeholder="$t('form.password')" />
            </el-form-item>

          </el-col>
          <el-col :span="10" :offset="2">
            <el-form-item :label="$t('form.sex')" prop="sex">
              <el-radio-group v-model="dataTemp.sex">
                <el-radio-button label="1">{{ $t('form.male') }}</el-radio-button>
                <el-radio-button label="0">{{ $t('form.female') }}</el-radio-button>
                <el-radio-button label="2">{{ $t('form.other') }}</el-radio-button>
              </el-radio-group>
            </el-form-item>

            <el-form-item :label="$t('form.birthday')" prop="birthday">
              <el-date-picker v-model="birthday" type="date" :placeholder="$t('form.birthday')"/>
            </el-form-item>


            <el-form-item :label="$t('form.enter_province')" prop="address1">
              <el-input v-model="dataTemp.address1" :placeholder="$t('form.enter_province')" />
            </el-form-item>

            <el-form-item :label="$t('form.enter_district')" prop="address2">
              <el-input v-model="dataTemp.address2" :placeholder="$t('form.enter_district')" />
            </el-form-item>

            <el-form-item :label="$t('form.enter_address')" prop="address3">
              <el-input v-model="dataTemp.address3" :placeholder="$t('form.enter_address')" />
            </el-form-item>

            <el-form-item :label="$t('form.country')" prop="country">
              <el-select v-model="dataTemp.country" placeholder="Select" prop="country" filterable>
                <el-option
                  v-for="(item,index) in CountriesOptions"
                  :key="index"
                  :label="item"
                  :value="index"
                />
              </el-select>
            </el-form-item>

            <el-form-item :label="$t('form.status')" prop="status">
                <el-switch
                  v-model="dataTemp.status"
                  active-color="#13ce66"
                  inactive-color="#ff4949"
                  active-value="1"
                  inactive-value="0"
                />
            </el-form-item>
          </el-col>
        </el-row>

      </el-form>
      <el-row>
        <el-button-group class="pull-right">
            <el-button type="success" @click="isEdit ? updateData() : createData()" icon="el-icon-check">{{ $t('form.done') }}</el-button>
        </el-button-group>
      </el-row>
    </el-col>
    <slot />
  </el-row>
</template>

<script>
import reloadRedirectToList from '@/utils';
import { parseTime } from '@/filters';
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import CustomerResource from '@/api/customer';
import CountryResource from '@/api/country';
const countryResource = new CountryResource();
const customerResource = new CustomerResource();

export default {
  name: 'CustomerDetail',
  components: {
    FileManager
  },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    dataTemp: {
      type: Object,
      default: false,
    },
    dataRules: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      CountriesOptions:[],
      visiblePopover:false,
      birthday:'',
    };
  },
  async created(){
    let {data} = await countryResource.list();
    this.CountriesOptions = data;
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'CustomerList' });
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          this.dataTemp.birthday = parseTime(this.birthday, '{y}-{m}-{d}');
          customerResource.store(this.dataTemp).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              const view = this.$router.resolve({ name: 'CustomerCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);
              reloadRedirectToList('CustomerList');
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          customerResource.update(this.dataTemp.id, this.dataTemp).then((res) => {
            reloadRedirectToList('CustomerList');

            this.$message({
              type: 'success',
              message: 'Updated successfully',
            });

            loading.close();
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
  },
};
</script>
<style type="text/css">
  .cus-el-main-form{
    height: calc(100vh - 250px);
    overflow-y: scroll;
  }
  .cus-el-main-form::-webkit-scrollbar {
      width: 0;
      background: transparent;
  }
  .cus-el-main-form .el-form-item__label{
    line-height: 20px!important;
    padding-bottom: 5px!important;
  }
</style>