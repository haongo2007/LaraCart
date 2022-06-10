<template>
  <div class="block-tables">
    <div class="el-descriptions__header">
      <div class="el-descriptions__title">{{ $t('form.info_product') }}</div>
      <div class="el-descriptions__extra">
        <el-button type="primary" icon="el-icon-plus" @click="handleAddProduct()" />
      </div>
    </div>
    <el-table
      :data="dataProducts.details"
      max-height="322"
      style="width: 100%"
    >
      <el-table-column
        :label="$t('table.attribute')"
        align="center"
        min-width="80"
      >
        <template slot-scope="scope">
          <el-popover
            v-if="scope.row.attribute"
            placement="top"
            width="400"
            trigger="click"
          >
            <attributes-product 
              :is-new="scope.row.is_new" 
              :data-currency="scope.row.currency" 
              :data-exchange-rate="scope.row.exchange_rate" 
              :data-atribute-group="dataAttributeGroup" 
              :data-attribute="scope.row.attribute" 
              :data-product="scope.row.product_id" 
              @handleAttributeProduct="handleAttributeProduct" />
            <el-button slot="reference" icon="el-icon-s-tools" size="mini" type="primary" @click="setCurrentIndex(scope.$index)" />
          </el-popover>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.name')"
        min-width="220"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.name }}</span>
          <el-autocomplete
            v-else
            v-model="temp[scope.$index].name"
            style="width: 100%;"
            size="mini"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchAsync"
            :placeholder="$t('table.product')"
            @focus="setCurrentIndex(scope.$index)"
            @select="handleSelect"
          />
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.sku')"
        min-width="100"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.sku }}</span>
          <el-input
            v-else
            v-model="temp[scope.$index].sku"
            size="mini"
            :disabled="true"
            :placeholder="$t('table.sku')"
          />
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.price')"
        min-width="150"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.price | toThousandFilter(scope.row.currency) }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].price" size="mini" :controls="false" :min="0" @focus="setCurrentIndex(scope.$index)" @change="handleChangePrice(temp[scope.$index].qty)" />
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.attribute_price')"
        min-width="150"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new" v-html="methodAttributePrice(scope.row.attribute)"></span>
          <span v-else v-html="methodAttributePrice(scope.row.groups)"></span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('table.qty')"
        min-width="80"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.qty }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].qty" style="width:50px;" size="mini" :controls="false" :min="0" @focus="setCurrentIndex(scope.$index)" @change="handleChangePrice(temp[scope.$index].qty)" />
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.total')"
        min-width="100"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.total_price | toThousandFilter(scope.row.currency) }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].total_price" style="width:100px;" size="mini" :disabled="true" :controls="false" :min="0" />
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        :label="$t('table.tax')"
        min-width="80"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ (scope.row.tax.label == '' ? scope.row.tax.value : scope.row.tax) | toThousandFilter(scope.row.currency) }}</span>
          <el-autocomplete
            @focus="setCurrentIndex(scope.$index)" 
            v-model="temp[scope.$index].tax.value"
            style="width: 100%"
            value-key="name"
            class="inline-input"
            :fetch-suggestions="querySearchTaxAsync"
            :placeholder="$t('table.tax')"
            @select="handleSelectTax"
            @change="handleSelectTax"
            v-else
          />
        </template>
      </el-table-column>
      
      <el-table-column
        :label="$t('table.actions')"
        min-width="100"
      >
        <template slot-scope="scope">
          <el-button-group style="width: 100%;">
            <el-button v-if="scope.row.is_new && acceptSave" icon="el-icon-check" size="mini" type="success" :loading="btnLoading" @click="saveProduct(scope.$index)" />
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleteProduct(scope.$index,scope.row.is_new)" />
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import ProductResource from '@/api/product';
import OrdersResource from '@/api/orders';
import AttributesProduct from './AttributesProduct';
import TaxResource from '@/api/tax';
import { changeCurrency } from '@/filters';

