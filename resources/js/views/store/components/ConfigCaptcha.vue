<template>
  <el-form class="form-config-container">
    <el-descriptions class="margin-top" title="Config Captcha" :column="1" border>
      <el-descriptions-item v-for="(item,index) in dataConfig.captcha" :key="index" :label="item.detail">
        <!-- /// captcha method -->
        <el-popover
          v-if="index == 'captcha_method'"
          v-model="visible[0]"
          placement="top"
          title="Admin name"
          width="200">
            <el-form-item
              prop="captcha_method">
              <el-select v-model="item.value" placeholder="Select" filterable @change="handleChangePlugins">
                <el-option
                  v-for="(type,i) in dataConfig.captchaInstalled"
                  :key="i"
                  :label="type"
                  :value="type"
                />
              </el-select>
            </el-form-item>
          <div style="text-align: right; margin: 12px 0px 0px 0px">
            <el-button-group>
              <el-button type="danger" size="mini" @click="handleCancel(0)">cancel</el-button>
              <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(item,0,'captcha_method')">Confirm</el-button>
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
          width="400">
          <el-form-item prop="captcha_page">
            <el-checkbox-group v-model="item.value" @change="handleCheckedPageCaptcha">
              <el-checkbox v-for="(page,id) in dataConfig.captcha_page" :label="id" :key="id">{{page}}</el-checkbox>
            </el-checkbox-group>
          </el-form-item>

          <div style="text-align: right; margin: 12px 0px 0px 0px">
            <el-button-group>
              <el-button type="danger" size="mini" @click="handleCancel(1)">cancel</el-button>
              <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(item,1,'captcha_page')">Confirm</el-button>
            </el-button-group>
          </div>
          <span slot="reference" class="border-edit" v-if="item.value.length">
            <span v-for="page in captchaPageTitle">
              {{ page }}<br>
            </span> 
          </span>
          <span v-else slot="reference" class="border-edit">
            Empty
          </span>
        </el-popover>
        <!-- /// active captcha -->
        <el-switch
          v-else-if="index == 'captcha_mode'"
          v-model="item.value"
          @change="handleActiveCaptcha(item)"
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
  name: 'ConfigCaptcha',
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
      captchaInstalled:'',
  	};
  },
  created(){
    this.dataConfig.captcha.captcha_page.value = JSON.parse(this.dataConfig.captcha.captcha_page.value);
  },
  computed:{
    captchaPageTitle(){
      let arr = [];
      let key = this.dataConfig.captcha.captcha_page.value;
      for(let prop in this.dataConfig.captcha_page) {
        let res = key.filter((item) => item === prop);
        if (res.length > 0) {
          arr.push(this.dataConfig.captcha_page[prop]);
        }
      };
      return arr;
    }
  },
  methods: {
    handleChangePlugins(val){
      let obj = JSON.parse(JSON.stringify(this.dataConfig.captchaInstalled));
      let res;
      for(let prop in obj){
        if (obj[prop] == val) {
          res = prop;
        }
      }
      this.captchaInstalled = res;
    },
    handleActiveCaptcha(data){
      this.$emit('handleUpdate', data);
    },
    handleCheckedPageCaptcha(value) {
      console.log(value);
    },
    handleConfirm(item,i,type){
      this.btnLoading = true;
      item = {...item};
      if (type == 'captcha_method') {
        item.value = this.captchaInstalled;
      }
      this.$emit('handleUpdate', item);
      this.btnLoading = false;
      this.visible[i] = false;
    },
    handleCancel(i){
      this.visible[i] = false;
    }
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
