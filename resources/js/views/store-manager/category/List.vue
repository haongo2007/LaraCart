<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-category
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
      row-key="id"
      border
      lazy
      :load="load"
      :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
    >
      <el-table-column :label="labelChildOrParent" align="center" min-width="100">
        <template v-if="row.hasChildren" slot-scope="{row}" />
        <template v-if="!row.hasChildren" slot-scope="{row}">
          <el-tag v-if="row.parent === 0" type="danger">ROOT</el-tag>
          <el-tag v-else type="success">
            {{ row.parent }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Store" min-width="150" v-if="checkOnlyStore">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop"></i>
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>
      
      <el-table-column :label="$t('table.name')" min-width="150px">
        <template slot-scope="scope">
          <el-tag>{{ scope.row.name }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.image')" min-width="100px" prop="image">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=100'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>
      <el-table-column :label="$t('sort')" width="110px" align="center" prop="sort">
        <template slot-scope="scope">
          <span style="color:red;">{{ scope.row.sort }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.active')" width="110px" align="center" prop="top">
        <template slot-scope="{row}">
          <el-tag :type="row.top | statusFilter">
            {{ row.top | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" width="200" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <router-link :to="{ name: 'CategoryEdit',params:{id:row.id} }">
            <el-button type="primary" size="mini" icon="el-icon-edit">
              {{ $t('table.edit') }}
            </el-button>
          </router-link>
          <el-button size="mini" type="danger" @click="handleDeleting(row)">
            {{ $t('table.delete') }}
          </el-button>
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
import FilterSystemCategory from './components/FilterSystemCategory';
import EventBus from '@/components/FileManager/eventBus';
import CategoryResource from '@/api/category';
import { checkOnlyStore } from '@/utils';

const categoryResource = new CategoryResource();

export default {
  name: 'CategoryList',
  components: { Pagination, RightPanel, FilterSystemCategory },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      parent: true,
      listQuery: {
        page: 1,
        limit: 20,
        status: ['1'],
        name: '',
        type: '',
        sort: 'id__desc',
        parent: '0',
      },
    };
  },
  computed: {
    labelChildOrParent(){
      return (this.listQuery.parent === false ? this.$t('table.parent') : this.$t('table.children'));
    },
    checkOnlyStore
  },
  created() {
  },
  methods: {
    async load(row, treeNode, resolve) {
      const id = row.id;
      const { data } = await categoryResource.getChildren({id:id,store_id:row.store.id});
      resolve(data);
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
    },
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
