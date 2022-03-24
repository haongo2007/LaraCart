<template>
  <div class="block-tables">
    <div class="el-descriptions__header">
      <div class="el-descriptions__title">Info Product</div>
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
        label="Attributes"
        align="center"
        min-width="70"
      >
        <template slot-scope="scope">
          <el-popover
            v-if="scope.row.attribute"
            placement="top"
            width="400"
            trigger="click"
          >
            <attributes-product :is-new="scope.row.is_new" :data-currency="scope.row.currency" :data-atribute-group="dataAttributeGroup" :data-attribute="scope.row.attribute" @handleAttributeProduct="handleAttributeProduct" />
            <el-button slot="reference" icon="el-icon-s-tools" size="mini" type="primary" />
          </el-popover>
        </template>
      </el-table-column>
      <el-table-column
        label="Name"
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
            placeholder="Please Input"
            @focus="setCurrentIndex(scope.$index)"
            @select="handleSelect"
          />
        </template>
      </el-table-column>
      <el-table-column
        label="Sku"
        min-width="100"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.sku }}</span>
          <el-input
            v-else
            v-model="temp[scope.$index].sku"
            size="mini"
            :disabled="true"
            placeholder="Please input"
          />
        </template>
      </el-table-column>
      <el-table-column
        label="Price"
        min-width="150"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.price | toThousandFilter }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].price" size="mini" :controls="false" :min="0" @focus="setCurrentIndex(scope.$index)" @change="handleChangePrice('price',temp[scope.$index].price)" />
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Qty"
        min-width="50"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.qty }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].qty" style="width:50px;" size="mini" :controls="false" :min="0" @focus="setCurrentIndex(scope.$index)" @change="handleChangePrice('qty',temp[scope.$index].qty)" />
        </template>
      </el-table-column>
      <el-table-column
        label="Total"
        min-width="100"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.total_price | toThousandFilter }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].total_price" style="width:100px;" size="mini" :disabled="true" :controls="false" :min="0" />
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Tax"
        min-width="80"
      >
        <template slot-scope="scope">
          <span v-if="!scope.row.is_new">{{ scope.row.tax }}</span>
          <el-input-number v-else v-model.number="temp[scope.$index].tax" style="width:80px;" :controls="false" size="mini" :min="0" @focus="setCurrentIndex(scope.$index)" />
        </template>
      </el-table-column>
      <el-table-column
        label="Action"
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

const defaultForm = {
  is_new: true,
  id: '',
  order_id: '',
  name: '',
  sku: '',
  price: 0,
  qty: 0,
  total_price: 0,
  tax: 0,
  currency: '',
};
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
        productResource.list({ keyword: queryString }).then(response => {
          this.products = [...this.products, ...response.data];
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
    	this.temp[index].product_id = item.id;
    	this.temp[index].order_id = this.$route.params.id;
    	this.temp[index].sku = item.sku;
    	this.temp[index].price = item.price;
    	this.temp[index].qty = 1;
    	this.temp[index].total_price = item.price;
    	this.temp[index].tax = item.tax;
    	this.temp[index].attribute = item.attributes;
    	this.temp[index].currency = this.dataProducts.currency;
    	this.temp[index].groups = [];
    },
    setCurrentIndex(index){
    	this.currentIndex = index;
    },
    handleChangePrice(type, val){
    	if (type == 'qty'){
    		this.temp[this.currentIndex].total_price = val * this.temp[this.currentIndex].price;
    	} else if (type == 'price'){
    		this.temp[this.currentIndex].total_price = this.temp[this.currentIndex].qty * this.temp[this.currentIndex].price;
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
      this.temp.forEach(function(val, index) {
        if (val.product_id == obj.prd) {
          if (that.temp[index].groups.length == 0) {
            that.temp[index].groups.push(obj);
          } else {
            that.temp[index].groups.forEach(function(v, i) {
              if (obj.group == v.group) {
                that.temp[index].groups.splice(i);
                that.temp[index].groups.push(obj);
              } else {
                that.temp[index].groups.push(obj);
              }
            });
          }
        }
      });
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
      this.temp[index].attribute = JSON.stringify(text);
      ordersResource.addMoreItem(this.temp[index]).then((res) => {
        if (res) {
	        this.$message({
	          type: 'success',
	          message: 'Save successfully',
	        });
	        this.temp[index].id = res.data.order_detail_id;
		      this.btnLoading = false;
        	this.$emit('handleChangeInvoice', res.data.invoice);
        	this.$emit('handleChangeHistory', res.data.history);
	      }
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Save canceled',
        });
      });
    },
  },
};
</script>
