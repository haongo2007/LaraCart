<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-operation-logs  
          :data-loading="loading"
          :data-query="listQuery"
          @handleListenData="handleListenData"
        />
      </right-panel>
    </div>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%"
      border>

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.user')" min-width="120px">
        <template slot-scope="scope">
          {{ scope.row.user.fullname }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.method')" min-width="50px">
        <template slot-scope="scope">
          {{ scope.row.method }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.path')" min-width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.path }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.ip')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.ip }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.agent')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.agent }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.input')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.input }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.created_at')" min-width="120px" align="center">
        <template slot-scope="scope" v-if="scope.row.created_at">
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="100px" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)"></el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import { statusFilter } from '@/filters';
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import RightPanel from '@/components/RightPanel';
import FilterSystemOperationLogs from './components/FilterSystemOperationLogs';
import EventBus from '@/components/FileManager/eventBus';
import StoreResource from '@/api/store';

const storeResource = new StoreResource();

export default {
  name: 'OperationLogsList',
  components: { Pagination, RightPanel,FilterSystemOperationLogs },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 20,
        status: [],
        sort: 'id__desc',
        name: '',
      },
    };
  },
  created() {
  },
  methods: {
    handleChange(id,val,key){
      let params = {};
      params[key] = val;
      if (val == 0) {
        this.$confirm('this action will put your app into maintenance mode, are you sure ?', 'Warning', {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }).then(() => {
          this.updateData(id,params);
        }).catch(err => {
          let row = this.list.findIndex((item) => item.id == id);
          this.list[row].active = 1;
        });
      }else{        
        this.updateData(id,params);
      }
    },
    updateData(id,params){
      storeResource.update(id, params).then((res) => {
        if (res) {
          this.$message({
            type: 'success',
            message: 'Update successfully',
          });
        } else {
          this.$message({
            type: 'error',
            message: 'Update failed',
          });
        }
      }).catch(err => {
         console.log(err);
      });
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
    handleDeleting(row){
      EventBus.$emit('handleDeleting', row);
    }
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
