<template>
  <email-template-detail :is-edit="false" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import EmailTemplateDetail from './components/EmailTemplateDetail';
import Cookies from 'js-cookie';
export default {
  name: 'EmailTemplateCreate',
  components: {
    EmailTemplateDetail,
  },
  data() {
    return {
      temp:{
        id:null,
        store_id:0,
        name:'',
        group:'',
        content:'',
        design:{},
        status:0,
      },
      rules: {
        name: [
          {
            required: true,
            message: 'Name is required',
            trigger: 'blur',
          },
        ],
        group: [
          {
            required: true,
            message: 'Group is required',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  created() {
    let store_ck = Cookies.get('store');
    if (store_ck) {
      store_ck = JSON.parse(store_ck);
    }
    if (store_ck && store_ck.length == 1) {
      this.temp.store_id = store_ck[0];
    }else{
      this.temp.store_id = Object.keys(this.$store.getters.storeList)[0];
    }
  },
  methods: {
  }
};
</script>