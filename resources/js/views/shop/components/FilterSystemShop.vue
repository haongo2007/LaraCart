
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
            </el-button-group>
          </el-col>
        </el-row>
      </div>

      <h3 class="drawer-title">
        {{ $t('table.filter') }}
      </h3>
      <div class="drawer-item">

        <el-row :gutter="24">
          <el-col :span="8">
            <el-select v-model="dataQuery.sort" :placeholder="$t('table.order')" style="width:100%" clearable class="filter-item" @change="handleFilter('sort',dataQuery.sort)">
              <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
            </el-select>
          </el-col>
          <el-col :span="8">
            <el-select v-model="dataQuery.status" :placeholder="$t('table.status')" style="width: 100%" class="filter-item" clearable multiple @change="handleFilter('status',dataQuery.status)">
              <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
            </el-select>
          </el-col>
          <el-col :span="8">
            <el-select v-model="dataQuery.active" :placeholder="$t('table.active')" style="width: 100%" class="filter-item" clearable multiple @change="handleFilter('active',dataQuery.active)">
              <el-option v-for="item in fillterActiveOptions" :key="item.key" :label="item.label" :value="item.key" />
            </el-select>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-input v-model="dataQuery.contain" clearable placeholder="Typing for search domain, template, email or phone" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import { parseTime } from '@/filters';
import EventBus from '@/components/FileManager/eventBus';
import StoreResource from '@/api/store';

const storeResource = new StoreResource();
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
      listQuery: {},
      fillterStatusOptions: [{
        label: 'Deactive',
        key: '0',
        active: false,
      }, {
        label: 'Active',
        key: '1',
        active: false,
      }],
      fillterActiveOptions: [{
        label: 'Deactive',
        key: '0',
        active: false,
      }, {
        label: 'Active',
        key: '1',
        active: false,
      }],
      sortOptions: [{
        label: 'Id DESC',
        key: 'id__desc',
        active: false,
      }, {
        label: 'Id ASC',
        key: 'id__asc',
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
  },
  methods: {
    async getList() {
      const data = await storeResource.list(this.dataQuery);
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
      } else if (type === 'status' && e) {
        this.fillterStatusOptions.filter(function(elem){
          if (elem.key === e){
            elem.active = true;
          } else {
            elem.active = false;
          }
        });
      } else if (type === 'active' && e) {
        this.fillterActiveOptions.filter(function(elem){
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
