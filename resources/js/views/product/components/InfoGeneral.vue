<template>
  <div>
    <el-skeleton :rows="20" animated :loading="loading" />
    <el-row v-show="!loading">
      <el-col :span="12">

        <el-form-item :label="$t('table.cost')" prop="cost">
          <el-input-number v-model="temp.cost" style="width: 80%" :controls="false" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('table.price')" prop="price">
          <el-input-number v-model="temp.price" style="width: 80%" :controls="false" :min="1" />
        </el-form-item>

        <el-form-item :label="$t('table.tax')" prop="tax">
          <el-autocomplete
            v-model="temp.tax"
            style="width: 80%"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchTaxAsync"
            placeholder="Please Input"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('table.quantily')" prop="quantily">
          <el-input-number v-model="temp.quantily" style="width: 80%" :controls="false" />
        </el-form-item>

        <el-form-item :label="$t('table.mi_quantity')" prop="mi_quantity">
          <el-input-number v-model="temp.mi_quantity" style="width: 80%" :controls="false" />
        </el-form-item>

      </el-col>
      <el-col :span="12">

        <el-form-item :label="$t('table.brand')" prop="brand">
          <el-autocomplete
            v-model="temp.brand"
            style="width: 80%"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchBrandAsync"
            placeholder="Please Input"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('table.supplier')" prop="supplier">
          <el-autocomplete
            v-model="temp.supplier"
            style="width: 80%"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchSupplierAsync"
            placeholder="Please Input"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('table.sku')" prop="sku">
          <el-input
            v-model="temp.sku"
            style="width: 80%"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('table.category')" prop="category">
          <el-cascader
            v-model="temp.category"
            style="width: 80%"
            :options="listRecursive"
            :props="cateRecurProps"
            :show-all-levels="false"
            clearable
            filterable
          />
        </el-form-item>

        <el-form-item :label="$t('table.sell_date')" prop="sell_date">
          <el-date-picker
            v-model="temp.arDateToSell"
            style="width: 80%"
            type="datetimerange"
            align="right"
            unlink-panels
            range-separator="To"
            start-placeholder="Created date"
            end-placeholder="End date"
            :picker-options="pickerOptions"
            @change="handleFilterDate()"
          />
        </el-form-item>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import CategoryResource from '@/api/category';
import BrandResource from '@/api/brand';
import SupplierResource from '@/api/supplier';
import TaxResource from '@/api/tax';

const categoryResource = new CategoryResource();
const brandResource = new BrandResource();
const supplierResource = new SupplierResource();
const taxResource = new TaxResource();
export default {
  name: 'InfoGeneral',
  data() {
    return {
      temp: {
        category: '0',
        sku: '',
        brand: '',
        supplier: '',
        price: 0,
        cost: 0,
        tax: '',
        quantily: 0,
        mi_quantity: 0,
        arDateToSell: [],
      },
      loading: true,
      brands: [],
      suppliers: [],
      taxs: [],
      cateRecurProps: {
        children: 'children',
        label: 'title',
        value: 'id',
        checkStrictly: true,
      },
      listRecursive: [{
        id: '0',
        parent: 0,
        title: 'Is parent',
      }],
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          },
        }],
      },
    };
  },
  created() {
    this.getRecursive();
  },
  methods: {
    async getRecursive(id){
      const { data } = await categoryResource.getRecursive(id);
      data.unshift(this.listRecursive[0]);
      this.listRecursive = data;
      this.loading = false;
    },
    querySearchBrandAsync(queryString, cb) {
      var brands = this.brands;
      var results = queryString ? brands.filter(this.createFilter(queryString)) : brands;

      if (results.length == 0) {
        brandResource.list({ keyword: queryString }).then(response => {
          this.brands = [...this.brands, ...response.data];
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
    querySearchSupplierAsync(queryString, cb) {
      var suppliers = this.suppliers;
      var results = queryString ? suppliers.filter(this.createFilter(queryString)) : suppliers;

      if (results.length == 0) {
        supplierResource.list({ keyword: queryString }).then(response => {
          this.suppliers = [...this.suppliers, ...response.data];
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
    querySearchTaxAsync(queryString, cb){
      var taxs = this.taxs;
      var results = queryString ? taxs.filter(this.createFilter(queryString)) : taxs;

      if (results.length == 0) {
        taxResource.list({ keyword: queryString }).then(response => {
          this.taxs = [...this.taxs, ...response.data];
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
      return (data) => {
        return (data.name.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    handleSelect(item) {
    },
    handleFilterDate(){

    },
  },
};
</script>
