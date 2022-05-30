<template>
  <div class="app-container">
     <div class="filter-container">  
        <el-button type="danger" icon="el-icon-delete" class="filter-item" @click="handlerDeleteAll" :disabled="multiSelectRow.length == 0 ? true : false" />
    </div>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%"
      @selection-change="handleSelectionAllChange"
      border>
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
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

      <el-table-column :label="$t('table.method')" min-width="70px">
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

      <el-table-column align="center" :label="$t('table.agent')" min-width="120">
        <template slot-scope="scope">
          <el-popover
            placement="left-end"
            width="400"
            trigger="hover">
            <div>{{ scope.row.user_agent }}</div>
            <el-button slot="reference">{{ $t('table.detail') }}</el-button>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.input')" min-width="120">
        <template slot-scope="scope">
          <el-popover
            placement="left-end"
            width="400"
            trigger="hover">
            <div>{{ JSON.parse(scope.row.input) }}</div>
            <el-button slot="reference">{{ $t('table.detail') }}</el-button>
          </el-popover>
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
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination';
import OperationLogsResource from '@/api/operation-logs';
import reloadRedirectToList from '@/utils';

const operationLogsResource = new OperationLogsResource();

export default {
  name: 'OperationLogsList',
  components: { Pagination},
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 20,
        sort: 'id__desc',
      },
      multiSelectRow: [],
    };
  },
  created() {
    this.getList();
  },
  methods: {
    handleSelectionAllChange(data){
      this.multiSelectRow = data;
    },
    async getList() {
      const data = await operationLogsResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    paginationInit(data){
      this.loading = true;
      this.getList(this.listQuery);
    },
    handlerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDeleting(row, multiple = false) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        if (multiple) {
          var id = [];
          row.map((item) => id.push(item.id));
        } else {
          var id = row.id;
        }
        var that = this;
        operationLogsResource.destroy(id).then((res) => {
          if (res) {
            if(multiple){
              reloadRedirectToList('OperationLogsList');
            }else{
              const index = that.list.indexOf(row);
              that.list.splice(index, 1);
            }
            this.$message({
              type: 'success',
              message: 'Delete successfully',
            });
          }
        });
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
