
<template>
  <div v-loading="dataLoading" class="drawer-container">
    <div>
      <h3 class="drawer-title">
        {{ $t('table.actions') }}
      </h3>

      <div class="drawer-item">
        <el-row :gutter="24">
          <el-col :span="12">
            <el-button-group>
              <el-button v-permission="['create.order']" type="primary" icon="el-icon-plus" :disabled="dataLoading" class="filter-item" 
              @click="$router.push({ name: 'OrderCreate'})" />
              <el-button v-permission="['export.order']" type="success" :disabled="downloadLoading" @click="handleDownload">
                <svg-icon icon-class="excel" />
              </el-button>
              <el-button v-permission="['delete.order']" type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" 
              @click="handerDeleteAll" />
            </el-button-group>
          </el-col>
        </el-row>
      </div>

      <h3 class="drawer-title">
        {{ $t('table.filter') }}
      </h3>

      <div class="drawer-item">
        <el-row :gutter="24">
          <el-col :span="24">
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
        </el-row>
        <el-row :gutter="24">
          <el-col :span="12">
            <el-input v-model="dataQuery.customer_name" clearable placeholder="Customer" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
          <el-col :span="12">
            <el-input v-model="dataQuery.customer_email" clearable placeholder="Email" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>

        <el-row :gutter="24">
          <el-col :span="12">
            <el-input v-model="dataQuery.order_id" clearable placeholder="Order ID" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
          <el-col :span="12">
            <el-input v-model="dataQuery.customer_phone" clearable placeholder="Phone" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>

        <el-row :gutter="24">
          <el-col :span="12">
            <el-select v-model="dataQuery.status" style="width: 100%" :placeholder="$t('table.status')" class="filter-item" clearable multiple @change="handleFilter">
              <el-option v-for="item in dataStatusOptions" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </el-col>
          <el-col :span="12">
            <el-select v-model="dataQuery.sort_order" style="width: 100%" clearable class="filter-item" @change="handleFilter">
              <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
            </el-select>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import permission from '@/directive/permission'; // Permission directive (v-permission)
import OrdersResource from '@/api/orders';
import { parseTime } from '@/filters';
import EventBus from '@/components/FileManager/eventBus';

const ordersResource = new OrdersResource();
export default {
  name: 'FilterSystemProduct',
  directives:{ permission },
  props: {
    dataLoading: {
      type: Boolean,
      default: true,
    },
    dataQuery: {
      type: Object,
      default: false,
    },
    dataStatusOptions: {
      type: Array,
      default: undefined,
    },
  },
  data() {
    return {
      list: null,
      total: 0,
      downloadLoading: false,
      arDateToSearch: [],
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
  watch: {
    'dataQuery.limit': {
      handler(newValue, oldValue) {
        this.getList(newValue);
      },
    },
    'dataQuery.page': {
      handler(newValue, oldValue) {
        this.getList(newValue);
      },
    },
  },
  created() {
    this.getList();
    EventBus.$on('listenMultiSelectRow', data => {
      this.multiSelectRow = data;
    });
    EventBus.$on('handleDeleting', this.handleDeleting);
  },
  methods: {
    async getList() {
      const data = await ordersResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
    },
    handleDeleting(row, multiple = false) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.$emit('handleListenData', { loading: true });
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
            const total = this.total - Array(row).length;
            this.$emit('handleListenData', { list: this.list, loading: false, total: total });
          }
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleFilter(){
      this.$emit('handleListenData', { loading: true });
      if (this.arDateToSearch !== null && this.arDateToSearch.length > 0) {
        this.dataQuery.from = parseTime(this.arDateToSearch[0], '{d}-{m}-{y} {h}:{i}:{s}');
        this.dataQuery.to = parseTime(this.arDateToSearch[1], '{d}-{m}-{y} {h}:{i}:{s}');
      } else {
        this.dataQuery.from = '';
        this.dataQuery.to = '';
      }
      this.getList();
    },
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDownload() {
      this.downloadLoading = true;
      this.$emit('handleListenData', { loading: true });
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Order#', 'Customer', 'Country', 'Address', 'Phone', 'Email', 'Subtotal', 'Ship fee', 'Discount', 'Total', 'Payment', 'Currency', 'Status', 'Created at'];
        const filterVal = ['id', 'first_name', 'country', 'address3', 'phone', 'email', 'subtotal', 'shipping', 'discount', 'total', 'payment_method', 'currency', 'status', 'created_at'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Orders-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
        this.$emit('handleListenData', { loading: false });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      const getValue = (object, keys) => keys.split('.').reduce((o, k) => (o || {})[k], object);
      return jsonData.map(v => filterVal.map(j => {
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

<style lang="scss" scoped>
.drawer-container {
  padding: 24px;
  font-size: 14px;
  line-height: 1.5;
  word-wrap: break-word;

  .drawer-title {
    margin-bottom: 12px;
    color: rgba(0, 0, 0, .85);
    font-size: 20px;
    line-height: 22px;
  }

  .drawer-item {
    color: rgba(0, 0, 0, .65);
    font-size: 14px;
    padding: 12px 0;
  }

  .drawer-switch {
    float: right
  }
  .el-row {
    margin-bottom: 20px;
  }
}
</style>
