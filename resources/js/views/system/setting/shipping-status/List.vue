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
              clearable
            />
          </el-form-item>

          <el-form-item :label="$t('form.label')" prop="group">
            <el-select v-model="dataTemp.label" :placeholder="$t('form.label')" clearable style="width: 100%" class="filter-item">
              <el-option v-for="(item,index) in labelList" :key="index" :value="item.value">
                <el-tag :type="item.value" effect="dark">
                  {{ item.name }}
                </el-tag>
              </el-option>
            </el-select>
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
      <el-col :span="2">
      </el-col>
      <el-col :span="12" :offset="2">
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

          <el-table-column :label="$t('table.label')" min-width="120px">
            <template slot-scope="scope">
              <el-tag :type="scope.row.label" effect="dark">
                {{ scope.row.label == '' ? 'primary' : scope.row.label }}
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
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; 
import { checkOnlyStore } from '@/utils';
import ShippingStatusResource from '@/api/shipping-status';

const shippingStatusResource = new ShippingStatusResource();

const defaultTemp = {
    id:0,
    name:'',
    label:'',
    store_id:'',
}
export default {
  name: 'ShippingStatusList',
  components: { Pagination },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      labelList:[{
        name: 'Primary',
        value:''
      },{
        name: 'Info',
        value:'info'
      },{
        name: 'Success',
        value:'success'
      },{
        name: 'Warning',
        value:'warning'
      },{
        name: 'Danger',
        value:'danger'
      }],
      dataTemp:Object.assign({},defaultTemp),
      listQuery: {
        page: 1,
        limit: 20,
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
      shippingStatusResource.store(this.dataTemp).then((res) => {
        if (res.success) {
          this.dataTemp.id = res.data.id;
          this.list = [this.dataTemp,...this.list];
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
      shippingStatusResource.update(this.dataTemp.id,this.dataTemp).then((res) => {
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
      this.dataTemp.store_id = String(row.store.id);
    },
    async getList() {
      const data = await shippingStatusResource.list();
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
        shippingStatusResource.destroy(row.id).then((res) => {
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
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
