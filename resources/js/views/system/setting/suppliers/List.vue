<template>
  <div class="app-container">
    <el-row class="el-main-form" :gutter="20" style="margin:0px;">
      <el-col :span="8">
        <el-skeleton :rows="6" animated :loading="loading" />
        <el-form v-show="!loading" ref="dataForm" :model="dataTemp"  class="form-container" label-width="100px">

          <el-form-item :label="$t('form.store')" prop="store_id" v-if="checkOnlyStore">
            <el-radio v-for="(item,index) in storeList" :key="index" v-model="dataTemp.store_id" :label="index" @change="handleChangeStore(index)">
              {{ item.descriptions_current_lang[0].title }}
            </el-radio>
          </el-form-item>

          <el-form-item :label="$t('form.name')" prop="name">
            <el-input
              v-model="dataTemp.name"
              :placeholder="$t('form.name')"
              clearable/>
          </el-form-item>

          <el-table-column :label="$t('table.email')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.email }}
            </template>
          </el-table-column>

          <el-form-item :label="$t('form.phone')" prop="url">
            <el-input
              v-model="dataTemp.phone"
              :placeholder="$t('form.phone')"
              clearable/>
          </el-form-item>

          <el-form-item :label="$t('form.address')" prop="url">
            <el-input
              v-model="dataTemp.address"
              :placeholder="$t('form.address')"
              clearable/>
          </el-form-item>

          <el-form-item :label="$t('form.url')" prop="url">
            <el-input
              v-model="dataTemp.url"
              :placeholder="$t('form.url')"
              clearable/>
          </el-form-item>

          <el-form-item :label="$t('form.image')">
            <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
          </el-form-item>

          <div class="image-uploading" style="margin-left: 80px;position: relative;margin-bottom: 22px">
            <i v-if="dataTemp.image" class="el-icon-close" @click="resetImageUpload()" style="position: absolute;top: 15px;right: 15px"/>
            <el-image v-if="dataTemp.image" :src="dataTemp.image+'&w=644'">
              <div slot="placeholder" class="image-slot">
                <i class="el-icon-loading" />
              </div>
            </el-image>
          </div>

          <el-form-item :label="$t('form.sort')" prop="sort">
            <el-input type="number" v-model.number="dataTemp.sort" :placeholder="$t('form.sort')" :min="1"/>
          </el-form-item>

          <el-form-item :label="$t('form.status')" prop="status">
            <el-tooltip :content="'Switch value: ' + dataTemp.status" placement="top">
              <el-switch
                v-model="dataTemp.status"
                active-color="#13ce66"
                inactive-color="#ff4949"
                active-value="1"
                inactive-value="0"
              />
            </el-tooltip>

          </el-form-item>

          <el-button-group class="pull-right">
            <el-button type="danger" icon="el-icon-close" @click="handleCancel" v-if="dataTemp.id > 0">
              {{ $t('form.cancel') }}
            </el-button>

            <el-button type="success" icon="el-icon-check" @click="dataTemp.id == 0 ? create() : update()">
              {{ $t('form.done') }}
            </el-button>
          </el-button-group>
        </el-form>
      </el-col>
      <el-col :span="16">   

        <el-input style="margin-bottom:22px ;" :placeholder="$t('table.keyword')" v-model="listQuery.keyword" class="input-with-select" 
          @keyup.enter.native="handleFilter" >
          <el-button slot="append" icon="el-icon-search" @click="handleFilter"></el-button>
        </el-input>

        <el-table
          v-loading="loading"
          :data="list"
          border>

          <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
            <template slot-scope="scope">
              <span>{{ scope.row.id }}</span>
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.store')" min-width="150" v-if="checkOnlyStore">
            <template slot-scope="scope">
              <el-tag type="success">
                <i class="el-icon-s-shop" />
                {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.name')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.name }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.phone')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.phone }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.email')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.email }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.address')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.address }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.sort')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.sort }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.image')" min-width="150" align="center">
            <template slot-scope="scope">
              <el-image :src="scope.row.image+'&w=150'" fit="cover" style="max-height: 150px;">
                <div slot="error" class="image-slot">
                  <i class="el-icon-picture-outline" />
                </div>
              </el-image>
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
            <template slot-scope="{row}">
              <el-tag :type="row.status | statusFilter">
                {{ row.status | statusFilter(true) }}
              </el-tag>
            </template>
          </el-table-column>


          <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="250px" class-name="small-padding fixed-width">
            <template slot-scope="{row}">
              <el-button type="primary" size="mini" icon="el-icon-edit" @click="handleUpdate(row)" />
              <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
            </template>
          </el-table-column>
        </el-table>

        <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
      </el-col>
    </el-row>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; 