const defaultForm = {
  is_new: true,
  id: '',
  order_id: '',
  name: '',
  sku: '',
  price: 0,
  qty: 0,
  total_price: 0,
  tax: {
        label: '',
        value: '0',
      },
  currency: '',
};
const taxResource = new TaxResource();
const productResource = new ProductResource();
const ordersResource = new OrdersResource();
export default {
  name: 'InfoProduct',
  components: {
    AttributesProduct,
  },
  props: ['dataProducts', 'dataAttributeGroup'],
  data() {
    return {
      products: [],
      temp: [],
      currentIndex: '',
      acceptSave: false,
      counting: 0,
      dataAttribute: [],
      btnLoading: false,
      taxs: [],
      taxsLoading: true,
    };
  },
  created(){
    const that = this;
    this.dataProducts.details.forEach(function(i, v) {
      i.groups = [];
      that.temp.push(i);
    });
  },
  methods: {
    handleAddProduct(){
      console.log(defaultForm);
      const newval = Object.assign({}, defaultForm);
  		this.dataProducts.details.push(newval);
  		this.temp.push(newval);
  		this.acceptSave = true;
  		this.counting++;
    },
    querySearchAsync(queryString, cb) {
      var product = this.products;
      var results = queryString ? product.filter(this.createFilter(queryString)) : product;

      if (results.length == 0) {
        productResource.list({ keyword: queryString,storeId:this.dataProducts.stores.id }).then(response => {
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
    	const index = this.currentIndex;
    	this.$set(this.temp[index],'product_id',item.id);
    	this.$set(this.temp[index],'order_id', this.$route.params.id);
    	this.$set(this.temp[index],'sku', item.sku);
    	this.$set(this.temp[index],'price', item.price * this.dataProducts.exchange_rate);
    	this.$set(this.temp[index],'qty', 1);
    	this.$set(this.temp[index],'total_price', item.price * this.dataProducts.exchange_rate);
    	this.$set(this.temp[index],'attribute', {...item.attributes});
    	this.$set(this.temp[index],'currency', this.dataProducts.currency);
      this.$set(this.temp[index],'exchange_rate', this.dataProducts.exchange_rate);
    	this.$set(this.temp[index],'groups',[]);
      this.temp[index].tax = {
        label: '',
        value: String( (item.price * item.tax / 100) * this.dataProducts.exchange_rate ),
      }
    },
    setCurrentIndex(index){
    	this.currentIndex = index;
    },
    handleChangePrice(qty){
      let sumVariant = 0;
      if (this.temp[this.currentIndex].hasOwnProperty('groups')) {
        this.temp[this.currentIndex].groups.forEach((item) => {
          let key = Object.keys(item.text)[0];
          item = item.text[key].split('__');
          sumVariant += parseInt(item[1]);
        });
      }
      let old_total = this.temp[this.currentIndex].total_price / this.dataProducts.exchange_rate;
      this.temp[this.currentIndex].total_price = (( (this.temp[this.currentIndex].price / this.dataProducts.exchange_rate) + sumVariant) * qty) * this.dataProducts.exchange_rate;

      if (this.temp[this.currentIndex].tax.hasOwnProperty('value') && this.temp[this.currentIndex].tax.value > 0) {
        let tax_percent = old_total / this.temp[this.currentIndex].tax.value;
        this.temp[this.currentIndex].tax.value = String( (this.temp[this.currentIndex].total_price * tax_percent / 100) * this.dataProducts.exchange_rate ) ;
      }
    },
    handleDeleteProduct(row, is_new = false){
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
      	if (this.temp[row].id) {
	      	ordersResource.deleteItem({ id: this.temp[row].id }).then((res) => {
            if (res) {
              this.dataProducts.details.splice(row, 1);
			      	this.temp.splice(row, 1);
			      	if (is_new) {
				    		--this.counting;
			      	}
			      	if (this.counting == 0) {
			      		this.acceptSave = false;
			      	}
			        this.$message({
			          type: 'success',
			          message: 'Delete successfully',
			        });
		        	this.$emit('handleChangeInvoice', res.data.invoice);
		        	this.$emit('handleChangeHistory', res.data.history);
			      }
          }).catch(() => {
		        this.$message({
		          type: 'info',
		          message: 'Delete canceled',
		        });
		      });
	      } else {
          this.dataProducts.details.splice(row, 1);
	      	this.temp.splice(row, 1);
	      }
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleAttributeProduct(obj){
      const that = this;
      let index = this.currentIndex;
      if (this.temp[index].groups.length == 0) {
        this.temp[index].groups.push(obj);
      } else {
        this.temp[index].groups.forEach((item,key) => {
          if (item.group == obj.group) {
            that.temp[index].groups.splice(key);
            that.temp[index].groups.push(obj);
          }else{
            that.temp[index].groups.push(obj);
          }
        })
        
      }
      this.handleChangePrice(this.temp[index].qty);
    },
    saveProduct(index){
      this.btnLoading = true;
      if (!this.temp[index].product_id || ('groups' in this.temp[index] && this.temp[index].groups.length == 0)) {
        this.$message({
          type: 'error',
          message: 'You forgot to enter the some value !',
        });
      	this.btnLoading = false;
      	return false;
      }
      var text = [];
      this.temp[index].is_new = false;
      this.temp[index].groups.forEach(function(val, index) {
        text = { ...text, ...val.text };
      });
      this.temp[index].attribute = JSON.stringify([text]);
      ordersResource.addMoreItem(this.temp[index]).then((res) => {
        if (res.success) {
	        this.$message({
	          type: 'success',
	          message: 'Save successfully',
	        });
	        this.temp[index].id = res.data.order_detail_id;
        	this.$emit('handleChangeInvoice', res.data.invoice);
        	this.$emit('handleChangeHistory', res.data.history);
	      }
        this.btnLoading = false;
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Save canceled',
        });
      });
    },
    methodAttributePrice(att){
      let result = '';
      if (typeof att == 'object') {
        for(let prop in att) {
          let key = Object.keys(att[prop].text);
          let item = att[prop].text[key].split('__');
          result += this.dataAttributeGroup[key]+': '+item[0]+' - '+ changeCurrency(item[1],this.dataProducts.exchange_rate,this.dataProducts.currency)+'<br>';
        }
      }else if(typeof att == 'undefined'){
        return;
      }else{
        att = att.replace(/&quot;/g, "\"");
        let reAtt = JSON.parse(att);
        for(let props in reAtt){
          for(let prop in reAtt[props]) {
            let item = reAtt[props][prop].split('__');
            result += this.dataAttributeGroup[prop]+': '+item[0]+'<span class="el-badge__content el-badge__content--warning">'+ changeCurrency(item[1],this.dataProducts.exchange_rate,this.dataProducts.currency) +'</span><br>';
          }
        }
      }
      return result;
    },
    createTaxFilter(queryString) {
      return (data) => {
        return (data.name.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    querySearchTaxAsync(queryString, cb){
      var taxs = this.taxs;
      var results = queryString ? taxs.filter(this.createTaxFilter(queryString)) : taxs;

      if (results.length == 0) {
        taxResource.list({ keyword: queryString }).then(response => {
          this.taxs = response.data;
          results = response.data;
        })
        .catch(err => {
          console.log(err);
        });
      }      
      cb(results);
    },
    handleSelectTax(item) {
      let val = item;
      if (item.hasOwnProperty('value')) {
        val = item.value;
      }
      let subtotal = this.temp[this.currentIndex].total_price;
      let qty = this.temp[this.currentIndex].qty;
      this.temp[this.currentIndex].tax.value = String(subtotal * val / 100);
    },
  },
};
</script>
