<template>
  <div class="app-container">
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
            <el-button slot="reference">Detail</el-button>
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
            <el-button slot="reference">Detail</el-button>
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
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const data = await operationLogsResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    handleSelectionAllChange(val){

    },
    paginationInit(data){
      this.loading = true;
      this.getList(this.listQuery);
    },
    handleDeleting(row){

    }
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
