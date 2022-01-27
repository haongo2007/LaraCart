<template>
  <product-detail :is-edit="false" :kind="0" />
</template>

<script>
import ProductDetail from './components/ProductDetail';
export default {
  name: 'ProductCreateSingle',
  components: { ProductDetail },
};
</script>
