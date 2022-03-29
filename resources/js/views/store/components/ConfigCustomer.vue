<template>
  <el-form ref="dataForm" :model="temp" class="form-config-container">
    <div class="margin-top el-descriptions">
       <div class="el-descriptions__header">
          <div class="el-descriptions__title">Config Customer</div>
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
                <tr class="el-descriptions-row" v-for="(item,index) in dataConfig" :key="index">
                   <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                     {{ item.detail }}
                   </td>
                   <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                      <el-switch
                        v-model="item.value"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        active-value="1"
                        inactive-value="0">
                      </el-switch>
                   </td>
                   <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                      <el-switch
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
  </el-form>
</template>

<script>

export default {
  name: 'ConfigCustomer',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      temp:{
      }
  	};
  },
  created(){
  },
  methods: {
    handleCancel(i){
      this.visible[i] = false;
    },
    
  },
};
</script>
