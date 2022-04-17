<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-users
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
      
      <el-table-column align="center" label="Store" max-width="150">
        <template slot-scope="scope">
            <el-popover
              placement="right-end"
              width="200"
              trigger="click">
              <el-table :data="scope.row.store">
                <el-table-column label="Name" >
                  <template slot-scope="scope">
                    {{ scope.row.descriptions[0].title }}
                  </template>
                </el-table-column>
              </el-table>
              <el-button slot="reference">Click to view</el-button>
            </el-popover>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Avatar" min-width="80">
        <template slot-scope="scope">
          <el-avatar icon="el-icon-user-solid" :src="scope.row.avatar"></el-avatar>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Name">
        <template slot-scope="scope">
          <span>{{ scope.row.fullname }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Phone">
        <template slot-scope="scope">
          <span>{{ scope.row.phone }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Email">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Role" width="150">
        <template slot-scope="scope">
          <el-popover
            placement="left-end"
            width="200"
            trigger="click">
            <el-table :data="scope.row.roles">
              <el-table-column label="Name" >
                <template slot-scope="scope">
                  <div v-html="scope.row.name"></div>
                </template>
              </el-table-column>
            </el-table>
            <el-button slot="reference">Click to view</el-button>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Permissions" width="150">
        <template slot-scope="scope">
          <el-popover
            placement="left-end"
            width="200"
            trigger="click">
            <el-table :data="scope.row.permissions">
              <el-table-column label="Name" >
                <template slot-scope="scope">
                  <div v-html="scope.row.name"></div>
                </template>
              </el-table-column>
            </el-table>
            <el-button slot="reference">Click to view</el-button>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="80" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="$router.push({ name: 'UserEdit',params:{id:row.id} })" />
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import UserResource from '@/api/user';
import RightPanel from '@/components/RightPanel';
import FilterSystemUsers from './components/FilterSystemUsers';
import EventBus from '@/components/FileManager/eventBus';

export default {
  name: 'UsersList',
  components: { Pagination,FilterSystemUsers,RightPanel },
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
    }
  },
  methods:{
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
    }
  }
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
