
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
              <el-button type="primary" icon="el-icon-plus" :disabled="dataLoading" class="filter-item" @click="$router.push({ name: 'CategoryCreate'}).catch(() => {})" />
              <el-button type="success" :disabled="dataLoading" @click="handleDownload"><svg-icon icon-class="excel" /></el-button>
            </el-button-group>
            <div class="el-select filter-item el-select--medium">
              <el-checkbox v-model="parent" label="ROOT" border @change="handleFilter" />
            </div>
          </el-col>
        </el-row>
      </div>

      <h3 class="drawer-title">
        {{ $t('table.filter') }}
      </h3>
      <div class="drawer-item">

        <el-row :gutter="20">
          <el-col :span="12">
            <el-select v-model="dataQuery.sort" :placeholder="$t('table.order')" style="width:100%" clearable class="filter-item" @change="handleFilter('sort',dataQuery.sort)">
              <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
            </el-select>
          </el-col>
          <el-col :span="12">
            <el-select v-model="dataQuery.status" :placeholder="$t('table.status')" style="width: 100%" class="filter-item" clearable multiple @change="handleFilter('filter',dataQuery.status)">
              <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
            </el-select>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-input v-model="dataQuery.title" clearable :placeholder="$t('table.name')" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import { parseTime } from '@/filters';
import EventBus from '@/components/FileManager/eventBus';
import CategoryResource from '@/api/category';

const categoryResource = new CategoryResource();
export default {
  name: 'FilterSystemCategory',
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
      parent: true,
      listQuery: {},
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
        label: 'Title A-Z',
        key: 'title__asc',
        active: false,
      }, {
        label: 'Title Z-A',
        key: 'title__desc',
        active: false,
      }],
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
      if (this.parent === false) {
        this.dataQuery.parent = '';
      } else {
        this.dataQuery.parent = '0';
      }
      const data = await categoryResource.list(this.dataQuery);

      this.list = data.data;
      this.total = data.meta.total;

      this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
    },
    handleFilter(type, e) {
      if (type === 'sort' && e) {
        this.sortOptions.filter(function(elem){
          if (elem.key === e){
            elem.active = true;
          } else {
            elem.active = false;
          }
        });
      } else if (type === 'filter' && e) {
        this.fillterStatusOptions.filter(function(elem){
          if (elem.key === e){
            elem.active = true;
          } else {
            elem.active = false;
          }
        });
      }
      this.dataQuery.page = 1;

      this.getList();
    },
    handleDownload() {
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Id', 'Name', 'Parent', 'Sort', 'Show on app', 'Status'];
        const filterVal = ['id', 'name', 'parent', 'sort', 'top', 'status'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Category-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
      });
    },
    formatJson(filterVal, jsonData) {
      const getValue = (object, keys) => keys.split('.').reduce((o, k) => (o || {})[k], object);

      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j]);
        } else {
          return getValue(v, j);
        }
      }));
    },
    handleDeleting(row) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.$emit('handleListenData', { loading: true });
        categoryResource.destroy(row.id).then((res) => {
          const index = this.list.indexOf(row);
          this.$message({
            type: 'success',
            message: 'Delete successfully',
          });
          if (index == -1) {
            const view = this.$router.resolve({ name: 'CategoryList' }).route;
            this.$store.dispatch('tagsView/delCachedView', view).then(() => {
              const { fullPath } = view;
              this.$nextTick(() => {
                this.$router.replace({
                  path: '/redirect' + fullPath,
                });
              });
            });
          } else {
            this.list.splice(index, 1);
            const total = this.total - Array(row).length;
            this.$emit('handleListenData', { list: this.list, loading: false, total: total, listQuery: this.dataQuery });
          }
        }).catch(() => {
          this.$emit('handleListenData', { loading: false });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
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
