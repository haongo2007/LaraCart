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
      <el-table-column :label="$t('table.flag')" min-width="100px">
        <template slot-scope="scope">
          <el-image :src="scope.row.icon">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.rtl')" min-width="100px" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.rtl | statusFilter">
            {{ row.rtl | statusFilter('name') }}
          </el-tag>
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
import LanguageResource from '@/api/languages';

const languageResource = new LanguageResource();


export default {
  name: 'LanguagesList',
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
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    async getList() {
      const data = await languageResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
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
