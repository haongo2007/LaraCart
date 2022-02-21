<template>
  <div>
    <el-skeleton :rows="20" animated :loading="loading" />
    <el-row v-show="!loading">
      <el-form ref="dataGeneralForm" :model="temp" :rules="rules" class="form-container" label-width="150px">
        <el-col :span="12">

          <el-form-item :label="$t('table.cost')" prop="cost">
            <el-input-number v-model="temp.cost" style="width: 100%" :controls="false" :min="1" />
          </el-form-item>

          <el-form-item :label="$t('table.price')" prop="price">
            <el-input-number v-model="temp.price" style="width: 100%" :controls="false" :min="1" />
          </el-form-item>

          <el-form-item :label="$t('table.tax')" prop="tax.label">
            <el-autocomplete
              v-model="temp.tax.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchTaxAsync"
              placeholder="Please Input"
              @select="handleSelectTax"
            />
          </el-form-item>

          <el-form-item :label="$t('table.quantily')" prop="quantily">
            <el-input-number v-model="temp.quantily" style="width: 100%" :controls="false" />
          </el-form-item>

          <el-form-item :label="$t('table.mi_quantity')" prop="minimum">
            <el-input-number v-model="temp.minimum" style="width: 100%" :controls="false" />
          </el-form-item>

          <el-form-item :label="$t('table.url_config')" prop="alias">
            <el-input
              v-model="temp.alias"
              placeholder="Please input"
              clearable
            />
          </el-form-item>

        </el-col>
        <el-col :span="12">

          <el-form-item :label="$t('table.brand')" prop="brand">
            <el-autocomplete
              v-model="temp.brand.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchBrandAsync"
              placeholder="Please Input"
              @select="handleSelectBrand"
            />
          </el-form-item>

          <el-form-item :label="$t('table.supplier')" prop="supplier">
            <el-autocomplete
              v-model="temp.supplier.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchSupplierAsync"
              placeholder="Please Input"
              @select="handleSelectSup"
            />
          </el-form-item>

          <el-form-item :label="$t('table.sku')" prop="sku">
            <el-input
              v-model="temp.sku"
              style="width: 100%"
              placeholder="Please input"
              clearable
            />
          </el-form-item>

          <el-form-item :label="$t('table.category')" prop="category">
            <el-cascader style="width: 100%" v-model="temp.category" :props="cateRecurProps" clearable></el-cascader>
          </el-form-item>

          <el-form-item :label="$t('table.sell_date')" prop="date_available">
            <el-date-picker
              v-model="date_available"
              style="width: 100%"
              type="datetime"
              placeholder="Select date and time for available sale"
              @change="handleFilterDate()"
              :picker-options="pickerOptions">
            </el-date-picker>
          </el-form-item>
          <el-form-item :label="$t('table.stock')" prop="stock">
            <el-input
              v-model="temp.stock"
              placeholder="Please input"
              clearable
            />
          </el-form-item>

          <el-button-group class="pull-right">
            <el-button type="warning" icon="el-icon-arrow-left" @click="backStep">
              Previous
            </el-button>
            <el-button type="primary" icon="el-icon-arrow-right" @click="nextStep">
              Next
            </el-button>
          </el-button-group>
        </el-col>
      </el-form>
    </el-row>
  </div>
</template>

<script>
import {parseTime} from '@/filters';
import CategoryResource from '@/api/category';
import BrandResource from '@/api/brand';
import SupplierResource from '@/api/supplier';
import TaxResource from '@/api/tax';

const categoryResource = new CategoryResource();
const brandResource = new BrandResource();
const supplierResource = new SupplierResource();
const taxResource = new TaxResource();

let category_parent = 0;

export default {
  name: 'InfoGeneral',
  props: ['dataActive','dataRefs'],
  data() {
    return {
      temp: {
        category: '',
        sku: '',
        brand:{
          label:'',
          value:0
        },
        supplier:{
          label:'',
          value:0
        },
        price: 0,
        cost: 0,
        tax:{
          label:'',
          value:0
        },
        quantily: 0,
        minimum: 0,
        date_available: '',
        stock: 0,
        alias: '',
      },
      date_available:'',
      loading: true,
      brands: [],
      suppliers: [],
      taxs: [],
      cateRecurProps: {
        checkStrictly: true, 
        lazy: true,
        lazyLoad (node, resolve) {
          var level = node.value;
          if (!node.value) {
            level = category_parent;
          }
          categoryResource.list({parent:level}).then((res) => {
            const nodes = res.data.map(item => ({
                value: item.id,
                label: item.name,
                leaf: item.parent
              }));
            resolve(nodes);
          });
        }
      },
      listRecursive: [{
        id: '0',
        parent: 0,
        title: 'Is parent',
      }],
      pickerOptions: {
        shortcuts: [{
          text: 'Today',
          onClick(picker) {
            picker.$emit('pick', new Date());
          },
        }, {
          text: 'Tomorrow',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() + 3600 * 1000 * 24);
            picker.$emit('pick', date);
          },
        }],
      },
      rules: {
        cost: [
          {
            required: true,
            message: 'cost is required',
            trigger: 'blur',
          },
        ],
        price: [
          {
            required: true,
            message: 'price is required',
            trigger: 'change',
          },
        ],
        sku: [
          {
            required: true,
            message: 'sku is required',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  created() {
    this.getRecursive();
  },
  methods: {
    backStep() {
      const active = this.dataActive - 1;
      this.$emit('handleProcessActive', active);
    },
    nextStep() {    
      this.$refs['dataGeneralForm'].validate((valid) => {
        if (valid) {
          const active = this.dataActive + 1;
          this.$emit('handleProcessActive', active);
          this.$emit('handleProcessTemp', this.temp);
        }
      })
    },
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
    handleSelectTax(item) {
      this.temp.tax.value = item.id;
    },
    handleSelectSup(item) {
      this.temp.supplier.value = item.id;
    },
    handleSelectBrand(item) {
      this.temp.brand.value = item.id;
    },
    handleFilterDate(){
      this.temp.date_available = parseTime(this.date_available.toString(), '{d}-{m}-{y} {h}:{i}:{s}');
    },
  },
};
</script>
