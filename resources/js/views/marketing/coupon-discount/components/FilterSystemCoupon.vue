
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
              <el-button
                v-permission="['create.coupon']"
                type="primary"
                icon="el-icon-plus"
                :disabled="dataLoading"
                class="filter-item"
                @click="handleCreate()"
              />
              <el-button
                v-permission="['delete.coupon']"
                type="danger"
                icon="el-icon-delete"
                :disabled="multiSelectRow.length == 0 ? true : false"
                @click="handerDeleteAll"
              />
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

import EventBus from '@/components/FileManager/eventBus';
import CouponResource from '@/api/coupon-discount';
import permission from '@/directive/permission'; // Permission directive (v-permission)

const couponResource = new CouponResource();

export default {
  name: 'FilterSystemCoupon',
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
      const data = await couponResource.list(this.dataQuery);
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
        couponResource.destroy(id).then((res) => {
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
    handleCreate() {
      this.$emit('handleListenCreateForm', true);
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
