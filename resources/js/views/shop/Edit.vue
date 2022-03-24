<template>
  <shop-info v-if="!loading" :is-edit="true" :data-loading="loading" :data-info="shopInfo" />
</template>

<script>
import ShopInfo from './components/ShopInfo';
import StoreResource from '@/api/store';

const storeResource = new StoreResource();
export default {
  name: 'ShopEdit',
  components: { ShopInfo },
  data(){
    return {
      loading:true,
      shopInfo:{}
  	};
  },
  created(){
    const id = this.$route.params && this.$route.params.id;
    storeResource.get(id).then(({ data } = response) => {
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
