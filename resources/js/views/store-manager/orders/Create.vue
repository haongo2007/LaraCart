<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="12" :offset="6">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form v-show="!loading" ref="dataForm" :model="temp" :rules="rules" class="form-container" label-width="200px">
        <el-form-item :label="$t('form.select_customer')">
          <el-autocomplete
            v-model="temp.customer"
            style="width: 100%;"
            value-key="email"
            :fetch-suggestions="querySearchAsync"
            :placeholder="$t('form.select_customer')"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_firstname')" prop="first_name">
          <el-input
            v-model="temp.first_name"
            :placeholder="$t('form.enter_firstname')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_lastname')" prop="last_name">
          <el-input
            v-model="temp.last_name"
            :placeholder="$t('form.enter_lastname')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_email')" prop="email">
          <el-input
            v-model="temp.email"
            :placeholder="$t('form.enter_email')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_phone')" prop="phone">
          <el-input
            v-model="temp.phone"
            :placeholder="$t('form.enter_phone')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_province')" prop="address1">
          <el-input
            v-model="temp.address1"
            :placeholder="$t('form.enter_province')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_district')" prop="address2">
          <el-input
            v-model="temp.address2"
            :placeholder="$t('form.enter_district')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_address')" prop="address3">
          <el-input
            v-model="temp.address3"
            :placeholder="$t('form.enter_address')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.select_country')" prop="country">
          <el-select v-model="temp.country" placeholder="Select" prop="country" filterable style="width: 100%;">
            <el-option
              v-for="(item,index) in CountriesOptions"
              :key="index"
              :label="item"
              :value="index"
            />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.select_curency')" prop="currency">
          <el-select v-model="temp.currency" placeholder="Select" prop="currency" style="width: 100%;" @change="handleSelectCurrency">
            <el-option
              v-for="(item,index) in CurrenciesOption"
              :key="index"
              :label="item.name"
              :value="item.code"
            />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.enter_exchange')" prop="exchange_rate">
          <el-input
            v-model="temp.exchange_rate"
            :placeholder="$t('form.enter_exchange')"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('form.enter_note')">
          <el-input
            v-model="temp.comment"
            :placeholder="$t('form.enter_note')"
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 4}"
          />
        </el-form-item>

        <el-form-item :label="$t('form.choose_payment')" prop="payment_method">
          <el-select v-model="temp.payment_method" prop="payment_method" placeholder="Select" style="width: 100%;">
            <el-option
              v-for="(item,index) in PaymentOptions"
              :key="index"
              :label="item"
              :value="index"
            />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.choose_shipping')" prop="shipping_method">
          <el-select v-model="temp.shipping_method" prop="shipping_method" placeholder="Select" style="width: 100%;">
            <el-option
              v-for="(item,index) in ShippingOptions"
              :key="index"
              :label="item"
              :value="index"
            />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('form.choose_status')" prop="status">
          <el-select v-model="temp.status" placeholder="Select" prop="status" style="width: 100%;">
            <el-option
              v-for="(item,index) in StatusOptions"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-form-item>

        <el-button class="pull-right" type="success" icon="el-icon-check" @click="createData()">
          Done
        </el-button>
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import CustomerResource from '@/api/customer';
import OrdersResource from '@/api/orders';
import reloadRedirectToList from '@/utils';

const customerResource = new CustomerResource();
const ordersResource = new OrdersResource();

const defaultForm = {
  customer_id: undefined,
  storeId: '',
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  country: '',
  address1: '',
  address2: '',
  address3: '',
  status: '',
  exchange_rate: '',
  payment_method: '',
  shipping_method: '',
  currency: '',
  comment: '',
};

