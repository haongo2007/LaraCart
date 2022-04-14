<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-permissions
          :data-loading="loading"
          :data-query="listQuery"
          @handleListenData="handleListenData"
        />
      </right-panel>
    </div>
    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%" @selection-change="handleSelectionAllChange">
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column align="center" label="ID" width="50">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Slug" max-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.slug }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Name" max-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column label="HTTP path" max-width="150">
        <template slot-scope="scope">
          <el-popover
            placement="left-end"
            width="400"
            trigger="click">
            <el-table :data="scope.row.http_uri | http_pathFilter">
              <el-table-column label="Path" >
                <template slot-scope="scope">
                  <div v-html="scope.row.uri"></div>
                </template>
              </el-table-column>
            </el-table>
            <el-button slot="reference">Click to view</el-button>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Created at" max-width="150">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import RightPanel from '@/components/RightPanel';
import FilterSystemPermissions from './components/FilterSystemPermissions';
import EventBus from '@/components/FileManager/eventBus';

export default {
  name: 'RolesList',
  components: { Pagination, RightPanel, FilterSystemPermissions },
  filters: {
    http_pathFilter(data) {
      let arr = data.split(',');
      let res = [];
      arr.forEach(function(v,i){
        let cut = v.split('::');
        let type = '';
        if (cut[0].toLowerCase() == 'post') {
          type = 'el-tag--success';
        }else if(cut[0].toLowerCase() == 'put'){
          type = 'el-tag--warning';
        }else if(cut[0].toLowerCase() == 'delete'){
          type = 'el-tag--danger';
        }
        v = '<span class="el-tag '+type+' el-tag--dark el-tag--mini" >'+cut[0]+'</span>&nbsp;'+cut[1];
        res[i] = {uri:v};
      });
      return res;
    },
  },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 15,
        contain: '',
      },
    };
  },
  methods: {
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
