<template>
  <div class="block-tables">
    <el-form ref="dataForm" :model="temp" class="form-config-container">
      <el-descriptions class="margin-top" title="Config Captcha" :column="1" border>
        <el-descriptions-item v-for="(item,index) in dataConfig.captcha" :key="index" :label="item.detail">
          <!-- /// captcha method -->
          <el-popover
            v-if="index == 'captcha_method'"
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
            <span slot="reference" class="border-edit">{{ item.value ? item.value : 'Empty' }}</span>
          </el-popover>
          <!-- /// captcha page selected -->
          <el-popover
            v-else-if="index == 'captcha_page'"
            v-model="visible[1]"
            placement="top"
            title="Captcha page"
            width="400"
          >
            <el-form-item
              prop="captcha_page"
              :rules="[]"
            >
              <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">Check all</el-checkbox>

              <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                <el-checkbox v-for="(page,title) in dataConfig.captcha_page" :label="page" :key="title">{{page}}</el-checkbox>
              </el-checkbox-group>
            </el-form-item>
            <div style="text-align: right; margin: 12px 0px 0px 0px">
              <el-button-group>
                <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
                <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'admin_name')">Confirm</el-button>
              </el-button-group>
            </div>
            <span slot="reference" class="border-edit">{{ JSON.parse(item.value).length ? item.value : 'Empty' }}</span>
          </el-popover>
          <!-- /// active captcha -->
          <el-switch
            v-else-if="index == 'captcha_mode'"
            v-model="value2"
            active-color="#13ce66"
            inactive-color="#ff4949"
            active-value="1"
            inactive-value="0">
          </el-switch>
        </el-descriptions-item>
      </el-descriptions>
    </el-form>
  </div>
</template>

<script>

export default {
  name: 'ConfigCaptcha',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      value2:1,
      btnLoading:false,
      visible:[false,false],
      temp:{},
      checkAll: false,
      checkedCities: [],
      isIndeterminate: true
  	};
  },
  created(){

  },
  methods: {
    handleCheckAllChange(val) {
      this.checkedCities = val ? val : [];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.dataConfig.captcha_page.length;
      this.isIndeterminate = checkedCount > 0 && checkedCount < this.dataConfig.captcha_page.length;
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
