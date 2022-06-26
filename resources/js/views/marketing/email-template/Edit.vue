<template>
  <email-template-detail v-if="loading" :is-edit="true" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import EmailTemplateDetail from './components/EmailTemplateDetail';
import EmailTemplateResource from '@/api/email-template';

const emailTemplateResource = new EmailTemplateResource();
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
    const id = this.$route.params && this.$route.params.id;
    emailTemplateResource.get(id).then(({ data } = response) => {
      this.temp.id = data.id;
      this.temp.design = JSON.parse(data.design);
      this.temp.store_id = String(data.store_id);
      this.temp.name = data.name;
      this.temp.status = String(data.status);
      this.temp.group = data.group;
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