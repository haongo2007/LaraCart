<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-page
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
      <el-table-column align="center" :label="$t('table.id')" width="50">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.language')" min-width="100px">
        <template slot-scope="scope">
          <el-image :src="'/data/language/flag_'+scope.row.lang+'.png'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.title')">
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.alias')">
        <template slot-scope="scope">
          <span>{{ scope.row.alias }}</span>
        </template>
      </el-table-column>
      
      <el-table-column :label="$t('table.store')" min-width="100" v-if="checkOnlyStore">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop"></i>
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.image')" min-width="150">
        <template slot-scope="scope">
            <el-popover
              placement="right-end"
              width="200"
              trigger="hover">
                <div v-bind:style="{ backgroundImage: 'url(' + scope.row.image + ')' }" class="screen-shot"></div>
              <el-button slot="reference">Mouse on</el-button>
            </el-popover>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="80" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" 
            @click="$router.push({ name: 'PageEdit',params:{id:row.id,lang:row.lang} })" v-permission="['edit.page']" />
            <el-button type="danger" size="mini" icon="el-icon-delete" v-permission="['delete.page']" @click="handleDeleting(row)" />
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
import FilterSystemPage from './components/FilterSystemPage';
import EventBus from '@/components/FileManager/eventBus';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import { checkOnlyStore } from '@/utils';

export default {
  name: 'PageList',
  components: { Pagination,FilterSystemPage,RightPanel },
  directives: { permission },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 15,
        keyword: '',
        lang: '',
      },
    }
  },
  computed: {
    checkOnlyStore,
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
.screen-shot{
  min-height: 16rem;
  background-repeat: no-repeat;
  background-size: cover;
  display: block;
  background-color: #f4f4f4;
  border: .1rem solid #ebebeb;
  margin-bottom: 1.2rem;
  padding-top: 80.95%;
  background-position: top;
  transition: background-position .6s linear,box-shadow .3s;
  &:hover {
    box-shadow: 3px 10px 16px rgb(51 51 51 / 5%), -3px 10px 16px rgb(51 51 51 / 5%);
    background-position: bottom;
    transition: background-position 2s linear,box-shadow .3s;
  }
}
</style>
