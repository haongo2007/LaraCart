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
          <el-tab-pane label="Admin">
            <config-admin />
          </el-tab-pane>
          <el-tab-pane label="Captcha">
            <components :is="comp_captcha" :data-captcha="captchaConfig"/>
          </el-tab-pane>
          <el-tab-pane label="Customer">
            <config-customer />
          </el-tab-pane>
          <el-tab-pane label="Display">
            <config-display />
          </el-tab-pane>
          <el-tab-pane label="Email">
            <config-email />
          </el-tab-pane>
          <el-tab-pane label="Order">
            <config-order />
          </el-tab-pane>
          <el-tab-pane label="Product">
            <config-product />
          </el-tab-pane>
          <el-tab-pane label="Url">
            <config-url />
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
      captchaConfig:{},
      comp_captcha:'',
  	};
  },
  created(){
    const id = this.$route.params && this.$route.params.id;
    storeResource.getConfig(id).then(({ data } = response) => {
      this.captchaConfig = {captcha_page: data.captcha_page,captcha:data.configCaptcha};
      this.comp_captcha = 'ConfigCaptcha';
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
