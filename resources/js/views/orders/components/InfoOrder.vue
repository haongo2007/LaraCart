<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-container">
      <el-descriptions class="margin-top" title="Info Order" :column="1" border>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            State Order
          </template>
          <el-popover
            v-model="visible[0]"
            placement="top"
            title="State Order"
            width="200"
          >
            <el-form-item
              prop="status"
              :rules="[
                { required: true, message: 'Status is required'},
              ]"
            >
              <el-select v-model="temp.status" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataOrderStatus"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(0)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(0,'status')">confirm</el-button>
              </el-button-group>
            </div>
            <el-tag slot="reference" :type="statusFilter(dataOrderStatus,dataOrder.status,'label')">
              <span class="border-edit">{{ statusFilter(dataOrderStatus,dataOrder.status,'name') }}</span>
            </el-tag>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            State Shipping
          </template>
          <el-popover
            v-model="visible[1]"
            placement="top"
            title="State Shipping"
            width="200"
          >
            <el-form-item
              prop="shipping_status"
              :rules="[
                { required: true, message: 'Shipping status is required'},
              ]"
            >
              <el-select v-model="temp.shipping_status" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataShippingStatus"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'shipping_status')">confirm</el-button>
              </el-button-group>
            </div>
            <el-tag slot="reference" :type="statusFilter(dataShippingStatus,dataOrder.shipping_status,'label')">
              <span class="border-edit">{{ statusFilter(dataShippingStatus,dataOrder.shipping_status,'name') }}</span>
            </el-tag>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            State Payment
          </template>
          <el-popover
            v-model="visible[2]"
            placement="top"
            title="State Payment"
            width="200"
          >
            <el-form-item
              prop="payment_status"
              :rules="[
                { required: true, message: 'Payment status is required'},
              ]"
            >
              <el-select v-model="temp.payment_status" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataPaymentStatus"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(2)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(2,'payment_status')">confirm</el-button>
              </el-button-group>
            </div>
            <el-tag slot="reference" :type="statusFilter(dataPaymentStatus,dataOrder.payment_status,'label')">
              <span class="border-edit">{{ statusFilter(dataPaymentStatus,dataOrder.payment_status,'name') }}</span>
            </el-tag>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Shipping Method
          </template>
          <el-popover
            v-model="visible[3]"
            placement="top"
            title="Shipping Method"
            width="200"
          >
            <el-form-item
              prop="shipping_method"
              :rules="[
                { required: true, message: 'Shipping method is required'},
              ]"
            >
              <el-select v-model="temp.shipping_method" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataShippingMethod"
                  :key="index"
                  :label="item"
                  :value="index"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(3)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(3,'shipping_method')">confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.shipping_method }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Payment Method
          </template>
          <el-popover
            v-model="visible[4]"
            placement="top"
            title="Payment Method"
            width="200"
          >
            <el-form-item
              prop="payment_method"
              :rules="[
                { required: true, message: 'Payment method is required'},
              ]"
            >
              <el-select v-model="temp.payment_method" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataPaymentMethod"
                  :key="index"
                  :label="item"
                  :value="index"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(4)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(4,'payment_method')">confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.payment_method }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Domain
          </template>
          {{ dataOrder.domain }}
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Created at
          </template>
          {{ dataOrder.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Currency
          </template>
          {{ dataOrder.currency }}
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Exchange rate
          </template>
          {{ dataOrder.exchange_rate }}
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
</template>
<script>
import OrdersResource from '@/api/orders';

const ordersResource = new OrdersResource();
const defaultForm = {
  status: '',
  shipping_status: '',
  payment_status: '',
  shipping_method: '',
  payment_method: '',
};
export default {
  name: 'InfoOrder',
  	props: ['dataOrder', 'dataOrderStatus', 'dataShippingStatus', 'dataPaymentStatus', 'dataPaymentMethod', 'dataShippingMethod'],
  data() {
    return {
      visible: {},
      temp: Object.assign({}, defaultForm),
      btnLoading: false,
      cancelAction: false,
    };
  },
  created(){
    var that = this;
    Object.keys(defaultForm).forEach(function(key) {
      that.temp[key] = that.dataOrder[key];
      that.visible[key] = false;
    });
  },
  methods: {
	    statusFilter(data, status, get){
	      	const statusFilter = data.filter(v => v.id === status);
	      	return statusFilter[0][get];
	    },
    _checkValidate(msg){
      if (msg != '' && msg != undefined) {
        this.cancelAction = true;
      }
    },
    handleConfirm(i, key){
      this.cancelAction = false;
      this.$refs['dataForm'].validateField(key, this._checkValidate);
      if (this.cancelAction) {
        return false;
      }
      const id = this.$route.params.id;
      const params = {
        name: key,
        value: this.temp[key],
      };

	      this.btnLoading = true;
      ordersResource.update(id, params).then((res) => {
		      if (res) {
		        this.$message({
		          type: 'success',
		          message: 'Update successfully',
		        });
			      this.btnLoading = false;
          this.visible[i] = false;
          this.dataOrder[key] = this.temp[key];
	        	this.$emit('handleChangeHistory', res.data.history);
		      } else {
		        this.$message({
		          type: 'error',
		          message: 'Update failed',
		        });
			      this.btnLoading = false;
          this.visible[i] = false;
		      }
      }).catch(err => {
			      console.log(err);
			      this.btnLoading = false;
        this.visible[i] = false;
			    });
    },
  		handleCancel(i){
  			this.visible[i] = false;
  		},
  },
};
</script>
<style>
	.border-edit {
		border-bottom: 1px dotted #606266;
	    color: #1890ff;
	    cursor: pointer;
	}
</style>
