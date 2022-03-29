<template>
  <el-form ref="dataForm" :model="temp" class="form-config-container">
    <el-descriptions class="margin-top" title="Config Display" :column="1" border>
      <el-descriptions-item :label="item.detail" v-for="(item,index) in dataConfig.configDisplay" :key="index">
        <el-popover
          v-model="visible[index]"
          placement="top"
          :title="item.detail"
          width="200"
          >
            <el-form-item
              prop="admin_name"
              :rules="[
                { required: true, message: item.detail+' is required'},
              ]"
            >
              <el-input v-model="item.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'admin_name')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'admin_name')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ item.value ? item.value : 'Empty' }}</span>
        </el-popover>
      </el-descriptions-item>
    </el-descriptions>
  </el-form>
</template>

<script>

export default {
  name: 'ConfigDisplay',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      visible:{},
      temp:{}
  	};
  },
  created(){
    this.dataConfig.configDisplay.forEach(function(v,i){
      this.$set(this.visible,i,false);
    });
  },
  methods: {
  },
};
</script>
