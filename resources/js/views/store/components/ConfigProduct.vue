<template>
  <el-form ref="dataForm" class="form-config-container" style="width: 100%;">
    <el-row :gutter="20" style="margin:0px">
      <el-col :span="12" style="padding: 0;">
        <el-descriptions class="margin-top" :title="$t('store.product_config')" :column="1" border>
          <el-descriptions-item v-for="(item,index) in dataConfig.productConfig" :key="index" :label="item.detail">
            <el-popover
              v-if="item.key == 'product_tax'"
              v-model="visible"
              placement="top"
              :title="item.detail"
              width="200" >
              <el-form-item>
                <el-select v-model="item.value" placeholder="Select" filterable style="width: 100%;">
                  <el-option
                    v-for="(tax,key) in dataConfig.taxs"
                    :key="key"
                    :label="tax"
                    :value="key"
                  />
                </el-select>
              </el-form-item>
              <div style="text-align: right; margin: 12px 0px 0px 0px">
                <el-button-group>
                  <el-button type="danger" @click="handleCancel()" size="mini">cancel</el-button>
                  <el-button type="primary" @click="handleConfirm(item)" size="mini" :loading="btnLoading" >Confirm</el-button>
                </el-button-group>
              </div>
              <span slot="reference" class="border-edit">{{ dataConfig.taxs[item.value] }}</span>
            </el-popover>
            <el-switch v-else
              @change="handleActive(item)"
              v-model="item.value"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0">
            </el-switch>
          </el-descriptions-item>
        </el-descriptions>
      </el-col>
      <el-col :span="12"  style="padding: 0;">
        <div class="margin-top el-descriptions">
          <div class="el-descriptions__header">
            <div class="el-descriptions__title">{{$t('store.product_config')}}</div>
            <div class="el-descriptions__extra"></div>
          </div>
          <div class="el-descriptions__body">
            <table class="el-descriptions__table is-bordered el-descriptions--medium">
               <tbody>
                  <tr class="el-descriptions-row">
                     <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('table.fieldConfig') }}</th>
                     <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('table.value') }}</th>
                     <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('table.required') }}</th>
                  </tr>
                  <tr class="el-descriptions-row" v-for="(item,index) in dataConfig.productConfigAttribute" :key="index">
                     <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                       {{ item.detail }}
                     </td>
                     <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                        <el-switch
                          @change="handleActive(item)"
                          v-model="item.value"
                          active-color="#13ce66"
                          inactive-color="#ff4949"
                          active-value="1"
                          inactive-value="0">
                        </el-switch>
                     </td>
                     <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content" v-if="item.required">
                        <el-switch
                          @change="handleActive(item)"
                          v-model="item.required.value"
                          active-color="#13ce66"
                          inactive-color="#ff4949"
                          active-value="1"
                          inactive-value="0">
                        </el-switch>
                     </td>
                  </tr>
               </tbody>
            </table>
          </div>
        </div>
      </el-col>
    </el-row>
  </el-form>
</template>

<script>

export default {
  name: 'ConfigProduct',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      visible:false,
      btnLoading:false,
  	};
  },
  created(){},
  methods: {
    handleCancel(){
      this.visible = false;
    },
    handleConfirm(item){
      this.visible = false;
      this.$emit('handleUpdate', item);
    },
    handleActive(item){
      this.$emit('handleUpdate', item);
    }
  },
};
</script>
