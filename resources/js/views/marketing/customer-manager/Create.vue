<template>
  <div>
    <customer-detail v-if="!loading && !confirmStoreDialog" :is-edit="false" :data-temp="temp" :data-rules="rules" />
    <el-dialog
      :show-close="false"
      :title="$t('form.choose_store_for_customer')"
      :visible.sync="confirmStoreDialog"
      :before-close="handleConfirm"
      width="30%"
    >
      <div>
        <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index">
          {{ item.descriptions_current_lang[0].title }}
        </el-radio>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" :disabled="temp.store_id == 0" @click="confirmChooseStore">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import CustomerDetail from './components/CustomerDetail';
import Cookies from 'js-cookie';

export default {
  name: 'CustomerCreate',
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
        email: [
          {
            required: true,
            message: 'Please input email address',
            trigger: 'blur',
          },
          { 
            type: 'email', 
            message: 'Please input correct email address', 
            trigger: ['blur', 'change'] 
          }
        ],
      },
      confirmStoreDialog: false,
    };
  },
  computed: {
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  created() {
    let store_ck = Cookies.get('store');
    if (store_ck) {
      store_ck = JSON.parse(store_ck);
    }
    if (store_ck && store_ck.length == 1) {
      this.temp.store_id = store_ck[0];
    }else{
      this.confirmStoreDialog = true;
      return false;
    }
  },
  methods: {
    confirmChooseStore(){
      this.confirmStoreDialog = false;
      this.loading = false;
    },
    handleConfirm(done){
      if (this.temp.store_id != 0) {
        this.loading = false;
        done();
      }
    },
  },
};
</script>

