<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="12" :offset="6">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form v-show="!loading" ref="dataForm" :model="temp" :rules="rules" class="form-container" label-width="200px">

        <el-form-item :label="$t('user.first_name')" prop="first_name">
          <el-input
            v-model="temp.first_name"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.last_name')" prop="last_name">
          <el-input
            v-model="temp.last_name"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.email')" prop="email">
          <el-input
            v-model="temp.email"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('user.role')" prop="role">
          <el-select v-model="temp.role" placeholder="Select role" prop="role" filterable style="width: 100%;">
              <el-option v-for="item in roles" :key="item.id" :label="item.name | uppercaseFirst" :value="item.name" />
          </el-select>
        </el-form-item>


        <el-form-item :label="$t('user.password')" prop="password">
          <el-input v-model="temp.password" show-password />
        </el-form-item>

        <el-form-item :label="$t('user.confirmPassword')" prop="confirmPassword">
          <el-input v-model="temp.confirmPassword" show-password />
        </el-form-item>

        <el-button class="pull-right" type="success" icon="el-icon-check" @click="createData()">
          Done
        </el-button>
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import RoleResource from '@/api/role';
import UserResource from '@/api/user';
const roleResource = new RoleResource();
const userResource = new UserResource();


const defaultForm = {
    name:'',
    email:'',
    password:'',
    confirmPassword:'',
    role:'',
};

export default {
  name: 'UserCreate',
  data() {
    return {
      loading:true,
      roles:[],
      temp:Object.assign({},defaultForm),
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
      },
    };
  },
  created() {
    this.getListRole();
  },
  methods: {
    async getListRole(){
      const data = await roleResource.list();
      this.roles = data.data;
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

              this.temp = Object.assign({}, defaultForm);

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
