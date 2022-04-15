<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="12" :offset="6">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form v-show="!loading" ref="dataForm" :model="dataTemp" :rules="rules" class="form-container" label-width="200px">

        <el-form-item :label="$t('user.fullname')" prop="fullname">
          <el-input
            v-model="dataTemp.fullname"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.phone')" prop="phone">
          <el-input
            v-model="dataTemp.phone"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.email')" prop="email">
          <el-input
            v-model="dataTemp.email"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.role')" prop="role">
          <el-select multiple v-model="dataTemp.roles" placeholder="Select role" prop="role" filterable style="width: 100%;">
            <el-option v-for="item in roles" :key="item.id" :label="item.name | uppercaseFirst" :value="item.id" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('user.permissions')" prop="permissions">
          <el-select multiple v-model="dataTemp.permissions" placeholder="Select permissions" prop="permissions" filterable style="width: 100%;">
            <el-option v-for="item in permissions" :key="item.id" :label="item.name | uppercaseFirst" :value="item.id" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('user.store')" prop="store">
          <el-select v-model="dataTemp.stores" multiple placeholder="Select store" prop="store" filterable style="width: 100%;">
            <el-option v-for="(item,index) in storeList" :key="index" :label="item.descriptions_current_lang[0].title | uppercaseFirst" :value="index" />
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('user.password')" prop="password">
          <el-input v-model="dataTemp.password" show-password>
            <el-button @click="generatePw" slot="append" icon="el-icon-refresh"></el-button>
          </el-input>
        </el-form-item>

        <el-button class="pull-right" type="success" icon="el-icon-check" @click="isEdit ? updateData() : createData()">
          Done
        </el-button>
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import PermissionsResource from '@/api/permissions';
import RoleResource from '@/api/role';
import UserResource from '@/api/user';
const roleResource = new RoleResource();
const permissionsResource = new PermissionsResource();
const userResource = new UserResource();


export default {
  name: 'UserDetail',
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    dataTemp: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      loading:true,
      roles: [],
      permissions: [],
      rules: {
        email: [
          {
            required: true,
            message: 'Please input email address',
            trigger: 'blur',
          },
          {
            type: 'email',
            message: 'Please input correct email address',
            trigger: ['blur', 'change'],
          },
        ],
        phone: [
          {
            required: true,
            message: 'Please input phone',
            trigger: 'blur',
          },
        ],
        fullname: [
          {
            required: true,
            message: 'Please input full name',
            trigger: 'blur',
          },
        ],
        password: [
          {
            required: true,
            message: 'Please input password',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  computed:{
    storeList(){
      return this.$store.state.user.storeList;
    }
  },
  created() {
    this.getListRole();
    this.getListPermissions();
    if (!this.isEdit) {
      this.generatePw();
    }
      console.log(this.dataTemp);
  },
  methods: {
    generatePw () {
      let charactersArray = 'a-z,A-Z,0-9,#'.split(',');  
      let CharacterSet = '';
      let password = '';
      
      if( charactersArray.indexOf('a-z') >= 0) {
        CharacterSet += 'abcdefghijklmnopqrstuvwxyz';
      }
      if( charactersArray.indexOf('A-Z') >= 0) {
        CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      }
      if( charactersArray.indexOf('0-9') >= 0) {
        CharacterSet += '0123456789';
      }
      if( charactersArray.indexOf('#') >= 0) {
        CharacterSet += '![]{}()%&*$#^<>~@|';
      }
      
      for(let i=0; i < 8; i++) {
        password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length));
      }
      this.dataTemp.password = password;
    },
    async getListRole(){
      const data = await roleResource.list();
      this.roles = data.data;
    },
    async getListPermissions(){
      const data = await permissionsResource.list();
      this.permissions = data.data;
      this.loading = false;
    },
    goBackList(){
      this.$router.push({ name: 'UsersList' });
    },
    createData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          userResource.store(this.temp).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });

              const view = this.$router.resolve({ name: 'UsersList' }).route;
              this.$store.dispatch('tagsView/delCachedView', view).then(() => {
                const { fullPath } = view;
                this.$nextTick(() => {
                  this.$router.replace({
                    path: '/redirect' + fullPath,
                  });
                });
              });
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
    updateData(){

    }
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .image-uploading{
    position: relative;
    .el-icon-close{
        cursor: pointer;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 18px;
        opacity: 0;
        transition:all .5s;
    }
    &:hover {
      .el-icon-close{
        opacity: 1;
      }
    }
  }
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    vertical-align: bottom;
  }
  .el-main-form{
    height: calc(100vh - 142px);
    overflow-y: scroll;
  }
</style>
