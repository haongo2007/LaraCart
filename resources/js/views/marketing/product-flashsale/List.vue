<template>
  <div class="app-container">
    <div class="filter-container">
      <right-panel :button-top="'10%'" :z-index="2000" :max-width="'30%'" :i-con="'funnel'">
        <filter-system-product-flashsale
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
      <el-table-column align="center" label="ID" width="50">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column v-if="checkOnlyStore" :label="$t('table.store')" min-width="150">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop" />
            {{ scope.row.product.store.descriptions_current_lang[0].title && scope.row.product.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.product')" min-width="250">
        <template slot-scope="scope">
          <span>{{ scope.row.product.description.name }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.stock')" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.stock }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sold')">
        <template slot-scope="scope">
          <span>{{ scope.row.sold }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sort')">
        <template slot-scope="scope">
          <span>{{ scope.row.sort }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.start_date')" min-width="170">
        <template v-if="scope.row.promotion.date_start" slot-scope="scope">
          <span>{{ scope.row.promotion.date_start }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.end_date')" min-width="170">
        <template v-if="scope.row.promotion.date_end" slot-scope="scope">
          <span>{{ scope.row.promotion.date_end | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.money')">
        <template slot-scope="scope">
          <span>{{ scope.row.promotion.price_promotion }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.promotion.status_promotion | statusFilter">
            {{ row.promotion.status_promotion | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="200" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button
              v-permission="['edit.flashsale']"
              type="primary"
              size="mini"
              icon="el-icon-edit"
              class="filter-item"
              @click="UpdateForm(row)"
            />
            <el-button v-permission="['delete.flashsale']" type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
    <el-dialog :title="$t('form.'+dialogStatus)" :visible.sync="dialogFormVisible" :before-close="handleReset" class="dialog-custom">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 75%; margin:0 auto;">

        <el-form-item :label="$t('form.product')" prop="product_id">
          <el-autocomplete
            v-model="temp.product_name"
            style="width: 100%;"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchAsync"
            :placeholder="$t('form.product')"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('form.stock')" prop="stock">
          <el-input v-model.number="temp.stock" type="number" :placeholder="$t('form.stock')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.sort')" prop="sort">
          <el-input v-model.number="temp.sort" type="number" :placeholder="$t('form.sort')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.current_price')" v-if="temp.current_price">
          <el-input disabled v-model="temp.current_price" type="number" :placeholder="$t('form.current_price')">
            <template slot="append">{{ currency.symbol }}</template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('form.promotion_price')" prop="price_promotion">
          <el-input v-model.number="temp.price_promotion" type="number" :placeholder="currency ? $t('form.please_enter_currency') + currency.code : $t('form.promotion_price')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.sale_date')" prop="rangedate">
          <el-date-picker
            :loading="loadingButtonUpdate"
            v-model="temp.rangedate"
            style="width: 100%"
            type="daterange"
            align="right"
            unlink-panels
            range-separator="To"
            :start-placeholder="$t('form.start_date')"
            :end-placeholder="$t('form.end_date')"
          />
        </el-form-item>

        <el-form-item :label="$t('form.status')">
          <el-switch
            v-model="temp.status_promotion"
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
      :title="$t('form.choose_store_for_flashsale')"
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
import FilterSystemProductFlashsale from './components/FilterSystemProductFlashsale';
import EventBus from '@/components/FileManager/eventBus';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import { checkOnlyStore } from '@/utils';
import reloadRedirectToList from '@/utils';
import Cookies from 'js-cookie';
import ProductResource from '@/api/product';
import ProductFlashsaleResource from '@/api/product-flashsale';
import { parseTime } from '@/filters';

const productFlashsaleResource = new ProductFlashsaleResource();
const productResource = new ProductResource();

const dataForm = {
  id: 0,
  store_id: 0,
  product_name: '',
  product_id: '',
  stock: '',
  sort: '',
  price_promotion: '',
  current_price: '',
  date_start: '',
  date_end: '',
  status_promotion: '',
  rangedate: [],
};

export default {
  name: 'ProductFlashSale',
  components: { Pagination, FilterSystemProductFlashsale, RightPanel },
  directives: { permission },
  data() {
    return {
      list: [],
      total: 0,
      loading: true,
      currency:{},
      temp: Object.assign({}, dataForm),
      rules:{
        product_id: [
          {
            required: true,
            message: 'product is required',
            trigger: 'blur',
          },
        ],
        stock: [
          {
            required: true,
            message: 'stock is required',
            trigger: 'blur',
          },
          {
            type: 'number',
            message: 'stock must be a number',
            trigger: 'blur',
          },
        ],
        sort: [
          {
            required: true,
            message: 'sort is required',
            trigger: 'blur',
          },
          {
            type: 'number',
            message: 'sort must be a number',
            trigger: 'blur',
          },
        ],
        price_promotion: [
          {
            required: true,
            message: 'price promotion is required',
            trigger: 'blur',
          },
          {
            type: 'number',
            message: 'price promotion must be a number',
            trigger: 'blur',
          },
        ],
        rangedate: [
          { type: 'array', required: true, message: 'start date and end date is required', trigger: 'blur' }
        ],
      },
      products: [],
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
    cbGetProduct(res){
      const selectedProd = this.products.filter(prod => prod.id == this.temp.product_id);
      this.temp.product_name = selectedProd[0].name;
      this.temp.current_price = selectedProd[0].price;
    },
    querySearchAsync(queryString, cb) {
      var product = this.products;
      var results = queryString ? product.filter(this.createFilter(queryString)) : product;

      if (results.length === 0) {
        productResource.list({ keyword: queryString, storeId: this.temp.store_id }).then(response => {
          this.products = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetProduct(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        cb(results);
      }
    },
    createFilter(queryString) {
      return (product) => {
        return (product.name.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    handleSelect(item) {
      this.temp.product_id = item.id;
      this.temp.product_name = item.name;
      this.temp.current_price = item.price;
      this.currency = item.currency;
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
          this.temp.date_start = parseTime(this.temp.rangedate[0], '{y}-{m}-{d} {h}:{i}:{s}');
          this.temp.date_end = parseTime(this.temp.rangedate[1], '{y}-{m}-{d} {h}:{i}:{s}');
          productFlashsaleResource.store(this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              reloadRedirectToList('ProductFlashSale');
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
      this.temp.rangedate[0] = new Date(this.temp.rangedate[0]);
      this.temp.rangedate[1] = new Date(this.temp.rangedate[1]);
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-dialog__body',
          });
          this.temp.date_start = parseTime(this.temp.rangedate[0], '{y}-{m}-{d} {h}:{i}:{s}');
          this.temp.date_end = parseTime(this.temp.rangedate[1], '{y}-{m}-{d} {h}:{i}:{s}');
          productFlashsaleResource.update(this.temp.id, this.temp).then((res) => {
            if (res) {
              loading.close();
              this.dialogFormVisible = false;
              this.$message({
                type: 'success',
                message: 'Update successfully',
              });
              reloadRedirectToList('ProductFlashSale');
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
    handleCreate() {
      this.$emit('handleListenCreateForm', true);
    },
    async UpdateForm(row){
      console.log(row);
      this.loadingButtonUpdate = true;
      this.temp.product_id = row.product.id;
      this.temp.store_id = row.product.store.id;
      this.querySearchAsync();
      this.temp.id = row.id;
      this.temp.date_start = row.promotion.date_start;
      this.temp.date_end = row.promotion.date_end;
      this.temp.stock = row.stock;
      this.temp.sort = row.sort;
      this.temp.status_promotion = String(row.promotion.status_promotion);
      this.temp.price_promotion = row.promotion.price_promotion;
      this.temp.rangedate = [this.temp.date_start,this.temp.date_end];
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
