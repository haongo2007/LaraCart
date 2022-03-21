
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
              <el-button v-waves type="success" :disabled="dataLoading" @click="handleDownload"><svg-icon icon-class="excel" /></el-button>
              <el-button type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" @click="handerDeleteAll" />
              <el-dropdown trigger="click" placement="top-start" split-button type="primary" @command="handleCommand" @click="$router.push({ name: 'ProductCreateSingle'})">
                <i class="el-icon-plus" />
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item command="ProductCreateBundle">Product Bundle</el-dropdown-item>
                  <el-dropdown-item command="ProductCreateGroup">Product Group</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
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
            <el-input v-model="dataQuery.keyword" clearable placeholder="Typing for search name, Sku, Description or keyword" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>
        <el-row :gutter="24">
          <el-col :span="24">
            <el-date-picker
              v-model="arDateToSearch"
              style="width: 100%;display: flex;justify-content: space-between;"
              type="datetimerange"
              align="right"
              unlink-panels
              range-separator="To"
              start-placeholder="Created date"
              end-placeholder="End date"
              :picker-options="pickerOptions"
              @change="handleFilterDate()"
            />
          </el-col>
        </el-row>

        <el-row :gutter="24">
          <el-col :span="8">
            <el-tooltip class="item" effect="dark" content="Choose value needed filter" placement="bottom-start">
              <el-radio-group v-model="dataQuery.filter_price_by" size="mini" style="width: 100%;" @change="handleFilterPriceSlide">
                <el-radio-button label="Cost" />
                <el-radio-button label="Price" />
              </el-radio-group>
            </el-tooltip>
          </el-col>
          <el-col :span="16">
            <el-tooltip class="item" effect="dark" content="Filter price with slider" placement="bottom-start">
              <el-slider
                v-model="dataQuery.price"
                range
                show-stops
                :step="stepPrice"
                :format-tooltip="formatTooltip"
                :max="maxPrice"
                @change="handleFilter"
              />
            </el-tooltip>
          </el-col>
        </el-row>

        <el-row :gutter="24">
          <el-col :span="8">
            <el-cascader
              v-model="dataQuery.category"
              style="width: 100%;"
              placeholder="Category"
              :options="listRecursive"
              :props="cateRecurProps"
              collapse-tags
              clearable
              @change="handleFilter"
            />
          </el-col>
          <el-col :span="8">
            <el-select v-model="dataQuery.status" style="width: 100%" :placeholder="$t('table.status')" class="filter-item" clearable multiple @change="handleFilter">
              <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
            </el-select>
          </el-col>
          <el-col :span="8">
            <el-select v-model="dataQuery.sort_order" style="width: 100%" :placeholder="$t('table.order')" class="filter-item" @change="handleFilter">
              <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
            </el-select>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import { parseTime, toThousandFilter } from '@/filters';
import ProductResource from '@/api/product';
import CategoryResource from '@/api/category';
import waves from '@/directive/waves'; // Waves directive
import EventBus from '@/components/FileManager/eventBus';

const productResource = new ProductResource();
const categoryResource = new CategoryResource();
export default {
<<<<<<< HEAD:resources/js/views/product/components/FilterSystem.vue
  name: 'FilterSystem',
  directives: { waves },
  props: ['', '', ''],

=======
  name: 'FilterSystemProduct',
  directives: { waves },
>>>>>>> 9b8892473d2f520e27625870c30d3a252da1b7f1:resources/js/views/product/components/FilterSystemProduct.vue
  props: {
    dataLoading: {
      type: Boolean,
      default: true,
    },
    dataQuery: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      list: null,
      total: 0,
      downloadLoading: false,
      arDateToSearch: [],
      maxPrice: null,
      stepPrice: 2,
      fillterStatusOptions: [{
        label: 'Deactive',
        key: '0',
        active: false,
      }, {
        label: 'Active',
        key: '1',
        active: true,
      }],
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
        label: 'Name Z-A',
        key: 'name__desc',
        active: false,
      }, {
        label: 'Name A-Z',
        key: 'name__asc',
        active: false,
      }, {
        label: 'Sku Z-A',
        key: 'sku__desc',
        active: false,
      }, {
        label: 'Sku A-Z',
        key: 'sku__asc',
        active: false,
      }, {
        label: 'Price DESC',
        key: 'price__desc',
        active: false,
      }, {
        label: 'Price ASC',
        key: 'price__asc',
        active: false,
      }],
      listRecursive: [],
      cateRecurProps: {
        children: 'children',
        label: 'title',
        value: 'id',
        multiple: true,
      },
      CategoryOptions: [],
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
  created(){
    this.getListCategory();
    this.getMaxPrice(this.dataQuery.filter_price_by);
    EventBus.$on('listenMultiSelectRow', data => {
      this.multiSelectRow = data;
    });
    EventBus.$on('handleDeleting', this.handleDeleting);
  },
  methods: {
    getList() {
      const data = productResource.list(this.dataQuery).then((data) => {
        this.list = data.data;
        this.total = data.meta.total;
        this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
      });
    },
    async getListCategory() {
      const { data } = await categoryResource.getRecursive();
      this.listRecursive = data;
    },
    async getMaxPrice(type){
      const { data } = await productResource.getMaxPriceProduct(type);
      this.dataQuery.price[0] = 0;
      this.dataQuery.price[1] = data.max;
      this.maxPrice = data.max;
      this.stepPrice = data.max / 10;
      this.getList();
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
        productResource.destroy(id).then((res) => {
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
        }).catch(() => {
          this.$message({
            type: 'danger',
            message: 'Delete error',
          });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleFilterDate(){
      if (this.arDateToSearch !== null && this.arDateToSearch.length > 0) {
        this.dataQuery.from = parseTime(this.arDateToSearch[0], '{d}-{m}-{y} {h}:{i}:{s}');
        this.dataQuery.to = parseTime(this.arDateToSearch[1], '{d}-{m}-{y} {h}:{i}:{s}');
      } else {
        this.dataQuery.from = '';
        this.dataQuery.to = '';
      }
      this.getList();
    },
    handleFilterPriceSlide(){
      this.getMaxPrice(this.dataQuery.filter_price_by);
    },
    handleFilter(){
      this.$emit('handleListenData', { loading: true });
      this.getList();
    },
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDownload() {
      this.dataLoading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Order#', 'Customer', 'Country', 'Address', 'Phone', 'Email', 'Subtotal', 'Ship fee', 'Discount', 'Total', 'Payment', 'Currency', 'Status', 'Created at'];
        const filterVal = ['id', 'first_name', 'country', 'address3', 'phone', 'email', 'subtotal', 'shipping', 'discount', 'total', 'payment_method', 'currency', 'status', 'created_at'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Orders-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
        this.dataLoading = false;
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
    formatTooltip(val) {
      return toThousandFilter(val);
    },
    handleCommand(command) {
      this.$router.push({ name: command });
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
