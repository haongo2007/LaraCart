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
        <el-form-item :label="$t('user.role')" prop="roles">
          <el-popover
            placement="right"
            width="600"
            trigger="click">
            <el-checkbox :indeterminate="isIndeterminateRoles" v-model="checkAllRoles" @change="handleCheckAllChangeRoles" style="margin:5px;">Check all</el-checkbox>
            <el-checkbox-group v-model="dataTemp.roles" @change="handleCheckedChangeRoles">
              <div class="check-box">
                <el-checkbox v-for="role in roles" :label="role.id" :key="role.id">{{ role.name }}</el-checkbox>
              </div>
            </el-checkbox-group>
            <el-button slot="reference" type="primary">Open</el-button>
          </el-popover>
        </el-form-item>

        <el-form-item :label="$t('user.permissions')" prop="permissions">
          <el-popover
            placement="right"
            width="600"
            trigger="click">
            <el-checkbox :indeterminate="isIndeterminatePermissions" v-model="checkAllPermissions" @change="handleCheckAllChangePermissions" style="margin:5px;">Check all</el-checkbox>
            <el-checkbox-group v-model="dataTemp.permissions" @change="handleCheckedChangePermissions">
              <div class="check-box">
                <el-checkbox v-for="permission in permissions" :label="permission.id" :key="permission.id">{{ permission.name }}</el-checkbox>
              </div>
            </el-checkbox-group>
            <el-button slot="reference" type="primary">Open</el-button>
          </el-popover>
        </el-form-item>

        <el-form-item :label="$t('user.store')" prop="stores">
          <el-select v-model="dataTemp.stores" multiple placeholder="Select store" style="width: 100%;">
            <el-option v-for="(item,index) in storeList" :key="item.id" :label="item.descriptions_current_lang[0].title | uppercaseFirst" :value="item.id" />
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
import reloadRedirectToList from '@/utils';
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
    var checkStore = (rule, value, callback) => {
      setTimeout(() => {
        if (!value.length) {
          callback(new Error('Please choose store'));
        }else{
          callback();
        }
      }, 500);
    };
    return {
      loading:true,
      checkAllRoles: false,
      isIndeterminateRoles: false,
      checkAllPermissions: false,
      isIndeterminatePermissions: false,
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
        roles: [
          {
            required: true,
            message: 'Please choose role',
            trigger: 'change',
          },
        ],
        permissions: [
          {
            required: true,
            message: 'Please choose permission',
            trigger: 'change',
          },
        ],
        password: [
          {
            required: true,
            message: 'Please input password',
            trigger: 'change',
          },
        ],
        stores: [
          { validator: checkStore, trigger: 'change' }
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
    async getListRole(contain = 'all'){
      const data = await roleResource.list({limit:'all'});
      this.roles = data.data;
      if (this.dataTemp.roles.length == this.roles.length) {
        this.checkAllRoles = true;
      }
    },
    async getListPermissions(){
      const data = await permissionsResource.list({limit:'all'});
      this.permissions = data.data;
      this.loading = false;
      if (this.dataTemp.permissions.length == this.permissions.length) {
        this.checkAllPermissions = true;
      }
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
          userResource.store(this.dataTemp).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });

              const view = this.$router.resolve({ name: 'UserCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);

              reloadRedirectToList('UsersList')
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
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          userResource.update(this.dataTemp.id,this.dataTemp).then((res) => {
            reloadRedirectToList('UsersList');

            this.$message({
              type: 'success',
              message: 'Updated successfully',
            });

            loading.close();
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
    filterData(data){
      let res = [];
      data.forEach(function (e,i) {
        res.push(e.id);
      });
      return res;
    },
    handleCheckAllChangeRoles(val) {
      this.dataTemp.roles = val ? this.filterData(this.roles) : [];
      this.isIndeterminateRoles = false;
    },
    handleCheckedChangeRoles(value) {
      let checkedCount = value.length;
      this.checkAllRoles = checkedCount === this.roles.length;
      this.isIndeterminateRoles = checkedCount > 0 && checkedCount < this.roles.length;
    },
    handleCheckAllChangePermissions(val) {
      this.dataTemp.permissions = val ? this.filterData(this.permissions) : [];
      this.isIndeterminatePermissions = false;
    },
    handleCheckedChangePermissions(value) {
      let checkedCount = value.length;
      this.checkAllPermissions = checkedCount === this.permissions.length;
      this.isIndeterminatePermissions = checkedCount > 0 && checkedCount < this.permissions.length;
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
  .check-box{
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
    .el-checkbox{
      width: 30%;
      margin: 5px;
    }
  }
</style>