import { checkOnlyStore } from '@/utils';
import BrandResource from '@/api/brand';
import EventBus from '@/components/FileManager/eventBus';
import FileManager from '@/components/FileManager';
import SupplierResource from '@/api/supplier';

const supplierResource = new SupplierResource();

const defaultTemp = {
    id:0,
    name:'',
    url:'',
    status:'0',
    phone:'',
    email:'',
    address:'',
    image:'',
    sort:'',
    store_id:'',
}
export default {
  name: 'BrandList',
  components: { Pagination,FileManager },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      componentStorage: '',
      dialogStorageVisible: false,
      dataTemp:Object.assign({},defaultTemp),
      listQuery: {
        page: 1,
        limit: 20,
        keyword:''
      },
    };
  },
  computed: {
    checkOnlyStore,
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  created() {
    this.getList();
    if (this.checkOnlyStore == false) {
      this.dataTemp.store_id = this.$store.state.user.currentStore;
    }
  },
  methods: {
    create(){
      this.loading = true;
      supplierResource.store(this.dataTemp).then((res) => {
        if (res.success) {
          this.dataTemp.id = res.data.id;
          this.list = [this.dataTemp,...this.list];
          ++this.total;
          this.$message({
            type: 'success',
            message: 'Create successfully',
          });
        }
        this.loading = false;
      }).catch(() => {
        this.loading = false;
      })
    },
    update(){
      this.loading = true;
      supplierResource.update(this.dataTemp.id,this.dataTemp).then((res) => {
        if (res.success) {
          const index = this.list.findIndex((item) => item.id == this.dataTemp.id);
          if (index > -1) {
            this.$set(this.list,index,this.dataTemp);
          }
          this.$message({
            type: 'success',
            message: 'Update successfully',
          });
        }
        this.loading = false;
      }).catch(() => {
        this.loading = false;
      })
    },
    handleCancel(){
      this.dataTemp = Object.assign({},defaultTemp);
    },
    handleChangeStore(index){
      this.dataTemp.store = this.storeList[index];
    },
    handleUpdate(row){
      this.dataTemp = Object.assign({},row);
      this.dataTemp.status = String(row.status);
      this.dataTemp.store_id = String(row.store.id);
    },
    async getList() {
      const data = await supplierResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    handleDeleting(row){
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        supplierResource.destroy(row.id).then((res) => {
          if (res) {
            const index = this.list.indexOf(row);
            this.list.splice(index, 1);
            this.$message({
              type: 'success',
              message: 'Delete successfully',
            });
          }
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleFilter() {
      this.loading = true;
      this.listQuery.page = 1;
      this.getList();
    },
    resetImageUpload(){
      this.dataTemp.image = '';
      this.componentStorage = '';
    },
    dialogStorageClose(){
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handlerGeturl(data) {
      if (data) {
        this.dataTemp.image = data;
        this.dialogStorageClose();
      }
    },
    handleVisibleStorage(){
      this.$store.commit('fm/setDisks', 'supplier');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
      EventBus.$on('getFileResponse', this.handlerGeturl);
    },
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
