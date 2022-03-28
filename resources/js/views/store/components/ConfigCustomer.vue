<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-config-container">
      <el-descriptions class="margin-top" title="Config Customer" direction="vertical" border>
        <el-descriptions-item v-for="(value,label) in customerConfig" :key="label" :label="$t('table.'+label)">
          
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
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
      customerConfig:{
        fieldConfig:{},
        value:{},
        required:{},
      },
      temp:{
      }
  	};
  },
  created(){
    this.$set(this.customerConfig,'fieldConfig',this.dataConfig.customerConfigs);
    this.$set(this.customerConfig,'value',this.dataConfig.customerConfigs);
  },
  methods: {
    handleCancel(i){
      this.visible[i] = false;
    },
    
  },
};
</script>
