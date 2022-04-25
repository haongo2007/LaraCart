<template>
  <el-form ref="dataForm" :model="temp" class="form-config-container">
    <el-descriptions class="margin-top" title="Config Admin" :column="1" border>
      <el-descriptions-item v-for="(item,index) in temp" :label="item.detail" :key="index">
        <el-popover
          v-if="index != 'ADMIN_LOGO'"
          v-model="visible[item.id]"
          placement="top"
          title="Admin name"
          width="200"
        >
          <el-form-item
            prop="admin_name">
            <el-input v-model="item.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(item.id,index)" />
          </el-form-item>
          <div style="text-align: right; margin: 12px 0px 0px 0px">
            <el-button-group>
              <el-button type="danger" size="mini" @click="handleCancel(item.id)">cancel</el-button>
              <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(item.id,index)">Confirm</el-button>
            </el-button-group>
          </div>
          <span slot="reference" class="border-edit">{{ item.value ? item.value : 'Empty' }}</span>
        </el-popover>
        <div v-else @click="handleVisibleStorage()">
          <el-image 
            style="width: 100px;cursor: pointer;"
            :src="item.value ? item.value :'api/system/getFile?disk=store&path=logo.png&w=260'"
            fit="contain">
          </el-image>
        </div>
      </el-descriptions-item>
    </el-descriptions>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </el-form>
</template>

<script>

import EventBus from '@/components/FileManager/eventBus';
import FileManager from '@/components/FileManager';
export default {
  name: 'ConfigAdmin',
  components:{FileManager},
  props: ['dataConfig'],
  data(){
    return {
      btnLoading: false,
      visible:{},
      temp:{
      },
      componentStorage: '',
      dialogStorageVisible:false,
  	};
  },
  created(){
    this.temp = this.dataConfig;
    let that = this;
    for(var prop in this.temp) {
      that.$set(that.visible,this.temp[prop].id,false);
    }
  },
  methods: {
    handleCancel(i){
      this.visible[i] = false;
    },
    handleConfirm(i,key){
      this.btnLoading = true;
      let data = this.temp[key];
      this.$emit('handleUpdate', data);
      this.btnLoading = false;
      this.visible[i] = false;
    },
    handleVisibleStorage(index, key){
      EventBus.$on('getFileResponse', this.handlerGeturl);
      this.$store.commit('fm/setDisks', 'store');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
    },  
    dialogStorageClose(){
      EventBus.$off('getFileResponse');
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handlerGeturl(data) {
      this.temp.ADMIN_LOGO.value = data[0];
      this.$emit('handleUpdate', this.temp.ADMIN_LOGO);
      this.dialogStorageClose();
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
  .el-dialog{
    margin-top: 2vh!important;
  }
</style>
