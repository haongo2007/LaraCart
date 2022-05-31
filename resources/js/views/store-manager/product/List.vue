<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-product
          :data-loading="loading"
          :data-query="listQuery"
          @handleListenData="handleListenData"
        />
      </right-panel>
    </div>

    <el-table
      v-loading="loading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
      @selection-change="handleSelectionAllChange">
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column :label="$t('table.id')" min-width="50" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.store')" min-width="150" v-if="checkOnlyStore">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop"></i>
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.image')" min-width="150" align="center">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=150'" fit="cover" style="max-height: 150px;">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sku')" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.sku }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.name')" min-width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.name }}
        </template>
      </el-table-column>

      
      <el-table-column align="center" :label="$t('table.category')" min-width="150">
        <template slot-scope="scope">
            <el-popover
              placement="right-end"
              width="200"
              trigger="click">
              <el-table :data="scope.row.categories">
                <el-table-column :label="$t('table.name')" >
                  <template slot-scope="scope">
                    <el-tag> {{ scope.row.descriptions_with_lang_default.title }} </el-tag>
                  </template>
                </el-table-column>
              </el-table>
              <el-button slot="reference">Click to view</el-button>
            </el-popover>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.cost')" min-width="100">
        <template slot-scope="scope">
          {{ scope.row && scope.row.cost | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.price')" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.price | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.type')" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.property }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.kind')" min-min-width="150" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.kind | kindFilter">
            {{ row.kind | kindFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.created_at')" min-width="195" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="renderRouterEdit(row.kind,row.id)" 
            v-permission="['edit'+renderKind(row.kind)+'product']"/>
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" 
            v-permission="['delete'+renderKind(row.kind)+'product']"/>
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import RightPanel from '@/components/RightPanel';
import Pagination from '@/components/Pagination';
import FilterSystemProduct from './components/FilterSystemProduct';
import EventBus from '@/components/FileManager/eventBus';
import { checkOnlyStore } from '@/utils';
import permission from '@/directive/permission'; // Permission directive (v-permission)

export default {
  name: 'ProductList',
  components: { Pagination, RightPanel, FilterSystemProduct },
  directives:{ permission },
  data() {
    return {
      list: null,
      total: 0,
      loading: true,
      listQuery: {
        page: 1,
        limit: 10,
        from: '',
        to: '',
        price: null,
        filter_price_by: 'Cost',
        sort_order: 'id__desc',
        status: ['1'],
        category: null,
        keyword: '',
      },
    };
  },
  computed:{
    checkOnlyStore
  },
  methods: {
    renderKind(kind){
      let kinds = {
            0: '.single.',
            1: '.bundle.',
            2: '.group.',
          };
      return kinds[kind];
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
    handleSelectionAllChange(val){
      EventBus.$emit('listenMultiSelectRow', val);
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    handleDeleting(row){
      EventBus.$emit('handleDeleting', row);
    },
    renderRouterEdit(kind, prid){
      var rou = 'ProductEditSingle';
      if (kind == 1) {
        rou = 'ProductEditBundle';
      } else if (kind == 2) {
        rou = 'ProductEditGroup';
      }
      this.$router.push({ name: rou, params: { id: prid }});
    },
  },
};
</script>
<style>
.el-slider__runway{
  margin:11px 0px !important;
}
</style>
