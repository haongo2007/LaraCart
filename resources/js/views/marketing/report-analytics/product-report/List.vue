<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-product-report
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
      <el-table-column fixed label="#ID" min-width="50" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id }}
        </template>
      </el-table-column>

      <el-table-column label="Store" min-width="150">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop"></i>
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column label="Image" min-width="150" align="center">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=100'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column label="Sku" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.sku }}
        </template>
      </el-table-column>

      <el-table-column label="Name" min-width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.name }}
        </template>
      </el-table-column>

      <el-table-column label="Cost" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.cost | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column label="Price" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.price | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column label="Sold" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.sold }}
        </template>
      </el-table-column>

      <el-table-column label="Stock" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.stock }}
        </template>
      </el-table-column>

      <el-table-column label="View" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.view }}
        </template>
      </el-table-column>

      <el-table-column label="Last view" min-width="195" align="center">
        <template slot-scope="scope" v-if="scope.row.date_lastview">
          <i class="el-icon-time" />
          <span>{{ scope.row.date_lastview | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Kind" min-min-width="150" align="center">
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

      <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="renderRouterEdit(row.kind,row.id)" />
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
import FilterSystemProductReport from './components/FilterSystemProductReport';
import EventBus from '@/components/FileManager/eventBus';

export default {
  name: 'ProductReportList',
  components: { Pagination,FilterSystemProductReport,RightPanel },
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
