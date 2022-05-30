<template>
  <div class="app-container">
    <div class="filter-container">  
        <el-button type="primary" icon="el-icon-plus" class="filter-item" @click="createForm()" />
    </div>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%"
      border
    >

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Store" min-width="150" v-if="checkOnlyStore">
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

      <el-table-column :label="$t('table.code')" min-width="150px">
        <template slot-scope="scope">
          {{ scope.row.code }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.symbol')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.symbol }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.exchange_rate')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.exchange_rate }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.precision')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.precision }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.symbol_first')" min-width="130px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.symbol_first }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.thousands_symbol')" min-width="180px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.thousands }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sort')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.sort }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" min-width="100px" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="250px" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <router-link :to="{ name: 'StoreEdit',params:{id:row.id} }">
            <el-button type="primary" size="mini" icon="el-icon-edit" />
          </router-link>
          <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import { statusFilter } from '@/filters';
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; 
import EventBus from '@/components/FileManager/eventBus';
import { checkOnlyStore } from '@/utils';
import CurrencyResource from '@/api/currency';

const currencyResource = new CurrencyResource();

export default {
  name: 'CurrencyList',
  components: { Pagination },
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
  computed: {
    checkOnlyStore
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const data = await currencyResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    createForm(){

    }
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
