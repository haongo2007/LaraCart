<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-container">
      <el-descriptions class="margin-top" title="Info Customer" :column="1" border>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-user" />
            First name
          </template>

          <el-popover
            v-model="visible[0]"
            placement="top"
            title="First name"
            width="200"
          >
            <el-form-item
              prop="first_name"
              :rules="[
                { required: true, message: 'First name is required'},
                { max: 100, message: 'first name max length 100 character'}
              ]"
            >
              <el-input v-model="temp.first_name" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(1,'first_name')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(0)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(0,'first_name')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.first_name }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-user" />
            Last name
          </template>
          <el-popover
            v-model="visible[1]"
            placement="top"
            title="Last name"
            width="200"
          >
            <el-form-item
              prop="last_name"
              :rules="[
                { required: true, message: 'Last name is required'},
                { max: 100, message: 'Last name max length 100 character'}
              ]"
            >
              <el-input v-model="temp.last_name" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'last_name')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'last_name')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.last_name }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-mobile-phone" />
            Phone num
          </template>
          <el-popover
            v-model="visible[2]"
            placement="top"
            title="Phone num"
            width="200"
          >
            <el-form-item
              prop="phone"
              :rules="[
                { required: true, message: 'Phone is required'},
                { type: 'number', message: 'Phone must be a number'}
              ]"
            >
              <el-input v-model="temp.phone" size="mini" placeholder="Please input" type="number" @keyup.enter.native="handleConfirm(4,'phone')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(2)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(2,'phone')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.phone }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            email
          </template>
          {{ dataOrder.email }}
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-location-outline" />
            Country
          </template>
          <el-popover
            v-model="visible[3]"
            placement="top"
            title="Country"
            width="200"
          >
            <el-form-item
              prop="country"
              :rules="[
                { required: true, message: 'Country is required'},
              ]"
            >
              <el-select v-model="temp.country" placeholder="Select" filterable style="width: 100%;">
                <el-option
                  v-for="(item,index) in dataCountry"
                  :key="index"
                  :label="item"
                  :value="index"
                />
              </el-select>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(3)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(3,'country')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.country }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-location-outline" />
            Province
          </template>
          <el-popover
            v-model="visible[4]"
            placement="top"
            title="Province"
            width="200"
          >
            <el-form-item
              prop="address1"
              :rules="[
                { required: true, message: 'Province is required'}
              ]"
            >
              <el-input v-model="temp.address1" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(4,'address1')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(4)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(4,'address1')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.address1 }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-location-outline" />
            District
          </template>
          <el-popover
            v-model="visible[5]"
            placement="top"
            title="District"
            width="200"
          >
            <el-form-item
              prop="address2"
              :rules="[
                { required: true, message: 'District is required'}
              ]"
            >
              <el-input v-model="temp.address2" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(5,'address2')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(5)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(5,'address2')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.address2 }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-location-outline" />
            Address
          </template>
          <el-popover
            v-model="visible[6]"
            placement="top"
            title="Address"
            width="200"
          >
            <el-form-item
              prop="address3"
              :rules="[
                { required: true, message: 'Address is required'}
              ]"
            >
              <el-input v-model="temp.address3" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(6,'address3')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(6)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(6,'address3')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataOrder.address3 }}</span>
          </el-popover>
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Note Order
          </template>
          <el-popover
            v-model="visible[7]"
            placement="top"
            title="Notes"
            width="200"
          >
            <el-form-item
              prop="comment"
            >
              <el-input v-model="temp.comment" type="textarea" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(7)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(7,'comment')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ ( dataOrder.comment == null || dataOrder.comment == '' ? 'Empty' : dataOrder.comment) }}</span>
          </el-popover>
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
</template>
<script>
import OrdersResource from '@/api/orders';

const ordersResource = new OrdersResource();
const defaultForm = {
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  country: '',
  address1: '',
  address2: '',
  address3: '',
  comment: '',
};
export default {
  name: 'InfoCustomer',
  	props: ['dataOrder', 'dataCountry'],
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
