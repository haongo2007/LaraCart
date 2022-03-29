<template>
  <el-form ref="dataForm" :model="temp" class="form-config-container">
    <el-descriptions class="margin-top" title="Config Order" :column="1" border>
      <el-descriptions-item :label="item.detail" v-for="(item,index) in dataConfig.orderConfig" :key="index">
        <el-switch
          v-model="item.value"
          active-color="#13ce66"
          inactive-color="#ff4949"
          active-value="1"
          inactive-value="0">
        </el-switch>
      </el-descriptions-item>
    </el-descriptions>
  </el-form>
</template>

<script>

export default {
  name: 'ConfigOrder',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      temp:{}
  	};
  },
  created(){

  },
  methods: {
    
  },
};
</script>
