<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:0px;">
      <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
        <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
      </div>
    </el-row>
    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-form ref="dataForm" :model="temp" class="form-container">
        <el-col :span="12">
          <el-skeleton :rows="6" animated :loading="dataLoading" />
          <div class="block-tables">
            <el-descriptions class="margin-top" :column="1" border>
              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-s-check" />
                  Logo
                </template>
                <div @click="handleVisibleStorage()">
                  <el-image 
                    style="width: 100px;cursor: pointer;"
                    :src="temp.logo"
                    fit="contain">
                  </el-image>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-phone" />
                  Number Phone
                </template>
                <el-popover
                  v-model="visible[0]"
                  placement="top"
                  title="Number phone"
                  width="200"
                >
                  <el-form-item
                    prop="phone"
                    :rules="[
                      { required: true, message: 'Number Phone is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >
                    <el-input v-model="temp.phone" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'phone')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'phone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.phone }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-phone-outline" />
                  Number Phone (Other)
                </template>
                <el-popover
                  v-model="visible[1]"
                  placement="top"
                  title="Number phone"
                  width="200"
                >
                  <el-form-item
                    prop="long_phone"
                    :rules="[
                      { required: true, message: 'Number Phone is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >
                    <el-input v-model="temp.long_phone" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'long_phone')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'long_phone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.long_phone }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="email" />
                  Email address
                </template>
                <el-popover
                  v-model="visible[2]"
                  placement="top"
                  title="Email address"
                  width="200"
                >
                  <el-form-item
                    prop="email"
                    :rules="[
                      { required: true, message: 'Email is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >
                    <el-input v-model="temp.email" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'email')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'email')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.email }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-location" />
                  Address
                </template>
                <el-popover
                  v-model="visible[3]"
                  placement="top"
                  title="Address"
                  width="200"
                >
                  <el-form-item
                    prop="address"
                    :rules="[
                      { required: true, message: 'Address is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >
                    <el-input v-model="temp.address" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'address')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'address')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.address }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="international" />
                  Domain
                </template>
                <el-popover
                  v-model="visible[4]"
                  placement="top"
                  title="Domain"
                  width="200"
                >
                  <el-form-item
                    prop="domain"
                    :rules="[
                      { required: true, message: 'Domain is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >
                    <el-input v-model="temp.domain" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'domain')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'domain')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.domain }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="language" />
                  Default language
                </template>
                <el-popover
                  v-model="visible[5]"
                  placement="top"
                  title="Default language"
                  width="200"
                >
                  <el-form-item
                    prop="language"
                    :rules="[
                      { required: true, message: 'Domain is required'},
                      { max: 100, message: 'Last name max length 100 character'}
                    ]"
                  >

                    <el-select v-model="temp.language" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.languages"
                        :key="index"
                        :label="item.name"
                        :value="index"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'language')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.language }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="dollar" />
                  Default currency
                </template>
                <el-popover
                  v-model="visible[6]"
                  placement="top"
                  title="Default currency"
                  width="200"
                >
                  <el-form-item
                    prop="currency"
                    :rules="[
                      { required: true, message: 'Currency is required'}
                    ]"
                  >
                    <el-select v-model="temp.currency" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.currencies"
                        :key="index"
                        :label="item"
                        :value="index"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'currency')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.currency }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-s-home" />
                  Office
                </template>
                <el-popover
                  v-model="visible[7]"
                  placement="top"
                  title="Office"
                  width="200"
                >
                  <el-form-item
                    prop="office"
                    :rules="[
                      { required: true, message: 'Office is required'}
                    ]"
                  >
                    <el-input v-model="temp.office" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'office')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'office')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.office ? temp.office : 'Empty' }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-box" />
                  Warehouse
                </template>
                <el-popover
                  v-model="visible[8]"
                  placement="top"
                  title="Warehouse"
                  width="200"
                >
                  <el-form-item
                    prop="warehouse"
                    :rules="[
                      { required: true, message: 'Warehouse is required'}
                    ]"
                  >
                    <el-input v-model="temp.warehouse" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'warehouse')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'warehouse')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.warehouse ? temp.warehouse : 'Empty' }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-time" />
                  Timezone
                </template>
                <el-popover
                  v-model="visible[9]"
                  placement="top"
                  title="Timezone"
                  width="200"
                >
                  <el-form-item
                    prop="timezone"
                    :rules="[
                      { required: true, message: 'Timezone is required'}
                    ]"
                  >
                    <el-select v-model="temp.timezone" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.timezones"
                        :key="index"
                        :label="item"
                        :value="item"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'timezone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.timezone ? temp.timezone : 'Empty' }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-menu" />
                  Template
                </template>
                <el-popover
                  v-model="visible[10]"
                  placement="top"
                  title="Template"
                  width="200"
                >
                  <el-form-item
                    prop="template"
                    :rules="[
                      { required: true, message: 'Template is required'}
                    ]"
                  >
                    <el-input v-model="temp.template" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'template')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'template')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.template ? temp.template : 'Empty' }}</span>
                </el-popover>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-time" />
                  Time Working
                </template>
                <el-popover
                  v-model="visible[11]"
                  placement="top"
                  title="Time Working"
                  width="376"
                >
                  <el-form-item
                    prop="time_active"
                    :rules="[
                      { required: true, message: 'Time Working is required'}
                    ]"
                  >
                    <el-time-picker
                      is-range
                      v-model="temp.time_active"
                      range-separator="To"
                      start-placeholder="Start time"
                      end-placeholder="End time">
                    </el-time-picker>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'time_active')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.time_active ? temp.time_active : 'Empty' }}</span>
                </el-popover>
              </el-descriptions-item>

            </el-descriptions>
          </div>
        </el-col>

        <el-col :span="12">
          <el-skeleton :rows="6" animated :loading="dataLoading" />
          <el-descriptions class="margin-top" :column="1" border>
              
              <el-descriptions-item :content-style="{'text-align': 'left'}" :column="2">
                <template slot="label">
                  <i class="el-icon-s-marketing" />
                  App Name
                </template>
                <div v-for="item in temp.descriptions">
                  <svg-icon :icon-class="'flag-'+item.lang" style="width:2em"/>
                  <el-popover
                    v-model="item.title.visible"
                    placement="top"
                    title="Number phone"
                    width="200"
                  >
                    <el-form-item
                      prop="long_phone"
                      :rules="[
                        { required: true, message: 'Number Phone is required'},
                        { max: 100, message: 'Last name max length 100 character'}
                      ]"
                    >
                      <el-input v-model="item.title.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'long_phone')" />
                    </el-form-item>
                    <div style="text-align: right; margin: 12px 0px 0px 0px">
                      <el-button-group>
                        <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                        <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'long_phone')">Confirm</el-button>
                      </el-button-group>
                    </div>
                    <span slot="reference" class="border-edit">{{ item.title.value ? item.title.value : 'Empty' }}</span>
                  </el-popover>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'left'}">
                <template slot="label">
                  <i class="el-icon-key" />
                  Keyword
                </template>
                <div v-for="item in temp.descriptions">
                  <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" />
                  <el-popover
                    v-model="item.keyword.visible"
                    placement="top"
                    title="Email address"
                    width="200"
                  >
                    <el-form-item
                      prop="email"
                      :rules="[
                        { required: true, message: 'Email is required'},
                        { max: 100, message: 'Last name max length 100 character'}
                      ]"
                    >
                      <el-input v-model="item.keyword.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'email')" />
                    </el-form-item>
                    <div style="text-align: right; margin: 12px 0px 0px 0px">
                      <el-button-group>
                        <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                        <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'email')">Confirm</el-button>
                      </el-button-group>
                    </div>
                    <span slot="reference" class="border-edit">{{ item.keyword.value ? item.keyword.value  : 'Empty'}}</span>
                  </el-popover>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'left'}">
                <template slot="label">
                  <i class="el-icon-document-copy" />
                  Description
                </template>
                <div v-for="item in temp.descriptions">
                  <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" />
                  <el-popover
                    v-model="item.description.visible"
                    placement="top"
                    title="Address"
                    width="200"
                  >
                    <el-form-item
                      prop="address"
                      :rules="[
                        { required: true, message: 'Address is required'},
                        { max: 100, message: 'Last name max length 100 character'}
                      ]"
                    >
                      <el-input v-model="item.description.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'address')" />
                    </el-form-item>
                    <div style="text-align: right; margin: 12px 0px 0px 0px">
                      <el-button-group>
                        <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                        <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'address')">Confirm</el-button>
                      </el-button-group>
                    </div>
                    <span slot="reference" class="border-edit">{{ item.description.value ? item.description.value : 'Empty' }}</span>
                  </el-popover>
                </div>
              </el-descriptions-item>

          </el-descriptions>
        </el-col>
      </el-form>
    </el-row>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>


