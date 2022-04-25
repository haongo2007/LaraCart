<template>
  <div class="sidebar-logo-container" :class="{'collapse':collapse}">
    <transition name="sidebarLogoFade">
      <router-link v-if="collapse" key="collapse" class="sidebar-logo-link" to="/">
        <img v-if="logo" :src="logo" class="sidebar-logo">
        <h1 v-else class="sidebar-title">{{ names }} </h1>
      </router-link>
      <router-link v-else key="expand" class="sidebar-logo-link" to="/">
        <img v-if="logo" :src="logo" class="sidebar-logo">
        <h1 class="sidebar-title">{{ names }} </h1>
      </router-link>
    </transition>
  </div>
</template>

<script>
import Cookies from 'js-cookie';

export default {
  name: 'SidebarLogo',
  props: {
    collapse: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      logo: '',
      names:''
    };
  },
  watch: {
    '$store.state.user.currentStore'(value, oldValue) {
      this.logo_admin();
      this.name_admin();
    },
  },
  created(){
    this.name_admin();
    this.logo_admin();
  },
  methods:{
    renderAdminSignature(type){
      let store_ck = Cookies.get('store');
      let data,data_config = '';
      if (store_ck) {
        store_ck = JSON.parse(store_ck);
      }
      if (store_ck && store_ck.length == 0) {
        let index = Object.keys(this.$store.state.user.storeList);
        data_config = this.$store.state.user.storeList[index[0]] ? this.$store.state.user.storeList[index[0]].admin_custom_config.filter((item) => item.key == type) : '';
        data = data_config ? data_config[0].value : '';
      }else{
        store_ck = typeof store_ck === 'object' ? store_ck[0] : store_ck;
        data_config = this.$store.state.user.storeList[store_ck] ? this.$store.state.user.storeList[store_ck].admin_custom_config.filter((item) => item.key == type) : '';
        data = data_config ? data_config[0].value : '';
      }
      return data;
    },
    logo_admin(){
      let logo_config = this.renderAdminSignature('ADMIN_LOGO');
      this.logo = logo_config ? logo_config : '/svg/logo.svg'
    },
    name_admin(){
      let name_config = this.renderAdminSignature('ADMIN_NAME');
      this.names = name_config ? name_config : 'LaraCart Admin';
    } 
  }
};
</script>

<style lang="scss" scoped>
  .sidebarLogoFade-enter-active {
    transition: opacity 1.5s;
  }

  .sidebarLogoFade-enter,
  .sidebarLogoFade-leave-to {
    opacity: 0;
  }

  .sidebar-logo-container {
    position: relative;
    width: 100%;
    height: 50px;
    line-height: 50px;
    background: #2b2f3a;
    text-align: center;
    overflow: hidden;

    & .sidebar-logo-link {
      height: 100%;
      width: 100%;

      & .sidebar-logo {
        width: 32px;
        height: 32px;
        vertical-align: middle;
        margin-right: 12px;
      }

      & .sidebar-title {
        display: inline-block;
        margin: 0;
        color: #fff;
        font-weight: 600;
        line-height: 50px;
        font-size: 14px;
        font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
        vertical-align: middle;
      }
    }

    &.collapse {
      .sidebar-logo {
        margin-right: 0px;
      }
    }
  }
</style>
