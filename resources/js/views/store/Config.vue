<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:0px;">
      <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
        <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
      </div>
    </el-row>

    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-col :span="24">
        <el-tabs type="card" tab-position="top">
          <el-tab-pane v-for="(item,index) in comp" :label="index" :key="index">
            <components :is="item.value" :data-config="item.dataConfig"/>
          </el-tab-pane>
        </el-tabs>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import StoreResource from '@/api/store';
import ConfigAdmin from './components/ConfigAdmin';
import ConfigCaptcha from './components/ConfigCaptcha';
import ConfigCustomer from './components/ConfigCustomer';
import ConfigDisplay from './components/ConfigDisplay';
import ConfigEmail from './components/ConfigEmail';
import ConfigOrder from './components/ConfigOrder';
import ConfigProduct from './components/ConfigProduct';
import ConfigUrl from './components/ConfigUrl';

const storeResource = new StoreResource();
export default {
  name: 'StoreConfig',
  components: { 
    ConfigAdmin,
    ConfigCaptcha,
    ConfigCustomer,
    ConfigDisplay,
    ConfigEmail,
    ConfigOrder,
    ConfigProduct,
    ConfigUrl 
  },
  data(){
    return {
      customerConfig:{},
      comp:{
        'Admin': {
          'value':'',
          'dataConfig' : {}
        },
        Captcha: {
          'value':'',
          'dataConfig' : {}
        },
        Customer: {
          'value':'',
          'dataConfig' : {}
        },
        Display: {
          'value':'',
          'dataConfig' : {}
        },
        Email: {
          'value':'',
          'dataConfig' : {}
        },
        Order: {
          'value':'',
          'dataConfig' : {}
        },
        Product: {
          'value':'',
          'dataConfig' : {}
        },
        Url: {
          'value':'',
          'dataConfig' : {}
        }
      }
  	};
  },
  created(){
    const id = this.$route.params && this.$route.params.id;
    storeResource.getConfig(id).then(({ data } = response) => {
      this.$set(this.comp.Admin,'value', 'ConfigAdmin');
       
      this.$set(this.comp.Captcha,'value','ConfigCaptcha');
      this.$set(this.comp.Captcha,'dataConfig',{captcha_page: data.captcha_page,captcha:data.configCaptcha});


      this.$set(this.comp.Customer,'value','ConfigCustomer');
      this.$set(this.comp.Customer,'dataConfig',{customerConfigs: data.customerConfigs,customerConfigsRequired:data.customerConfigsRequired});


      this.$set(this.comp.Display,'value','ConfigDisplay');
      this.$set(this.comp.Display,'dataConfig',{configDisplay: data.configDisplay});


      this.$set(this.comp.Email,'value','ConfigEmail');
      this.$set(this.comp.Email,'dataConfig',{emailConfig: data.emailConfig});


      this.$set(this.comp.Order,'value','ConfigOrder');
      this.$set(this.comp.Order,'dataConfig',{orderConfig: data.orderConfig});


      this.$set(this.comp.Product,'value','ConfigProduct');
      this.$set(this.comp.Product,'dataConfig',{
        productConfig: data.productConfig,
        productConfigAttribute:data.productConfigAttribute,
        productConfigAttributeRequired:data.productConfigAttributeRequired
      });


      this.$set(this.comp.Url,'value','ConfigUrl');
    }).catch(err => {
      console.log(err);
    });
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'StoreList' });
    },
    
  },
};
</script>
<style type="text/css">
  .form-config-container{
    border: 1px solid #eee;
    width: 50%;
    padding: 20px;
    border-radius: 5px;
  }
</style>
