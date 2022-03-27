<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-store
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
      <el-table-column :label="$t('table.domain')" min-width="80px">
        <template slot-scope="scope">
          {{ scope.row.domain }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.template')" min-width="80px" prop="template">
        <template slot-scope="scope">
          {{ scope.row.template }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.logo')" min-width="80px" prop="logo">
        <template slot-scope="scope">
          <el-image :src="scope.row.logo+'&w=260'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>
      <el-table-column :label="$t('phone')" min-width="70px" align="center" prop="phone">
        <template slot-scope="scope">
          <span>{{ scope.row.phone }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('email')" min-width="120px" align="center" prop="email">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('address')" min-width="120px" align="center" prop="address">
        <template slot-scope="scope">
          <span>{{ scope.row.address }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('currency')" min-width="60px" align="center" prop="currency">
        <template slot-scope="scope">
          <span>{{ scope.row.currency }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" min-width="60px" align="center" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.active')" class-name="status-col" min-width="70px" prop="active">
        <template slot-scope="{row}">
          <el-switch
            v-model="row.active"
            :active-value="1"
            active-color="#13ce66"
            :inactive-value="0"
            inactive-color="#ff4949">
          </el-switch>  
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" min-width="100px" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <router-link :to="{ name: 'StoreEdit',params:{id:row.id} }">
            <el-button type="primary" size="mini" icon="el-icon-edit"></el-button>
          </router-link>
          <router-link :to="{ name: 'StoreConfig',params:{id:row.id} }">
            <el-button type="primary" size="mini" icon="el-icon-s-tools"></el-button>
          </router-link>
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
import FilterSystemStore from './components/FilterSystemStore';
import EventBus from '@/components/FileManager/eventBus';


export default {
  name: 'StoreList',
  components: { Pagination, RightPanel,FilterSystemStore },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 20,
        status: [],
        active: [],
        sort: 'id__desc',
        contain: '',
      },
    };
  },
  created() {
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
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
