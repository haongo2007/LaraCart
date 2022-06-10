<template>
  <div>
    <el-skeleton :rows="20" animated :loading="loading" />
    <el-form ref="dataGeneralForm" :model="temp" :rules="rules" class="form-container" label-width="150px">
      <el-row v-show="!loading" class="el-main-form">
        <el-col :span="12">

          <el-form-item :label="$t('form.cost')" prop="cost">
            <el-input-number v-model="temp.cost" style="width: 100%" :controls="false" :min="1" />
          </el-form-item>

          <el-form-item :label="$t('form.price')" prop="price">
            <el-input-number v-model="temp.price" style="width: 100%" :controls="false" :min="1" />
          </el-form-item>

          <el-form-item :label="$t('form.tax')" prop="tax.label">
            <el-autocomplete
              v-model="temp.tax.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchTaxAsync"
              :placeholder="$t('form.tax')"
              @select="handleSelectTax"
            />
          </el-form-item>

          <el-form-item :label="$t('form.mi_quantity')" prop="minimum">
            <el-input-number v-model="temp.minimum" style="width: 100%" :controls="false" :min="1" />
          </el-form-item>

          <el-form-item :label="$t('form.stock')" prop="stock">
            <el-input
              v-model="temp.stock"
              :placeholder="$t('form.mi_quantity')"
              clearable
            />
          </el-form-item>

        </el-col>
        <el-col :span="12">

          <el-form-item :label="$t('form.brand')" prop="brand">
            <el-autocomplete
              v-model="temp.brand.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchBrandAsync"
              :placeholder="$t('form.brand')"
              @select="handleSelectBrand"
            />
          </el-form-item>

          <el-form-item :label="$t('form.supplier')" prop="supplier">
            <el-autocomplete
              v-model="temp.supplier.label"
              style="width: 100%"
              value-key="name"
              class="inline-input"
              :fetch-suggestions="querySearchSupplierAsync"
              :placeholder="$t('form.supplier')"
              @select="handleSelectSup"
            />
          </el-form-item>

          <el-form-item :label="$t('form.sku')" prop="sku">
            <el-input
              v-model="temp.sku"
              style="width: 100%"
              :placeholder="$t('form.sku')"
              clearable
            />
          </el-form-item>

          <!-- <el-form-item :label="$t('table.category')" prop="category">
            <el-cascader v-model="temp.category" style="width: 100%" :props="cateRecurProps" clearable />
          </el-form-item> -->

          <el-form-item :label="$t('form.sell_date')" prop="date_available">
            <el-date-picker
              v-model="date_available"
              style="width: 100%"
              type="datetime"
              :placeholder="$t('form.sell_date')"
              :picker-options="pickerOptions"
              @change="handleFilterDate()"
            />
          </el-form-item>

          <el-form-item :label="$t('form.url_config')" prop="alias">
            <el-input
              v-model="temp.alias"
              :placeholder="$t('form.url_config')"
              clearable
            />
          </el-form-item>

        </el-col>
        <el-col :span="24">
          <el-form-item :label="$t('form.category')" prop="category">
            <category-multiple :store-id="parseInt(dataStoreId)" :data-temp-multiple="temp.category ? temp.category : []" @handleProcessCategory="handleProcessCategory" :is-multiple="true" 
            :is-edit="isEdit" />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <el-row>
      <el-button-group class="pull-right">
        <el-button type="warning" icon="el-icon-arrow-left" @click="backStep">
          {{ $t('form.prev') }}
        </el-button>
        <el-button type="primary" icon="el-icon-arrow-right" @click="nextStep">
          {{ $t('form.next') }}
        </el-button>
      </el-button-group>
    </el-row>
  </div>
</template>

<script>
import { parseTime } from '@/filters';
import CategoryResource from '@/api/category';
import BrandResource from '@/api/brand';
import SupplierResource from '@/api/supplier';
import TaxResource from '@/api/tax';
import CategoryMultiple from '@/components/CategoryMultiple';

const categoryResource = new CategoryResource();
const brandResource = new BrandResource();
const supplierResource = new SupplierResource();
const taxResource = new TaxResource();

const category_parent = 0;

