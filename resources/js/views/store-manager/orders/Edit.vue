<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:0px;">
      <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
        <el-page-header @back="goBackList" :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' )"/>
          <div slot="title"> 
            <el-tag
              size="medium"
              type="success"
              effect="dark">
              <i class="el-icon-s-shop"></i>
              {{ Object.keys(info_order).length ? info_order.stores.descriptions_current_lang[0].title : '' }}
            </el-tag>
          </div>
        </el-page-header>
        <div>
          <el-button-group>
            <el-button type="success" @click="exportToExcel"><svg-icon icon-class="excel" /></el-button>
            <el-button type="primary" @click="printToPdf"><svg-icon icon-class="pdf" /></el-button>
          </el-button-group>
        </div>
      </div>
    </el-row>
    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-col :span="12" class="toPrint">
        <el-skeleton :rows="6" animated :loading="loading" />
        <component :is="customer_component" v-show="!loading" :data-order="info_order" :data-country="listCountry" @handleChangeHistory="handleChangeHistory" />
      </el-col>
      <el-col :span="12" class="toPrint">
        <el-skeleton :rows="6" animated :loading="loading" />
        <component
          :is="order_component"
          v-show="!loading"
          :data-order="info_order"
          :data-order-status="statusOrder"
          :data-shipping-status="statusShipping"
          :data-payment-status="statusPayment"
          :data-payment-method="paymentMethod"
          :data-shipping-method="shippingMethod"
          @handleChangeHistory="handleChangeHistory"
        />
      </el-col>
    </el-row>

    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-col :span="24">
        <el-skeleton :rows="6" animated :loading="loading" />
        <component :is="products_component" v-show="!loading" :data-attribute-group="dataAttributeGroup" :data-products="info_order" @handleChangeHistory="handleChangeHistory" @handleChangeInvoice="handleChangeInvoice" />
      </el-col>
    </el-row>
    
    <el-row :gutter="20" style="margin:0px;">
      <el-col :span="8" class="toPrint">
        <el-skeleton :rows="6" animated :loading="loading" />
        <component :is="invoice_component" v-show="!loading" :data-total="info_order" :data-total-updated="order_total" @handleChangeHistory="handleChangeHistory" />
      </el-col>

      <el-col :span="16">
        <el-skeleton :rows="6" animated :loading="loading" />
        <component :is="history_component" v-show="!loading" :data-history="dataHistory" />
      </el-col>
    </el-row>
  </div>
</template>

<script>
import OrdersResource from '@/api/orders';
import InfoCustomer from './components/InfoCustomer';
import InfoOrder from './components/InfoOrder';
import InfoProduct from './components/InfoProduct';
import Invoice from './components/Invoice';
import OrdersHistory from './components/OrdersHistory';

const ordersResource = new OrdersResource();

export default {
  name: 'OrderEdit',
  components: {
  	InfoCustomer,
  	InfoOrder,
  	InfoProduct,
  	Invoice,
  	OrdersHistory,
  },
  data() {
    return {
    	loading: true,
    	info_order: {},
    	customer_component: '',
      listCountry: {},
    	order_component: '',
    	statusOrder: {},
    	statusPayment: {},
    	statusShipping: {},
      shippingMethod: {},
      paymentMethod: {},
    	products_component: '',
    	invoice_component: '',
    	history_component: '',
    	dataHistory: {},
      dataAttributeGroup: {},
      order_total: {},
    };
  },
  created() {
  	this.getOrder(this.$route.params.id);
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'OrdersList' });
    },
    async getOrder(id){
    	const { data } = await ordersResource.get(id);
    	this.info_order = data.order;
      this.listCountry = data.countries;
    	this.statusOrder = data.statusOrder;
    	this.statusPayment = data.statusPayment;
    	this.statusShipping = data.statusShipping;
      this.shippingMethod = data.shippingMethod;
      this.paymentMethod = data.paymentMethod;
    	this.dataHistory = data.order.history;
      this.dataAttributeGroup = data.attributesGroup;

    	this.customer_component = 'InfoCustomer';
    	this.order_component = 'InfoOrder';
    	this.products_component = 'InfoProduct';
    	this.invoice_component = 'Invoice';
    	this.history_component = 'OrdersHistory';
    	this.loading = false;
    },
    handleChangeHistory(data){
      this.dataHistory.unshift(data);
    },
    handleChangeInvoice(data){
      this.order_total = data;
    },
    printToPdf(){
      var html = '';
      const that = this;
      Object.keys(this.$el.getElementsByClassName('toPrint')).forEach(function(k) {
        html += that.$el.getElementsByClassName('toPrint')[k].innerHTML + '<br>';
      });
      html = html.replace(/^\s+|\s+$/gm, '');
      localStorage.setItem('dataPdf', JSON.stringify({ title: 'Order #' + this.$route.params.id, content: html }));
      const routeData = this.$router.resolve('/pdf/download');
      window.open(routeData.href, '_blank');
    },
    exportToExcel(){
      ordersResource.downloadExcel(this.$route.params.id, 'invoice').then((res) => {
        var fileURL = window.URL.createObjectURL(new Blob([res]));
        var fileLink = document.createElement('a');
        fileLink.href = fileURL;
        fileLink.setAttribute('download', 'Order #' + this.$route.params.id + '.xls');
        document.body.appendChild(fileLink);
        fileLink.click();
        fileLink.remove();
      });
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
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    vertical-align: bottom;
  }
  .block-tables{
  	padding: 24px;
  	border: 1px solid #ebebeb;
    border-radius: 3px;
    transition: .2s;
  }
  .el-main-form{
    height: calc(100vh - 130px);
    overflow-y: scroll;
  }
</style>
