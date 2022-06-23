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
        store_id:0,
        title:'',
        group:'',
        content:'',
        design:{},
        status:0,
      },
      rules: {
        title: [
          {
            required: true,
            message: 'Title is required',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  created() {
    const params = this.$route.params;
    pageResource.get(params).then(({ data } = response) => {
      this.temp.page_id = data.id;
      this.temp.design = JSON.parse(data.descriptions[0].design);
      this.temp.store_id = String(data.store_id);
      this.temp.image = data.image;
      this.temp.lang = data.descriptions[0].lang;
      this.temp.title = data.descriptions[0].title;
      this.temp.keyword = data.descriptions[0].keyword ? data.descriptions[0].keyword.split(',') : [];
      this.temp.description = data.descriptions[0].description;
      this.temp.status = String(data.status);
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