export default {
  name: 'OrderCreate',
  data() {
    return {
      loading: true,
      customers: [],
      timeout: null,
      temp: Object.assign({}, defaultForm),
      StatusOptions: [],
      ShippingOptions: [],
      PaymentOptions: [],
      CountriesOptions: [],
      CurrenciesOption: [],
      ExchangeRateOption: [],
      rules: {
        first_name: [
          {
            required: true,
            message: 'First name is required',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Length max 100',
            trigger: 'blur',
          },
        ],
        last_name: [
          {
            required: true,
            message: 'Last name is required',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Length max 100',
            trigger: 'blur',
          },
        ],
        phone: [
          {
            required: true,
            message: 'Phone is required',
            trigger: 'blur',
          },
        ],
        email: [
          {
            required: true,
            message: 'Please input email address',
            trigger: 'blur',
          },
          {
            type: 'email',
            message: 'Please input correct email address',
            trigger: ['blur', 'change'],
          },
        ],
        address1: [
          {
            required: true,
            message: 'Province is required',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Length max 100',
            trigger: 'blur',
          },
        ],
        address2: [
          {
            required: true,
            message: 'District is required',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Length max 100',
            trigger: 'blur',
          },
        ],
        address3: [
          {
            required: true,
            message: 'Address is required',
            trigger: 'blur',
          },
          {
            max: 100,
            message: 'Length max 100',
            trigger: 'blur',
          },
        ],
        country: [
          {
            required: true,
            message: 'Country is required',
            trigger: 'change',
          },
        ],
        exchange_rate: [
          {
            required: true,
            message: 'Exchange rate is required',
            trigger: 'blur',
          },
        ],
        currency: [
          {
            required: true,
            message: 'Currency is required',
            trigger: 'change',
          },
        ],
        status: [
          {
            required: true,
            message: 'Status is required',
            trigger: 'change',
          },
        ],
        payment_method: [
          {
            required: true,
            message: 'Payment method is required',
            trigger: 'change',
          },
        ],
        shipping_method: [
          {
            required: true,
            message: 'Shipping method is required',
            trigger: 'change',
          },
        ],
      },
    };
  },
  created() {
    this.getListCustomer();
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'OrdersList' });
    },
    async getListCustomer() {
      const { data } = await customerResource.list();
      this.customers = data;
      this.loading = false;
    },
    async getRelationData() {
      const { data } = await ordersResource.getRelationData(this.temp.storeId);
      this.StatusOptions = data.orderStatus;
      this.CountriesOptions = data.countries;
      this.CurrenciesOption = data.currencies;
      this.ExchangeRateOption = data.currenciesRate;
      this.ShippingOptions = data.shippingMethod;
      this.PaymentOptions = data.paymentMethod;
    },
    querySearchAsync(queryString, cb) {
      var customer = this.customers;
      var results = queryString ? customer.filter(this.createFilter(queryString)) : customer;

      if (results.length == 0) {
        customerResource.list({ keyword: queryString }).then(response => {
          this.customers = response.data
          results = response.data;
        })
          .catch(err => {
            console.log(err);
          });
      }
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        cb(results);
      }, 1000 * Math.random());
    },
    createFilter(queryString) {
      return (customer) => {
        return (customer.email.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    handleSelect(item) {
      this.temp.customer_id = item.id;
      this.temp.first_name = item.first_name;
      this.temp.last_name = item.last_name;
      this.temp.phone = item.phone;
      this.temp.email = item.email;
      this.temp.country = item.country;
      this.temp.address1 = item.address1;
      this.temp.address2 = item.address2;
      this.temp.address3 = item.address3;
      this.temp.storeId = item.store.id;
      this.getRelationData();
    },
    handleSelectCurrency(index){
      this.temp.exchange_rate = this.ExchangeRateOption[index];
    },
    createData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          ordersResource.store(this.temp).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });

              this.temp = Object.assign({}, defaultForm);
              
              reloadRedirectToList('OrdersList');
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .image-uploading{
    position: relative;
    .el-icon-close{
        cursor: pointer;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 18px;
        opacity: 0;
        transition:all .5s;
    }
    &:hover {
      .el-icon-close{
        opacity: 1;
      }
    }
  }
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    vertical-align: bottom;
  }
  .el-main-form{
    height: calc(100vh - 142px);
    overflow-y: scroll;
  }
</style>
