
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
              @click="$router.push({ name: 'EmailTemplateCreate'}).catch(() => {})" v-permission="['create.email.template']" />
              <el-button type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" 
              @click="handerDeleteAll" v-permission="['delete.email.template']"/>
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
            <el-select v-model="dataQuery.status" :placeholder="$t('table.status')" style="width: 100%" class="filter-item" clearable multiple @change="handleFilter('filter',dataQuery.status)">
              <el-option v-for="item in statusOptions" :key="item.key" :label="item.label" :value="item.key" />
            </el-select>
          </el-col>
          <el-col :span="8">
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
import EmailTemplateResource from '@/api/email-template';
import permission from '@/directive/permission'; // Permission directive (v-permission)

const emailTemplateResource = new EmailTemplateResource();

export default {
  name: 'FilterSystemEmailTemplate',
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
      statusOptions:[{
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
        label: 'Name A-Z',
        key: 'name__asc',
        active: false,
      }, {
        label: 'Name Z-A',
        key: 'name__desc',
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
      const data = await emailTemplateResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;

      this.$emit('handleListenData', { list: this.list, loading: false, total: this.total, listQuery: this.dataQuery });
    },
    handleFilter(type, e) {
      if (type === 'filter' && e) {
        this.statusOptions.filter(function(elem){
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
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
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
        emailTemplateResource.destroy(id).then((res) => {
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
