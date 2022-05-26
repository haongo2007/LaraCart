<template>
  <div class="app-container">
    <el-row class="el-main-form" :gutter="20" style="margin:0px;">
      <el-col :span="12">
        <el-skeleton :rows="6" animated :loading="loading" />
        <el-form v-show="!loading" ref="dataForm" :model="dataTemp"  class="form-container" label-width="200px">

          <el-form-item :label="$t('table.name')" prop="fullname">
            <el-input
              v-model="dataTemp.name"
              placeholder="Please input"
              clearable
            />
          </el-form-item>

          <el-button class="pull-right" type="success" icon="el-icon-check" >
            Done
          </el-button>
        </el-form>
      </el-col>
      <el-col :span="12">
        <el-table
          v-loading="loading"
          :data="list"
          border>

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

          <el-table-column :label="$t('table.name')" min-width="70px">
            <template slot-scope="scope">
              {{ scope.row.name }}
            </template>
          </el-table-column>

          <el-table-column :label="$t('table.description')" min-width="120px">
            <template slot-scope="scope">
              {{ scope.row.description }}
            </template>
          </el-table-column>

          <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="150px" class-name="small-padding fixed-width">
            <template slot-scope="{row}">
              <router-link :to="{ name: 'StoreEdit',params:{id:row.id} }">
                <el-button type="primary" size="mini" icon="el-icon-edit" />
              </router-link>
              <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
            </template>
          </el-table-column>
        </el-table>

        <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; 
import EventBus from '@/components/FileManager/eventBus';
import { checkOnlyStore } from '@/utils';
import LengthResource from '@/api/length';

const lengthResource = new LengthResource();


export default {
  name: 'BrandList',
  components: { Pagination },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      dataTemp:{
        name:'',
      },
      listQuery: {
        page: 1,
        limit: 20,
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
      const data = await lengthResource.list();
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
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
