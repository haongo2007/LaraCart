
<template>
  <div v-loading="dataLoading" class="drawer-container">
    <div>
      <h3 class="drawer-title">
        {{ $t('table.actions') }}
      </h3>

      <div class="drawer-item">
        <el-row :gutter="20">
          <el-col :span="24">
            <el-button-group>
              <el-button type="success" @click="handleDownload" :disabled="downloadLoading" class="filter-item" v-permission="['export.reportanalytics']">
                <svg-icon icon-class="excel" />
              </el-button>
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
            <el-input v-model="dataQuery.keyword" :placeholder="$t('table.keyword')" style="width: 100%;" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import { parseTime } from '@/filters';
import EventBus from '@/components/FileManager/eventBus';
import Reportesource from '@/api/report';
import permission from '@/directive/permission'; // Permission directive (v-permission)

const reportesource = new Reportesource();

export default {
  name: 'FilterSystemProductReport',
  directives: { permission },
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
      downloadLoading:false,
      list: null,
      total: 0,
      roles: [],
      multiSelectRow:[]
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
    EventBus.$on('handleDeleting', this.handleDeleting);
  },
  methods: {
    async getList() {
      const data = await reportesource.product(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;

      this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
    },
    handleFilter(type, e) {
      this.dataQuery.page = 1;
      this.getList();
    },
    handleDownload() {
      this.downloadLoading = true;
      this.$emit('handleListenData', { loading: true });
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['ID', 'Store', 'SKU', 'Name', 'Cost', 'Price', 'Sold', 'Stock', 'View', 'last View', 'Kind', 'Status'];
        const filterVal = ['id', 'first_name', 'sku', 'name', 'cost', 'price', 'sold', 'stock', 'view', 'last_view', 'kind', 'status'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Product-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
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
