<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row :gutter="20">
        <el-col :span="3">
          <el-button-group style="width: 100%;">
            <el-button style="width: 33.3%;" type="primary" icon="el-icon-plus" :disabled="loading" class="filter-item" @click="$router.push({ name: 'OrderCreate'})" />
            <el-button v-waves style="width: 33.3%;" type="success" :disabled="loading" @click="handleDownload"><svg-icon icon-class="excel" /></el-button>
            <el-button style="width: 33.3%;" type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" @click="handerDeleteAll" />
          </el-button-group>
        </el-col>
        <el-col :span="9">
          <el-date-picker
            v-model="arDateToSearch"
            style="width: 100%;display: flex;justify-content: space-between;"
            type="datetimerange"
            align="right"
            unlink-panels
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            :picker-options="pickerOptions"
            @change="handleFilter()"
          />
        </el-col>
        <el-col :span="6">
          <el-input v-model="listQuery.customer_name" clearable placeholder="Customer" class="filter-item" @keyup.enter.native="handleFilter" />
        </el-col>
        <el-col :span="6">
          <el-input v-model="listQuery.customer_email" clearable placeholder="Email" class="filter-item" @keyup.enter.native="handleFilter" />
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="6">
          <el-input v-model="listQuery.order_id" clearable placeholder="Order ID" class="filter-item" @keyup.enter.native="handleFilter" />
        </el-col>
        <el-col :span="6">
          <el-input v-model="listQuery.customer_phone" clearable placeholder="Phone" class="filter-item" @keyup.enter.native="handleFilter" />
        </el-col>
        <el-col :span="6">
          <el-select v-model="listQuery.status" style="width: 100%" :placeholder="$t('table.status')" class="filter-item" clearable multiple @change="handleFilter">
            <el-option v-for="item in StatusOptions" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-col>
        <el-col :span="6">
          <el-select v-model="listQuery.sort_order" style="width: 100%" clearable class="filter-item" @change="handleFilter">
            <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
          </el-select>
        </el-col>
      </el-row>
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

      <el-table-column label="Discount" min-min-width="150" align="center">
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

      <el-table-column :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="$router.push({ name: 'OrderEdit',params:{id:row.id} })" />
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { parseTime } from '@/filters';
import OrdersResource from '@/api/orders';
import OrderStatusResource from '@/api/order_status';
import Pagination from '@/components/Pagination';
import waves from '@/directive/waves'; // Waves directive

const ordersResource = new OrdersResource();
const orderStatusResource = new OrderStatusResource();
var statusMap = null;
export default {
  name: 'OrdersList',
  components: { Pagination },
  directives: { waves },
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
      downloadLoading: false,
      arDateToSearch: [],
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
      sortOptions: [{
        label: 'Id DESC',
        key: 'id__desc',
        active: false,
      }, {
        label: 'Id ASC',
        key: 'id__asc',
        active: false,
      }, {
        label: 'Created DESC',
        key: 'created_at__desc',
        active: false,
      }, {
        label: 'Created ASC',
        key: 'created_at__asc',
        active: false,
      }, {
        label: 'Price DESC',
        key: 'subtotal__desc',
        active: false,
      }, {
        label: 'Price ASC',
        key: 'subtotal__asc',
        active: false,
      }],
      StatusOptions: [],
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          },
        }],
      },
      multiSelectRow: [],
    };
  },
  created() {
    this.getListStatus();
    this.getList();
  },
  methods: {
    async getList() {
      const data = await ordersResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    async getListStatus() {
      const { data } = await orderStatusResource.list();
      this.StatusOptions = data;
      statusMap = data;
    },
    handleDeleting(row, multiple = false) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.loading = true;
        if (multiple) {
          var id = [];
          row.map((item) => id.push(item.id));
        } else {
          var id = row.id;
        }
        var that = this;
        ordersResource.destroy(id).then((res) => {
          if (res) {
            if (multiple) {
              row.forEach(function(v) {
                const index = that.list.indexOf(v);
                that.list.splice(index, 1);
              });
            } else {
              const index = that.list.indexOf(row);
              that.list.splice(index, 1);
            }
            this.$message({
              type: 'success',
              message: 'Delete successfully',
            });
            this.total = this.total - Array(row).length;
          }
          this.loading = false;
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
        this.loading = false;
      });
    },
    handleFilter(){
      this.loading = true;
      if (this.arDateToSearch !== null && this.arDateToSearch.length > 0) {
        this.listQuery.from = parseTime(this.arDateToSearch[0], '{d}-{m}-{y} {h}:{i}:{s}');
        this.listQuery.to = parseTime(this.arDateToSearch[1], '{d}-{m}-{y} {h}:{i}:{s}');
      } else {
        this.listQuery.from = '';
        this.listQuery.to = '';
      }
      this.getList();
    },
    handleSelectionAllChange(val){
      this.multiSelectRow = val;
    },
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDownload() {
      this.loading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Order#', 'Customer', 'Country', 'Address', 'Phone', 'Email', 'Subtotal', 'Ship fee', 'Discount', 'Total', 'Payment', 'Currency', 'Status', 'Created at'];
        const filterVal = ['id', 'first_name', 'country', 'address3', 'phone', 'email', 'subtotal', 'shipping', 'discount', 'total', 'payment_method', 'currency', 'status', 'created_at'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Orders-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
        this.loading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      const getValue = (object, keys) => keys.split('.').reduce((o, k) => (o || {})[k], object);
      return jsonData.map(v => filterVal.map(j => {
        console.log(j);
        if (j === 'created_at') {
          return parseTime(v[j]);
        } else {
          return getValue(v, j);
        }
      }));
    },
  },
};
</script>
