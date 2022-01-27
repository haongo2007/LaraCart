<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.name" :placeholder="$t('table.name')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-select v-model="listQuery.is_active" :placeholder="$t('table.status')" style="width: 90px" class="filter-item" clearable @change="handleFilter('filter',listQuery.is_active)">
        <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
      </el-select>
      <el-select v-model="listQuery.by" :placeholder="$t('Sort by')" clearable class="filter-item" style="width: 130px">
        <el-option v-for="item in sortByOptions" :key="item.key" :value="item.key" :label="item.label" />
      </el-select>
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter('sort',listQuery.sort)">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        {{ $t('table.export') }}
      </el-button>
      <!-- <el-checkbox v-model="showReviewer" class="filter-item" style="margin-left:15px;" @change="tableKey=tableKey+1">
        {{ $t('table.reviewer') }}
      </el-checkbox> -->
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column :label="$t('table.id')" prop="id" sortable="custom" align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.name')" min-width="150px">
        <template slot-scope="scope">
          <el-tag>{{ scope.row.name }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.banner')" min-width="150px">
        <template slot-scope="scope">
          <el-image :src="scope.row.banner+'&w=260'">
            <div slot="placeholder" class="image-slot">
              <i class="el-icon-loading" />
            </div>
          </el-image>
        </template>
      </el-table-column>
      <el-table-column :label="$t('order')" width="110px" align="center">
        <template slot-scope="scope">
          <span style="color:red;">{{ scope.row.order }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('creator')" width="110px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.creator }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.status')" class-name="status-col" width="100">
        <template slot-scope="{row}">
          <el-tag :type="row.is_active == 1 ? 'published' : 'deleted' | statusFilter">
            {{ row.is_active == 1 ? 'Active' : 'Deactive' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.date')" width="220px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | parseTime('{d}-{m}-{y}') }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.actions')" align="center" width="350" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">
            {{ $t('table.edit') }}
          </el-button>
          <el-button size="mini" type="danger" @click="handleDeleting(row)">
            {{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="70px" style="width: 70%; margin:0 auto;">
        <el-form-item :label="$t('table.name')" prop="name">
          <el-input v-model="temp.name" />
        </el-form-item>
        <el-form-item :label="$t('table.order')" prop="order">
          <el-input v-model.number="temp.order" />
        </el-form-item>
        <el-form-item :label="$t('table.status')" prop="is_active">
          <el-select v-model="temp.is_active" class="filter-item" placeholder="Please select">
            <el-option v-for="(item,index) in statusOptions" :key="item" :label="item" :value="index" />
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('table.banner')">
          <el-upload
            :disabled="disableUpload"
            :multiple="false"
            class="upload-demo"
            action="/"
            accept="image/jpeg,image/gif,image/png"
            :auto-upload="false"
            :on-remove="handleRemove"
            :on-change="handleChange"
            :file-list="fileList"
            :limit="1"
            list-type="picture"
          >
            <el-button slot="trigger" size="small" :disabled="disableUpload" type="primary">Click to upload</el-button>

            <el-button size="small" type="success" :disabled="disableUseStorage" @click="handleVisibleStorage()">Use Storage</el-button>
          </el-upload>
        </el-form-item>
        <div class="image-uploading">
          <el-image v-if="fileStorage" :src="fileStorage">
            <div slot="placeholder" class="image-slot">
              <i class="el-icon-loading" />
            </div>
          </el-image>
          <i v-if="fileStorage" class="el-icon-close" @click="resetImageUpload()" />
        </div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="component" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>
import BrandResource from '@/api/brand';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';

// const calendarTypeOptions = [
//   { key: 'CN', display_name: 'China' },
//   { key: 'US', display_name: 'USA' },
//   { key: 'JA', display_name: 'Japan' },
//   { key: 'VI', display_name: 'Vietnam' },
// ];

// arr to obj ,such as { CN : "China", US : "USA" }
// const calendarTypeKeyValue = calendarTypeOptions.reduce((acc, cur) => {
//   acc[cur.key] = cur.display_name;
//   return acc;
// }, {});

const brandResource = new BrandResource();

export default {
  name: 'Brand',
  components: { Pagination, FileManager },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        deleted: 'danger',
      };
      return statusMap[status];
    },
    // typeFilter(type) {
    //   return calendarTypeKeyValue[type];
    // },
  },
  data() {
    return {
      component: '',
      fileList: [],
      fileStorage: '',
      dialogStorageVisible: false,
      disableUpload: false,
      disableUseStorage: false,
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      dialogLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        is_active: undefined,
        name: undefined,
        type: undefined,
        sort: undefined,
        by: undefined,
      },
      sortOptions: [{
        label: 'ID Ascending',
        key: 'asc',
        active: false,
      }, {
        label: 'ID Descending',
        key: 'desc',
        active: true,
      }],
      fillterStatusOptions: [{
        label: 'All',
        key: '-1',
        active: true,
      }, {
        label: 'Deactive',
        key: '0',
        active: false,
      }, {
        label: 'Active',
        key: '1',
        active: false,
      }],
      sortByOptions: [{
        label: 'ID',
        key: 'id',
        active: false,
      }, {
        label: 'Name',
        key: 'name',
        active: false,
      }, {
        label: 'order',
        key: 'order',
        active: false,
      }, {
        label: 'status',
        key: 'is_active',
        active: false,
      }, {
        label: 'Date',
        key: 'created_at',
        active: false,
      }],
      statusOptions: ['Deactive', 'Active'],
      // showReviewer: false,
      temp: {
        id: undefined,
        name: '',
        order: '',
        is_active: undefined,
        banner: [],
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create',
      },
      dialogPvVisible: false,
      pvData: [],
      ifData: [],
      rules: {
        order: [
          {
            type: 'number',
            message: 'order must be a number',
            trigger: 'blur',
          },
        ],
        status: [
          {
            required: true,
            message: 'status is required',
            trigger: 'change',
          },
        ],
        name: [
          {
            required: true,
            message: 'name is required',
            trigger: 'blur',
          },
          {
            min: 3,
            message: 'Length min 3',
            trigger: 'blur',
          },
        ],
      },
      downloadLoading: false,
    };
  },
  created() {
    this.getList();
    EventBus.$on('getFileResponse', this.handlerGeturl);
  },
  methods: {
    handlerGeturl(data) {
      if (data) {
        this.fileStorage = data.url + '&w=644';
        this.disableUpload = true;
        this.temp.banner = data.id;
        this.dialogStorageClose();
      }
    },
    async getList() {
      this.listLoading = true;
      const data = await brandResource.list(this.listQuery);
      this.list = data.data;
      this.total = data.meta.total;
      // Just to simulate the time of the request
      this.listLoading = false;
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
    resetTemp() {
      this.temp = {
        id: undefined,
        name: undefined,
        order: undefined,
        status: undefined,
        banner: [],
      };
    },
    handleCreate() {
      // reset form add;
      this.fileList = [];
      this.resetTemp();
      this.disableUpload = false;
      this.disableUseStorage = false;
      this.fileStorage = '';

      this.dialogStatus = 'create';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog',
          });
          const form_data = new FormData();
          for (var key in this.temp) {
            form_data.append(key, this.temp[key]);
          }
          brandResource.store(form_data).then((res) => {
            if (res) {
              res.banner = res.banner.url;
              this.list.unshift(res);
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
            }
            loading.close();
            this.dialogFormVisible = false;
          });
        }
      });
    },
    handleUpdate(row) {
      const loading = this.$loading({
        target: '.el-dialog',
      });
      this.fileList = [];
      this.resetTemp();
      this.temp = Object.assign({}, row); // copy obj
      // refresh temp
      this.disableUpload = true;
      this.temp.order = parseInt(row.order);
      this.temp.is_active = parseInt(row.is_active);
      this.fileStorage = row.banner + '&w=644';
      this.dialogStatus = 'update';
      // open dialog
      loading.close();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog',
          });
          const form_data = new FormData();
          for (var key in this.temp) {
            form_data.append(key, this.temp[key]);
            form_data.append('_method', 'PUT');
          }

          // tempData.timestamp = +new Date(tempData.timestamp); // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          brandResource.update(this.temp.id, form_data).then((res) => {
            for (const v of this.list) {
              if (v.id === res.id) {
                const index = this.list.indexOf(v);
                // fillter data
                res.banner = res.banner.url;
                res.order = parseInt(res.order);
                res.is_active = parseInt(res.is_active);
                this.list.splice(index, 1, res);
                break;
              }
            }
            loading.close();
            this.dialogFormVisible = false;
            this.$message({
              type: 'success',
              message: 'Updated successfully',
            });
          });
        }
      });
    },
    handleDownload() {
      this.downloadLoading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Id', 'Child', 'Name', 'Creator', 'Status', 'Date'];
        const filterVal = ['id', 'children', 'name', 'creator', 'is_active', 'created_at'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list',
        });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j]);
        } else {
          return v[j];
        }
      }));
    },
    handleDeleting(row) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        brandResource.destroy(row.id).then((res) => {
          const index = this.list.indexOf(row);
          this.list.splice(index, 1);
          this.$message({
            type: 'success',
            message: 'Delete successfully',
          });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleRemove(file) {
      this.disableUseStorage = false;
    },
    handleChange(file) {
      this.temp.banner = file.raw;
      this.disableUseStorage = true;
    },
    handleVisibleStorage(){
      this.component = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
      this.component = '';
      this.dialogStorageVisible = false;
    },
    resetImageUpload(){
      this.disableUpload = false;
      this.temp.banner = [];
      this.component = '';
      this.fileStorage = '';
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .image-uploading{
    position: relative;
    .el-icon-close{
        cursor: pointer;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 18px;
        opacity: 0;
        transition:all .5s;
    }
    &:hover {
      .el-icon-close{
        opacity: 1;
      }
    }
  }
</style>
