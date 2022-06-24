<template>
  <email-template-detail v-if="loading" :is-edit="true" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import EmailTemplateDetail from './components/EmailTemplateDetail';
import PageResource from '@/api/page';

const pageResource = new PageResource();
export default {
  name: 'EmailTemplateEdit',
  components: {
    EmailTemplateDetail,
  },
  data() {
    return {
      loading:false,
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
      },
    };
  },
  created() {
    const params = this.$route.params;
    pageResource.get(params).then(({ data } = response) => {
      this.temp.id = data.id;
      this.temp.design = JSON.parse(data.descriptions[0].design);
      this.temp.store_id = String(data.store_id);
      this.temp.name = data.descriptions[0].name;
      this.temp.status = String(data.status);
      this.temp.group = data.group;
      this.temp.design = data.design;
      this.loading = true;
    })
    .catch(err => {
      console.log(err);
    });
  },
  methods: {
  }
};
</script>