export default {
  name: 'InfoGeneral',
  components:{
    CategoryMultiple
  },
  props: ['dataActive', 'dataProduct','dataStoreId'],
  data() {
    return {
      temp: {
        category: '',
        sku: '',
        brand: {
          label: '',
          value: 0,
        },
        supplier: {
          label: '',
          value: 0,
        },
        price: 0,
        cost: 0,
        tax: {
          label: '',
          value: 0,
        },
        date_available: '',
        stock: 0,
        alias: '',
      },
      isEdit:Object.keys(this.dataProduct).length > 0 ? true : false,
      minimum: 0,
      date_available: '',
      loading: true,
      brands: [],
      suppliers: [],
      taxs: [],
      // cateRecurProps: {
      //   checkStrictly: true,
      //   multiple: true,
      //   store_id:this.dataStoreId,
      //   lazy: true,
      //   lazyLoad(node, resolve) {
      //     var level = node.value;
      //     if (!node.value) {
      //       level = category_parent;
      //     }
      //     categoryResource.list({ parent: level,store_id:this.store_id }).then((res) => { 
      //       const nodes = res.data.map(item => ({
      //         value: item.id,
      //         label: item.name,
      //         leaf: item.hasOwnProperty('hasChildren') && item.hasChildren == true ? item.id : level,
      //       }));
      //       resolve(nodes);
      //     });
      //   },
      // },
      // listRecursive: [{
      //   id: '0',
      //   parent: 0,
      //   title: 'Is parent',
      // }],
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
    // this.getRecursive();
    if (Object.keys(this.dataProduct).length > 0) {
      this.temp.sku = this.dataProduct.sku;

      if (this.dataProduct.brand_id) {
        this.querySearchBrandAsync();
      }
      if (this.dataProduct.supplier_id) {
        this.querySearchSupplierAsync();
      }
      if (this.dataProduct.tax_id) {
        this.querySearchTaxAsync();
      }
      if (this.dataProduct.categories) {
        let categories = [];
        this.dataProduct.categories.forEach(function(v, i) {
          categories.push(parseInt(v.id));
        });
        categories = [...new Set(categories)];

        this.temp.category = categories;
      }
      this.temp.price = this.dataProduct.price;
      this.temp.cost = this.dataProduct.cost;
      this.temp.minimum = this.dataProduct.minimum;
      this.temp.date_available = this.dataProduct.date_available ? parseTime(this.dataProduct.date_available.toString(), '{d}-{m}-{y} {h}:{i}:{s}') : '';
      this.date_available = this.dataProduct.date_available ? parseTime(this.dataProduct.date_available.toString(), '{y}-{m}-{d} {h}:{i}:{s}') : '';
      this.temp.stock = this.dataProduct.stock;
      this.temp.alias = this.dataProduct.alias;
    } else {
      this.loading = false;
    }
  },
  methods: {
    handleProcessCategory(data){
      let ids = data.map((item, index) => {
        return item.map((item1, index1) => {
          return item1.id;
        });
      });
      this.temp.category = ids;
    },
    cbGetBrands(res) {
      const selectedBrand = this.brands.filter(brand => brand.id == this.dataProduct.brand_id);
      this.temp.brand.label = selectedBrand[0].name;
      this.temp.brand.value = this.dataProduct.brand_id;
    },
    cbGetSupplier(res){
      const selectedSupplier = this.suppliers.filter(supplier => supplier.id == this.dataProduct.supplier_id);
      this.temp.supplier.label = selectedSupplier[0].name;
      this.temp.supplier.value = this.dataProduct.supplier_id;
    },
    cbGetTax(res){
      const selectedTax = this.taxs.filter(tax => tax.id == this.dataProduct.tax_id);
      if (selectedTax.length > 0) {
        this.temp.tax.label = selectedTax[0].name;
        this.temp.tax.value = this.dataProduct.tax_id;
      }
      this.loading = false;
    },
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
      });
    },
    // async getRecursive(id){
    //   const { data } = await categoryResource.getRecursive(id);
    //   data.unshift(this.listRecursive[0]);
    //   this.listRecursive = data;

    //   if (Object.keys(this.dataProduct).length == 0) {
    //     this.loading = false;
    //   }
    // },
    async querySearchBrandAsync(queryString, cb) {
      var brands = this.brands;
      var results = queryString ? brands.filter(this.createFilter(queryString)) : brands;

      if (results.length == 0) {
        brandResource.list({ keyword: queryString }).then(response => {
          this.brands = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetBrands(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        if (cb) {
          cb(results);
        }
      }
    },
    querySearchSupplierAsync(queryString, cb) {
      var suppliers = this.suppliers;
      var results = queryString ? suppliers.filter(this.createFilter(queryString)) : suppliers;

      if (results.length == 0) {
        supplierResource.list({ keyword: queryString }).then(response => {
          this.suppliers = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetSupplier(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        if (cb) {
          cb(results);
        }
      }
    },
    querySearchTaxAsync(queryString, cb){
      var taxs = this.taxs;
      var results = queryString ? taxs.filter(this.createFilter(queryString)) : taxs;

      if (results.length == 0) {
        taxResource.list({ keyword: queryString }).then(response => {
          this.taxs = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetTax(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        if (cb) {
          cb(results);
        }
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
