<template>
  <customer-detail v-if="!loading" :is-edit="true" :data-temp="temp" :data-rules="rules"/>
</template>

<script>

import CustomerDetail from './components/CustomerDetail';
import CustomerResource from '@/api/customer';
const customerResource = new CustomerResource();

export default {
  name: 'CategoryEdit',
  components: { CustomerDetail },
  data() {
    return {
    	loading: true,
      temp: {
        store_id: 0,
        first_name:'',
        last_name:'',
        email:'',
        phone:'',
        sex:'',
        birthday:'',
        password:'',
        address1:'',
        address2:'',
        address3:'',
        country:'',
        status:'',
      },
      rules: {
        sort: [
          {
            type: 'number',
            message: 'sort must be a number',
            trigger: 'blur',
          },
        ],
        parent: [
          {
            required: true,
            message: 'parent is required',
            trigger: 'change',
          },
        ],
        descriptions: [],
      },
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchCustomer(id);
  },
  methods: {
    fetchCustomer(id) {
      const loading = this.$loading({
        target: '.app-main',
      });
      customerResource.get(id)
        .then(({ data } = response) => {
          this.temp = Object.assign({},data.customer);
          loading.close();
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
  },
};
</script>

