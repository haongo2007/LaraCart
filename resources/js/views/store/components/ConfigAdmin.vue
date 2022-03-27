<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-container">
      <el-descriptions class="margin-top" title="Config Admin" :column="1" border>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Admin name
          </template>
          <el-popover
            v-model="visible[0]"
            placement="top"
            title="Admin name"
            width="200"
          >
            <el-form-item
              prop="admin_name"
              :rules="[
                { required: true, message: 'Admin name is required'},
              ]"
            >
              <el-input v-model="temp.admin_name" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'admin_name')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'admin_name')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ temp.admin_name ? temp.admin_name : 'Empty' }}</span>
          </el-popover>
        </el-descriptions-item>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Admin title
          </template>
          <el-popover
            v-model="visible[1]"
            placement="top"
            title="Admin title"
            width="200"
          >
            <el-form-item
              prop="admin_title"
              :rules="[
                { required: true, message: 'Admin title is required'},
              ]"
            >
              <el-input v-model="temp.admin_title" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'admin_title')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'admin_title')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ temp.admin_title ? temp.admin_title : 'Empty' }}</span>
          </el-popover>
        </el-descriptions-item>
        <el-descriptions-item>
          <template slot="label">
            <i class="el-icon-tickets" />
            Admin logo
          </template>
          <el-popover
            v-model="visible[2]"
            placement="top"
            title="Admin logo"
            width="200"
          >
            <el-form-item
              prop="admin_logo"
              :rules="[
                { required: true, message: 'Admin logo is required'},
              ]"
            >
              <el-input v-model="temp.admin_logo" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'admin_logo')" />
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'admin_logo')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ temp.admin_logo ? temp.admin_logo : 'Empty' }}</span>
          </el-popover>
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
</template>

<script>

export default {
  name: 'ConfigAdmin',
  props: ['dataAdminConfig'],
  data(){
    return {
      btnLoading: false,
      visible:[false,false,false],
      temp:{
        admin_name:'',
        admin_title:'',
        admin_logo:''
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

<style>
  .border-edit {
    border-bottom: 1px dotted #606266;
      color: #1890ff;
      cursor: pointer;
  }
  .form-container{
    border: 1px solid #eee;
    width: 50%;
    padding: 20px;
    border-radius: 5px;
  }
</style>
