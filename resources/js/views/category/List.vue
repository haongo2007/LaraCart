<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row :gutter="20">
        <el-col :span="16">
          <el-button-group>
            <el-button type="primary" icon="el-icon-plus" :disabled="loading" class="filter-item" @click="$router.push({ name: 'CategoryCreate'}).catch(() => {})" />
            <el-button v-waves type="success" :disabled="loading" @click="handleDownload"><svg-icon icon-class="excel" /></el-button>
          </el-button-group>
          <div class="el-select filter-item el-select--medium">
            <el-checkbox v-model="parent" label="ROOT" border @change="handleFilter" />
          </div>
          <el-select v-model="listQuery.sort" :placeholder="$t('table.order')" style="width: 140px" clearable class="filter-item" @change="handleFilter('sort',listQuery.sort)">
            <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
          </el-select>

          <el-select v-model="listQuery.status" :placeholder="$t('table.status')" style="width: 220px" class="filter-item" clearable multiple @change="handleFilter('filter',listQuery.status)">
            <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>

        </el-col>
        <el-col :span="8" style="display: flex;justify-content: space-between;">

          <el-input v-model="listQuery.name" clearable :placeholder="$t('table.name')" style="width: 100%;margin-right: 10px;" class="filter-item" @keyup.enter.native="handleFilter" />
          <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
            {{ $t('table.search') }}
          </el-button>

        </el-col>
      </el-row>
    </div>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%"
      row-key="id"
      border
      lazy
      :load="load"
      :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
    >
      <el-table-column :label="labelChildOrParent" align="center" min-width="50">
        <template v-if="row.hasChildren" slot-scope="{row}" />
        <template v-if="!row.hasChildren" slot-scope="{row}">
          <el-tag v-if="row.parent === 0" type="danger">ROOT</el-tag>
          <el-tag v-else type="success">
            {{ row.parent }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.name')" min-width="150px">
        <template slot-scope="scope">
          <el-tag>{{ scope.row.name }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.alias')" min-width="150px" prop="alias">
        <template slot-scope="scope">
          <el-tag>{{ scope.row.alias }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.image')" min-width="150px" prop="image">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=260'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>
      <el-table-column :label="$t('sort')" width="110px" align="center" prop="sort">
        <template slot-scope="scope">
          <span style="color:red;">{{ scope.row.sort }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.top')" width="110px" align="center" prop="top">
        <template slot-scope="{row}">
          <el-tag :type="row.top | statusFilter">
            {{ row.top | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" width="350" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <router-link :to="{ name: 'CategoryEdit',params:{id:row.id} }">
            <el-button type="primary" size="mini" icon="el-icon-edit">
              {{ $t('table.edit') }}
            </el-button>
          </router-link>
          <el-button size="mini" type="danger" @click="handleDeleting(row)">
            {{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { statusFilter } from '@/filters';
import CategoryResource from '@/api/category';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

const categoryResource = new CategoryResource();

const defaultQuery = {
  page: 1,
  limit: 20,
  status: ['1'],
  name: '',
  type: '',
  sort: '',
  parent: '0',
};

export default {
  name: 'CategoryList',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      component: '',
      defaultProps: {
        children: 'children',
        label: 'name',
        value: 'id',
        checkStrictly: true,
      },
      list: [],
      total: 0,
      loading: true,
      dialogLoading: true,
      parent:true,
      listQuery: Object.assign({}, defaultQuery),
      fillterStatusOptions: [{
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
      }, {
        label: 'Title A-Z',
        key: 'title__asc',
        active: false,
      }, {
        label: 'Title Z-A',
        key: 'title__desc',
        active: false,
      }],
      statusOptions: ['Deactive', 'Active'],
      listRecursive: [{
        id: '0',
        id_parent: 0,
        name: 'Is parent',
      }],
    };
  },
  computed: {
    labelChildOrParent(){
      return (this.listQuery.parent === false ? this.$t('table.parent') : this.$t('table.children'));
    },
  },
  created() {
    this.getList();
  },
  methods: {
    async load(row, treeNode, resolve) {
      const id = row.id;
      const { data } = await categoryResource.getChildren(id);

      resolve(data);
    },
    async getList() {
      this.loading = true;
      if (this.parent === false) {
        this.listQuery.parent = '';
      }else{
        this.listQuery.parent = '0';
      }
      const data = await categoryResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      // Just to simulate the time of the request
      this.loading = false;
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
      this.listQuery.page = 1;

      this.getList();
    },
    handleDownload() {
      this.loading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Id', 'Name', 'Parent', 'Sort', 'Show on app', 'Status'];
        const filterVal = ['id', 'name', 'parent', 'sort', 'top', 'status'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Category-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
        this.loading = false;
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
          }
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
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
