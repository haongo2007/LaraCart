
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
              <el-button type="primary" icon="el-icon-plus" :disabled="dataLoading" class="filter-item" 
              @click="$router.push({ name: 'BlogCreate'}).catch(() => {})" v-permission="['create.blog']" />
              <el-button type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" 
              @click="handerDeleteAll" v-permission="['delete.blog']"/>
            </el-button-group>
          </el-col>
        </el-row>
      </div>

      <h3 class="drawer-title">
        {{ $t('table.filter') }}
      </h3>
      <div class="drawer-item">

        <el-row :gutter="24">
          <el-col :span="12">
            <el-select v-model="dataQuery.sort_order" :placeholder="$t('table.order')" style="width:100%" clearable class="filter-item" @change="handleFilter('sort_order',dataQuery.sort_order)">
              <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
            </el-select>
          </el-col>
          <el-col :span="12">
            <el-input v-model="dataQuery.keyword" :placeholder="$t('form.typing_input_blog')" style="width: 100%;" class="filter-item" @keyup.enter.native="handleFilter" />
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>

import { parseTime } from '@/filters';
import EventBus from '@/components/FileManager/eventBus';
import BlogsResource from '@/api/blogs';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import reloadRedirectToList from '@/utils';

const blogsResource = new BlogsResource();
export default {
  name: 'FilterSystemBlog',
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
      list: null,
      total: 0,
      roles: [],
      multiSelectRow:[],
      sortOptions: [{
        label: 'Id DESC',
        key: 'id__desc',
        active: true,
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
    EventBus.$on('listenMultiSelectRow', data => {
      this.multiSelectRow = data;
    });
    EventBus.$on('handleDeleting', this.handleDeleting);
  },
  methods: {
    async getList() {
      const data = await blogsResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;

      this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
    },
    handleFilter(type, e) {
      this.dataQuery.page = 1;
      this.getList();
    },
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDeleting(row, multiple = false) {
      this.$confirm(this.$t('form.answer_delete'), 'Warning', {
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
        blogsResource.destroy(id).then((res) => {
          if (res) {
            if (multiple) {
              reloadRedirectToList('BlogList');
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
