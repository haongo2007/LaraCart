<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:0px;">
      <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
        <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
      </div>
    </el-row>
    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-col :span="12" class="toPrint">
        <el-skeleton :rows="6" animated :loading="dataLoading" />
        <div class="block-tables">
          <el-form ref="dataForm" :model="temp" class="form-container">
            <el-descriptions class="margin-top" :column="1" border>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                  <i class="el-icon-user" />
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
                    <el-input v-model="temp.language" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'language')" />
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

            </el-descriptions>
          </el-form>
        </div>
      </el-col>
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
  name: 'ShopInfo',
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
      dialogStorageVisible:false
    };
  },
  created() {
    let id = this.dataInfo.id;
    let data = storeResource.getConfig(id);
    this.temp = Object.assign({}, this.dataInfo);
    Object.keys(this.temp).forEach(function(key) {
      that.visible[key] = false;
    });
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'ShopList' });
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