<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-container">
      <el-descriptions class="margin-top" title="Invoice" :column="1" border>
        <el-descriptions-item v-for="(item,index) in dataTotal.order_total" :key="item.id">
          <template slot="label">
            <i class="el-icon-tickets" />
            {{ item.title }}
          </template>

          <el-popover
            v-if="['shipping','discount','received'].includes(item.code)"
            v-model="visible[index]"
            placement="top"
            :title="item.title"
            width="200"
          >
            <el-form-item
              :prop="item.code"
              :rules="[{ required: true, message: item.title+' is required'}]"
            >
              <el-input v-model="temp[item.code]" size="mini" placeholder="Please input" type="number" @keyup.enter.native="handleConfirm(index,item.code,item.id)" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(index)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(index,item.code,item.id)">confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ dataTotal[item.code] | toThousandFilter }}</span>
          </el-popover>

          <span v-else>{{ dataTotal[item.code] | toThousandFilter }}</span>

        </el-descriptions-item>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Balance
          </template>
          {{ ( dataTotal.balance === null ? dataTotal.total : dataTotal.balance ) | toThousandFilter }}
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
</template>
<script>
import OrdersResource from '@/api/orders';

const ordersResource = new OrdersResource();
const defaultForm = {
  shipping: '',
  discount: '',
  received: '',
};
export default {
  name: 'Invoice',
  props: ['dataTotal', 'dataTotalUpdated'],
  data() {
    return {
      visible: {},
      temp: Object.assign({}, defaultForm),
      btnLoading: false,
      cancelAction: false,
    };
  },
  watch: {
    dataTotalUpdated: function(newVal, oldVal) {
	    this.updatedInvoice(newVal);
	  },
  },
  created(){
    var that = this;
    Object.keys(defaultForm).forEach(function(key) {
      that.temp[key] = that.dataTotal[key];
      that.visible[key] = false;
    });
  },
  methods: {
    _checkValidate(msg){
      if (msg != '' && msg != undefined) {
        this.cancelAction = true;
      }
    },
    handleConfirm(i, key, id){
      this.cancelAction = false;
      this.$refs['dataForm'].validateField(key, this._checkValidate);
      if (this.cancelAction) {
        return false;
      }
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
          this.updatedInvoice(res.data.invoice);
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
    updatedInvoice(invoice){
      const that = this;
      Object.keys(invoice).forEach(function(k) {
        that.dataTotal[k] = invoice[k];
      });
      this.dataTotal.balance = invoice.balance;
    },
  },
};
</script>
