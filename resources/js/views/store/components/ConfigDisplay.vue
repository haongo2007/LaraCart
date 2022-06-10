<template>
  <el-form ref="dataForm" class="form-config-container">
    <el-descriptions class="margin-top" :title="$t('store.display_config')" :column="1" border>
      <el-descriptions-item :label="item.detail" v-for="(item,index) in dataConfig.configDisplay" :key="index">
        <el-popover
          v-model="visible[index]"
          placement="top"
          :title="item.detail"
          width="200"
          >
            <el-form-item
              prop="admin_name">
              <el-input v-model="item.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(item,index)" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(index)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(item,index)">Confirm</el-button>
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
      btnLoading:false,
      visible:{},
  	};
  },
  created(){
    for (var prop in this.dataConfig.displayConfig) {   
      this.$set(this.visible,prop,false);
    };
  },
  methods: {
    handleConfirm(item,i){
      this.btnLoading = true;
      this.$emit('handleUpdate', item);
      this.visible[i] = false;
    },
    handleCancel(i){
      this.visible[i] = false;
    }
  },
};
</script>
