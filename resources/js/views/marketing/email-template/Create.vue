<template>
  <email-template-detail :is-edit="false" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import EmailTemplateDetail from './components/EmailTemplateDetail';
export default {
  name: 'EmailTemplateCreate',
  components: {
    EmailTemplateDetail,
  },
  data() {
    return {
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
  },
  methods: {
  }
};
</script>