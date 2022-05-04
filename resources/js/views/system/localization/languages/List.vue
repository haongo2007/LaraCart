<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-languages
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
      border
    >

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Store" min-width="150">
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
      <el-table-column :label="$t('table.icon')" min-width="100px">
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

      <el-table-column :label="$t('sort')" min-width="120px" align="center">
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
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import RightPanel from '@/components/RightPanel';
import FilterSystemLanguages from './components/FilterSystemLanguages';
import EventBus from '@/components/FileManager/eventBus';
import StoreResource from '@/api/store';

const storeResource = new StoreResource();

export default {
  name: 'LanguagesList',
  components: { Pagination, RightPanel, FilterSystemLanguages },
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
    handleChange(id, val, key){
      const params = {};
      params[key] = val;
      if (val == 0) {
        this.$confirm('this action will put your app into maintenance mode, are you sure ?', 'Warning', {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }).then(() => {
          this.updateData(id, params);
        }).catch(err => {
          const row = this.list.findIndex((item) => item.id == id);
          this.list[row].active = 1;
        });
      } else {
        this.updateData(id, params);
      }
    },
    updateData(id, params){
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
    },
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
