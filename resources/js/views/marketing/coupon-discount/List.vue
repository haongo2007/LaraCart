<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-coupon
          :data-loading="loading"
          :data-query="listQuery"
          :data-loading-button-create="loadingButtonCreate"
          @handleListenData="handleListenData"
          @handleListenCreateForm="CreateForm"
        />
      </right-panel>
    </div>
    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%" @selection-change="handleSelectionAllChange">
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column align="center" :label="$t('table.id')" width="50">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column v-if="checkOnlyStore" :label="$t('table.store')" min-width="150">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop" />
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.code')">
        <template slot-scope="scope">
          <span>{{ scope.row.code }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.value')">
        <template slot-scope="scope">
          <span>{{ scope.row.reward }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.type')">
        <template slot-scope="scope">
          <span>{{ scope.row.type }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.data')" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.data }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.limit')">
        <template slot-scope="scope">
          <span>{{ scope.row.limit }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.used')" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.used }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.login_required')" class-name="status-col" width="150" prop="login_required">
        <template slot-scope="{row}">
          <el-tag :type="row.login | statusFilter">
            {{ row.login | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.expries_at')" min-width="195" align="center">
        <template v-if="scope.row.expires_at" slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.expires_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="200" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button
              v-permission="['edit.coupon']"
              :loading="loadingButtonUpdate"
              type="primary"
              size="mini"
              icon="el-icon-edit"
              class="filter-item"
              @click="UpdateForm(row)"
            />
            <el-button v-permission="['delete.coupon']" type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
    <el-dialog :title="$t('form.'+dialogStatus)" :visible.sync="dialogFormVisible" :before-close="handleReset" class="dialog-custom">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 75%; margin:0 auto;">

        <el-form-item :label="$t('form.code')" prop="code" :placeholder="$t('form.code')">
          <el-input v-model="temp.code" />
        </el-form-item>

        <el-form-item :label="$t('form.reward')" prop="reward">
          <el-input v-model.number="temp.reward" type="number" :placeholder="$t('form.reward')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.type')" prop="type">
          <el-radio v-for="(item,index) in typeList" :key="index" v-model="temp.type" :label="item.value">
            {{ item.name }}
          </el-radio>
        </el-form-item>

        <el-form-item :label="$t('form.description')" prop="description">
          <el-input v-model="temp.data" :placeholder="$t('form.description')" />
        </el-form-item>

        <el-form-item :label="$t('form.limit')" prop="limit">
          <el-input v-model.number="temp.limit" type="number" :placeholder="$t('form.limit')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.expires')" prop="expires_at">
          <el-date-picker
            v-model="temp.expires_at"
            style="width: 100%"
            type="date"
            placeholder="Pick a day"
            format="yyyy-MM-dd"
            :picker-options="pickerOptions"
          />
        </el-form-item>

        <el-form-item :label="$t('form.login_require')">
          <el-switch
            v-model="temp.login"
            active-color="#13ce66"
            inactive-color="#ff4949"
            active-value="1"
            inactive-value="0"
          />
        </el-form-item>

        <el-form-item :label="$t('form.status')">
          <el-switch
            v-model="temp.status"
            active-color="#13ce66"
            inactive-color="#ff4949"
            active-value="1"
            inactive-value="0"
          />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('form.cancel') }}
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">
          {{ $t('form.confirm') }}
        </el-button>
      </div>
    </el-dialog>
    <el-dialog
      :show-close="false"
      :title="$t('form.choose_store_for_coupon')"
      :visible.sync="confirmStoreDialog"
      :before-close="handleConfirm"
      width="30%"
    >
      <div>
        <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index">
          {{ item.descriptions_current_lang[0].title }}
        </el-radio>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" :disabled="temp.store_id == 0" @click="confirmChooseStore">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import RightPanel from '@/components/RightPanel';
import FilterSystemCoupon from './components/FilterSystemCoupon';
import EventBus from '@/components/FileManager/eventBus';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import Cookies from 'js-cookie';
import CouponResource from '@/api/coupon-discount';
import { parseTime } from '@/filters';
import { checkOnlyStore } from '@/utils';

const couponResource = new CouponResource();

const dataForm = {
  id: 0,
  store_id: 0,
  code: '',
  reward: '',
  type: '%',
  data: '',
  limit: '1',
  login: '',
  status: '',
  expires_at: '',
  used: 0,
};

export default {
  name: 'CouponList',
  components: { Pagination, FilterSystemCoupon, RightPanel },
  directives: { permission },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      temp: Object.assign({}, dataForm),
      rules: {
        code: [
          {
            required: true,
            message: 'code is required',
            trigger: 'blur',
          },
        ],
        limit: [
          {
            required: true,
            message: 'limit is required',
            trigger: 'blur',
          },
          {
            type: 'number',
            message: 'limit must be a number',
            trigger: 'blur',
          },
        ],
        reward: [
          {
            required: true,
            message: 'reward is required',
            trigger: 'blur',
          },
          {
            type: 'number',
            message: 'reward must be a number',
            trigger: 'blur',
          },
        ],
        type: [
          {
            required: true,
            message: 'type is required',
            trigger: 'blur',
          },
        ],
        expires_at: [
          { type: 'date', required: true, message: 'date is required', trigger: 'blur' },
        ],
      },
      typeList: [{
        name: 'Point',
        value: 'Point',
      }, {
        name: 'Percent',
        value: '%',
      }],
      listQuery: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      loadingButtonCreate: false,
      loadingButtonUpdate: false,
      dialogFormVisible: false,
      confirmStoreDialog: false,
      dialogStatus: '',
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() < Date.now();
        },
        shortcuts: [{
          text: 'Yesterday',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit('pick', date);
          },
        }, {
          text: 'A week',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
            picker.$emit('pick', date);
          },
        }, {
          text: 'A Month',
          onClick(picker) {
            const date = new Date();
            date.setMonth(date.getMonth() + 1);
            picker.$emit('pick', date);
          },
        }],
      },
    };
  },
  computed: {
    checkOnlyStore,
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  methods: {
    handleListenData(data){
      if (data.hasOwnProperty('list')) {
        this.list = data.list;
      }
      if (data.hasOwnProperty('loading')) {
        this.loading = data.loading;
      }
      if (data.hasOwnProperty('total')) {
        this.total = data.total;
      }
      if (data.hasOwnProperty('listQuery')) {
        this.listQuery = data.listQuery;
      }
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    handleSelectionAllChange(val){
      EventBus.$emit('listenMultiSelectRow', val);
    },
    handleDeleting(row){
      EventBus.$emit('handleDeleting', row);
    },
    handleReset(done){
      this.temp = Object.assign({}, dataForm);
      done();
    },
    handleConfirm(done){
      if (this.temp.store_id > 0) {
        this.CreateForm();
        done();
      }
    },
    confirmChooseStore(){
      this.confirmStoreDialog = false;
      this.CreateForm();
    },
    createData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog',
          });
          this.temp.expires_at = parseTime(this.temp.expires_at, '{y}-{m}-{d} {h}:{i}:{s}');
          couponResource.store(this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              this.$set(this.temp, 'store', this.storeList[this.temp.store_id]);
              this.temp.id = res.data.id;
              this.list.push(this.temp);
              // reloadRedirectToList('BannerList');
              this.handleReset();
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
            // eslint-disable-next-line handle-callback-err
          }).catch(err => {
            loading.close();
          });
        }
      });
    },
    updateData(){
      this.temp.expires_at = new Date(this.temp.expires_at);
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog__body',
          });
          this.temp.expires_at = parseTime(this.temp.expires_at, '{y}-{m}-{d} {h}:{i}:{s}');
          couponResource.update(this.temp.id, this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Update successfully',
              });
              const index = this.list.findIndex((item) => item.id === this.temp.id);
              if (index > -1) {
                this.$set(this.list, index, { ...this.temp });
              }
              this.handleReset();
            } else {
              this.$message({
                type: 'error',
                message: 'Update failed',
              });
              loading.close();
            }
            // eslint-disable-next-line handle-callback-err
          }).catch(err => {
            loading.close();
          });
        }
      });
    },
    async CreateForm(){
      this.loadingButtonCreate = true;
      let store_ck = Cookies.get('store');
      if (store_ck) {
        store_ck = JSON.parse(store_ck);
      }
      if (store_ck && store_ck.length === 1) {
        this.temp.store_id = store_ck[0];
      } else {
        if (this.temp.store_id === 0) {
          this.confirmStoreDialog = true;
          return false;
        }
      }
      // reset form add;
      this.loadingButtonCreate = false;
      this.dialogStatus = 'create';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    async UpdateForm(row){
      this.loadingButtonUpdate = true;
      this.temp = Object.assign(this.temp, row);
      this.temp.status = String(this.temp.status);
      this.temp.login = String(this.temp.login);
      this.temp.store_id = row.store.id;
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.loadingButtonUpdate = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
