<template>
  <page-detail :is-edit="false" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import PageDetail from './components/PageDetail';
export default {
  name: 'PageCreate',
  components: {
    PageDetail,
  },
  data() {
    return {
      temp:{
        store_id:0,
        page_id:[],
        lang:'',
        title:'',
        keyword:[],
        description:'',
        content:'',
        design:{},
        image:'',
        status:0,
      },
      rules: {
        store_id: [
          {
            required: true,
            message: 'Store is required',
            trigger: 'change',
          },
        ],
        title: [
          {
            required: true,
            message: 'Title is required',
            trigger: 'blur',
          },
        ],
        lang: [
          {
            required: true,
            message: 'Language is required',
            trigger: 'change',
          },
        ],
        image: [
          {
            required: true,
            message: 'Image is required',
            trigger: 'change',
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