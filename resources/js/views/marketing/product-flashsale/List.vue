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
              @click="$router.push({ name: 'UserEdit',params:{id:row.id} })"
            />
            <el-button v-permission="['delete.flashsale']" type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
    <el-dialog :title="$t('form.'+dialogStatus)" :visible.sync="dialogFormVisible" :before-close="handleReset" class="dialog-custom">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 75%; margin:0 auto;">

        <el-form-item :label="$t('form.product')" prop="product">
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

        <el-form-item :label="$t('form.price')" prop="price">
          <el-input v-model.number="temp.price" type="number" :placeholder="$t('form.price')" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('form.sale_date')" prop="sale_date">
          <el-date-picker
            v-model="temp.sale_date"
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
import Cookies from 'js-cookie';
import ProductResource from '@/api/product';

const productResource = new ProductResource();

const dataForm = {
  id: 0,
  store_id: 0,
  product_name: '',
  product_id: '',
  stock: '',
  sort: '',
  price: '',
  sale_date: '',
  status: '',
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
      temp: Object.assign({}, dataForm),
      rules: {},
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
        role: '',
      },
      loadingButtonCreate: false,
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
    querySearchAsync(queryString, cb) {
      var product = this.products;
      var results = queryString ? product.filter(this.createFilter(queryString)) : product;

      if (results.length === 0) {
        productResource.list({ keyword: queryString, storeId: this.temp.store_id }).then(response => {
          this.products = response.data;
          results = response.data;
          cb(results);
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
      console.log(this.temp);
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
      console.log(this.temp);
    },
    updateData(){},
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
