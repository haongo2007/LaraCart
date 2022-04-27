<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-order
          :data-loading="loading"
          :data-query="listQuery"
          :data-status-options="statusOptions"
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
      @selection-change="handleSelectionAllChange"
    >
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column fixed label="Order #" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id | orderNoFilter }}
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

      <el-table-column label="Customer" min-width="150">
        <template slot-scope="scope">
          {{ scope.row && scope.row.first_name }} {{ scope.row && scope.row.last_name }}
        </template>
      </el-table-column>

      <el-table-column label="Country" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.country }}
        </template>
      </el-table-column>

      <el-table-column label="Address" min-width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.address1 }} {{ scope.row && scope.row.address2 }} {{ scope.row && scope.row.address3 }}
        </template>
      </el-table-column>

      <el-table-column label="Phone" min-width="200">
        <template slot-scope="scope">
          {{ scope.row && scope.row.phone | formatPhone }}
        </template>
      </el-table-column>

      <el-table-column label="Email" min-width="200">
        <template slot-scope="scope">
          {{ scope.row && scope.row.email }}
        </template>
      </el-table-column>

      <el-table-column label="Subtotal" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.subtotal | toThousandFilter }} {{ scope.row && scope.row.currency }}
        </template>
      </el-table-column>

      <el-table-column label="Ship fee" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.shipping | toThousandFilter }} {{ scope.row && scope.row.currency }}
        </template>
      </el-table-column>

      <el-table-column label="Discount" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.discount | toThousandFilter }} {{ scope.row && scope.row.currency }}
        </template>
      </el-table-column>

      <el-table-column label="Total" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.total | toThousandFilter }} {{ scope.row && scope.row.currency }}
        </template>
      </el-table-column>

      <el-table-column label="Pm method" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.payment_method }}
        </template>
      </el-table-column>

      <el-table-column label="Currency" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.currency + '/' +scope.row.exchange_rate }}
        </template>
      </el-table-column>

      <el-table-column label="Status" min-width="100" align="center">
        <template slot-scope="scope">
          <el-tag :type="scope.row && scope.row.status | statusFilter('label')">
            {{ scope.row && scope.row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column label="Created at" min-width="195" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="$router.push({ name: 'OrderEdit',params:{id:row.id} })" />
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>

import RightPanel from '@/components/RightPanel';
import FilterSystemOrder from './components/FilterSystemOrder';
import OrderStatusResource from '@/api/order-status';
import Pagination from '@/components/Pagination';
import EventBus from '@/components/FileManager/eventBus';

const orderStatusResource = new OrderStatusResource();
var statusMap = null;
export default {
  name: 'OrdersList',
  components: { Pagination, RightPanel, FilterSystemOrder },
  filters: {
    statusFilter(status, get) {
      const statusFilter = statusMap.filter(v => v.id === status);
      return statusFilter[0][get];
    },
    orderNoFilter(str) {
      return '#' + str;
    },
  },
  data() {
    return {
      list: null,
      total: 0,
      loading: true,
      statusOptions: [],
      listQuery: {
        page: 1,
        limit: 20,
        from: '',
        to: '',
        order_id: '',
        status: [],
        sort_order: 'id__desc',
        customer_name: '',
        customer_email: '',
        customer_phone: '',
      },
    };
  },
  created(){
    this.getListStatus();
  },
  methods: {
    async getListStatus() {
      const { data } = await orderStatusResource.list();
      this.statusOptions = data;
      statusMap = data;
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
    handleSelectionAllChange(val){
      EventBus.$emit('listenMultiSelectRow', val);
    },
    handleDeleting(row){
      EventBus.$emit('handleDeleting', row);
    },
  },
};
</script>
