<template>
  <store-info v-if="!loading" :is-edit="false" :data-loading="loading" :data-info="shopInfo" />
</template>

<script>
import StoreInfo from './components/StoreInfo';
import StoreResource from '@/api/store';

const storeResource = new StoreResource();
export default {
  name: 'StoreCreate',
  components: { StoreInfo },
  data(){
    return {
      loading:true,
      shopInfo:{}
  	};
  },
  created(){
    storeResource.get(0).then(({ data } = response) => {
      this.shopInfo = data;
      this.loading = false;
    }).catch(err => {
      console.log(err);
    });
  },
  methods: {
    
  },
};
</script>
