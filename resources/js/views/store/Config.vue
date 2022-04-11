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
            <components :is="item.value" :btn-Loading="btnLoading" :data-config="item.dataConfig" @handleUpdate="handleUpdate"/>
          </el-tab-pane>
        </el-tabs>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import StoreConfigResource from '@/api/store-config';
import ConfigAdmin from './components/ConfigAdmin';
import ConfigCaptcha from './components/ConfigCaptcha';
import ConfigCustomer from './components/ConfigCustomer';
import ConfigDisplay from './components/ConfigDisplay';
import ConfigEmail from './components/ConfigEmail';
import ConfigOrder from './components/ConfigOrder';
import ConfigProduct from './components/ConfigProduct';

const storeconfigResource = new StoreConfigResource();
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
  },
  data(){
    return {
      id:null,
      customerConfig:{},
      btnLoading:false,
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
      }
  	};
  },
  created(){
    const loading = this.$loading({
      target: '.app-main',
    });
    let id = this.id = this.$route.params && this.$route.params.id;
    storeconfigResource.get(id).then(({ data } = response) => {
      this.$set(this.comp.Admin,'value', 'ConfigAdmin');
      this.$set(this.comp.Admin,'dataConfig',data.adminConfig);
       
      this.$set(this.comp.Captcha,'value','ConfigCaptcha');
      this.$set(this.comp.Captcha,'dataConfig',{captchaInstalled:data.pluginCaptchaInstalled,captcha_page: data.captcha_page,captcha:data.captchaConfig});


      this.$set(this.comp.Customer,'value','ConfigCustomer');
      this.$set(this.comp.Customer,'dataConfig',data.customerConfigs);


      this.$set(this.comp.Display,'value','ConfigDisplay');
      this.$set(this.comp.Display,'dataConfig',{configDisplay: data.displayConfig});


      this.$set(this.comp.Email,'value','ConfigEmail');
      this.$set(this.comp.Email,'dataConfig',{emailConfig: data.emailConfig,smtp_method: data.smtp_method});


      this.$set(this.comp.Order,'value','ConfigOrder');
      this.$set(this.comp.Order,'dataConfig',{orderConfig: data.orderConfig});


      this.$set(this.comp.Product,'value','ConfigProduct');
      this.$set(this.comp.Product,'dataConfig',{
        productConfig: data.productConfig,
        productConfigAttribute:data.productConfigAttribute,
        taxs:data.taxs
      });

      loading.close();
    }).catch(err => {
      console.log(err);
    });
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'StoreList' });
    },
    handleUpdate(data){
      storeconfigResource.update(this.id,data).then((res) => {
        if (res) {
          this.$message({
            type: 'success',
            message: 'Update successfully',
          });
        } else {
          this.$message({
            type: 'error',
            message: 'Update failed',
          });
        }
      }).catch(err => {
          console.log(err);
      });
    }
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