import StoreResource from '@/api/store';
import EventBus from '@/components/FileManager/eventBus';
import FileManager from '@/components/FileManager';

const storeResource = new StoreResource();

export default {
  name: 'StoreInfo',
  components:{FileManager},
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    dataLoading: {
      type: Boolean,
      default: true,
    },
    dataInfo: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      visible: {},
      btnLoading: false,
      temp: {},
      componentStorage: '',
      dialogStorageVisible:false,
      cancelAction:false,
    };
  },
  created() {
    this.temp = Object.assign({}, this.dataInfo);

    for (var i = 0; i <=11; i++) {
      this.visible[i] = false;
    }
    this.temp.descriptions.forEach(function(v,i) {
      v.description = {value:v.description,visible:false};
      v.title = {value:v.title,visible:false};
      v.keyword = {value:v.keyword,visible:false};
    })
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'StoreList' });
    },
    handleVisibleStorage(index, key){
      EventBus.$on('getFileResponse', this.handlerGeturl);
      this.$store.commit('fm/setDisks', 'store');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
      EventBus.$off('getFileResponse');
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handlerGeturl(data) {
      this.temp.logo = data[0];
      this.dialogStorageClose();
    },
    handleConfirm(i, key){
      this.cancelAction = false;
      this.$refs['dataForm'].validateField(key, this._checkValidate);
      if (this.cancelAction) {
        return false;
      }
      const id = this.$route.params.id;
      const params = {
        name: key,
        value: this.temp[key],
      };

      this.btnLoading = true;
     
    },
    handleCancel(i){
      this.visible[i] = false;
    },
    _checkValidate(msg){
      if (msg != '' && msg != undefined) {
        this.cancelAction = true;
      }
    },
  }
};
</script>
<style type="text/css">

  .block-tables{
    padding: 24px;
    border: 1px solid #ebebeb;
    border-radius: 3px;
    transition: .2s;
  }
  .el-main-form{
    height: calc(100vh - 130px);
    overflow-y: scroll;
  }
  .border-edit {
    border-bottom: 1px dotted #606266;
      color: #1890ff;
      cursor: pointer;
  }
</style>