<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-banner
          :data-loading="loading"
          :data-query="listQuery"
          :data-loading-button-create="loadingButtonCreate"
          @handleListenData="handleListenData"
          @handleListenCreateForm="CreateForm"
        />
      </right-panel>
    </div>
    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%" @selection-change="handleSelectionAllChange">
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column align="center" :label="$t('table.id')" width="50">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column v-if="checkOnlyStore" :label="$t('table.store')" min-width="150">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop" />
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.image')" min-width="300">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=300'" fit="cover" style="max-height: 150px;">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.title')" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.url')" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.url }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.target')">
        <template slot-scope="scope">
          <span>{{ scope.row.target }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.clicked')" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.click }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.type')" prop="type" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.type }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button
              v-permission="['edit.banner']"
              :loading="loadingButtonUpdate"
              type="primary"
              size="mini"
              icon="el-icon-edit"
              class="filter-item"
              @click="UpdateForm(row)"
            />
            <el-button v-permission="['delete.banner']" type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
    <el-dialog :title="$t('form.'+dialogStatus)" :visible.sync="dialogFormVisible" :before-close="handleReset" class="dialog-custom">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="80px" style="width: 75%; margin:0 auto;">

        <el-form-item :label="$t('form.title')" prop="title">
          <el-input v-model="temp.title" />
        </el-form-item>

        <el-form-item :label="$t('form.url')" prop="url">
          <el-input v-model="temp.url" />
        </el-form-item>

        <el-form-item :label="$t('form.type')" prop="type">
          <el-select v-model="temp.type" filterable placeholder="Select">
            <el-option
              v-for="item in listBannerType"
              :key="item.id"
              :label="item.name"
              :value="item.code"
            />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.target')" prop="target">
          <el-select v-model="temp.target" filterable placeholder="Select">
            <el-option label="_self" value="_self" />
            <el-option label="_blank" value="_blank" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.image')">
          <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
        </el-form-item>
        <div class="image-uploading" style="margin-left: 80px;position: relative;margin-bottom: 22px">
          <i v-if="temp.image" class="el-icon-close" style="position: absolute;top: 15px;right: 15px" @click="resetImageUpload()" />
          <el-image v-if="temp.image" :src="temp.image+'&w=644'">
            <div slot="placeholder" class="image-slot">
              <i class="el-icon-loading" />
            </div>
          </el-image>
        </div>

        <el-form-item :label="$t('form.html')">
          <json-editor ref="jsonEditor" v-model="temp.html" type="html" />
        </el-form-item>

        <el-form-item :label="$t('form.sort')" prop="sort">
          <el-input v-model.number="temp.sort" type="number" :placeholder="$t('form.sort')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.status')">
          <el-switch
            v-model="temp.status"
            active-color="#13ce66"
            inactive-color="#ff4949"
            active-value="1"
            inactive-value="0"
          />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('form.cancel') }}
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">
          {{ $t('form.confirm') }}
        </el-button>
      </div>
    </el-dialog>
    <el-dialog
      :show-close="false"
      :title="$t('form.choose_store_for_banner')"
      :visible.sync="confirmStoreDialog"
      :before-close="handleConfirm"
      width="30%"
    >
      <div>
        <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index">
          {{ item.descriptions_current_lang[0].title }}
        </el-radio>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" :disabled="temp.store_id == 0" @click="confirmChooseStore">Confirm</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import UserResource from '@/api/user';
import RightPanel from '@/components/RightPanel';
import FilterSystemBanner from './components/FilterSystemBanner';
import EventBus from '@/components/FileManager/eventBus';
import FileManager from '@/components/FileManager';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import BannerTypeResource from '@/api/banner-type';
import Cookies from 'js-cookie';
import JsonEditor from '@/components/JsonEditor';
import BannerResource from '@/api/banner';
import reloadRedirectToList from '@/utils';
import { checkOnlyStore } from '@/utils';

const bannerResource = new BannerResource();

const dataForm = {
  id: 0,
  store_id: 0,
  title: '',
  url: '',
  sort: 1,
  type: '',
  target: '',
  status: '0',
  image: '',
  html: '',
};
const bannerTypeResource = new BannerTypeResource();
export default {
  name: 'BannerList',
  components: { Pagination, FilterSystemBanner, RightPanel, FileManager, JsonEditor },
  directives: { permission },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
      },
      rules: {
      },
      temp: Object.assign({}, dataForm),
      dialogStatus: '',
      dialogFormVisible: false,
      confirmStoreDialog: false,
      listBannerType: [],
      componentStorage: '',
      dialogStorageVisible: false,
      loadingButtonCreate: false,
      loadingButtonUpdate: false,
    };
  },
  computed: {
    checkOnlyStore,
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  methods: {
    handleConfirm(done){
      if (this.temp.store_id > 0) {
        this.CreateForm();
        done();
      }
    },
    handleReset(done){
      this.temp = Object.assign({}, dataForm);
      done();
    },
    confirmChooseStore(){
      this.confirmStoreDialog = false;
      this.CreateForm();
    },
    handleListenData(data){
      if (data.hasOwnProperty('list')) {
        this.list = data.list;
      }
      if (data.hasOwnProperty('loading')) {
        this.loading = data.loading;
      }
      if (data.hasOwnProperty('total')) {
        this.total = data.total;
      }
      if (data.hasOwnProperty('listQuery')) {
        this.listQuery = data.listQuery;
      }
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    handleSelectionAllChange(val){
      EventBus.$emit('listenMultiSelectRow', val);
    },
    handleDeleting(row){
      EventBus.$emit('handleDeleting', row);
    },
    resetTemp() {
      this.temp = Object.assign({}, dataForm);
    },
    handleVisibleStorage(){
      this.$store.commit('fm/setDisks', 'banner');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
      EventBus.$on('getFileResponse', this.handlerGeturl);
    },
    dialogStorageClose(){
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handlerGeturl(data) {
      if (data) {
        this.temp.image = data;
        this.dialogStorageClose();
      }
    },
    resetImageUpload(){
      this.temp.image = '';
      this.componentStorage = '';
    },
    async CreateForm(){
      this.loadingButtonCreate = true;
      let store_ck = Cookies.get('store');
      if (store_ck) {
        store_ck = JSON.parse(store_ck);
      }
      if (store_ck && store_ck.length == 1) {
        this.temp.store_id = store_ck[0];
      } else {
        if (this.temp.store_id == 0) {
          this.confirmStoreDialog = true;
          return false;
        }
      }
      // reset form add;
      const { data } = await bannerTypeResource.list({ store_id: this.temp.store_id });
      this.loadingButtonCreate = false;
      this.listBannerType = data;
      this.dialogStatus = 'create';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    createData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog',
          });
          bannerResource.store(this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              reloadRedirectToList('BannerList');
              this.handleReset();
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
          }).catch(err => {
            loading.close();
          });
        }
      });
    },
    async UpdateForm(row){
      this.loadingButtonUpdate = true;
      this.temp = Object.assign(this.temp, row);
      this.temp.status = String(this.temp.status);
      this.temp.store_id = row.store.id;
      const { data } = await bannerTypeResource.list({ store_id: this.temp.store_id });
      this.listBannerType = data;
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.loadingButtonUpdate = false;
    },
    updateData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog__body',
          });
          bannerResource.update(this.temp.id, this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Update successfully',
              });
              reloadRedirectToList('BannerList');
              this.handleReset();
            } else {
              this.$message({
                type: 'error',
                message: 'Update failed',
              });
              loading.close();
            }
          }).catch(err => {
            loading.close();
          });
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
