<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="12" :offset="6">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form v-show="!loading" ref="dataForm" :model="dataTemp" :rules="rules" class="form-container" label-width="200px">

        <el-form-item :label="$t('roles.name')" prop="name">
          <el-input
            v-model="dataTemp.name"
            placeholder="Please input"
            clearable/>
        </el-form-item>

        <el-form-item :label="$t('roles.slug')" prop="slug">
          <el-input
            v-model="dataTemp.slug"
            placeholder="Please input"
            clearable/>
        </el-form-item>

        <el-form-item :label="$t('roles.permissions')" prop="permissions">
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
const roleResource = new RoleResource();
const permissionsResource = new PermissionsResource();


export default {
  name: 'RoleDetail',
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
      checkAllPermissions: false,
      isIndeterminatePermissions: false,
      permissions: [],
      rules: {
        name: [
          {
            required: true,
            message: 'Please input name',
            trigger: 'blur',
          },
        ],
        slug: [
          {
            required: true,
            message: 'Please input slug',
            trigger: 'blur',
          },
        ],
        permissions: [
          {
            required: true,
            message: 'Please choose permission',
            trigger: 'change',
          },
        ],
      },
    };
  },
  created() {
    this.getListPermissions();
    
  },
  methods: {
    async getListPermissions(){
      const data = await permissionsResource.list({limit:'all'});
      this.permissions = data.data;
      this.loading = false;
    },
    goBackList(){
      this.$router.push({ name: 'RolesList' });
    },
    createData(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          this.loading = true;
          roleResource.store(this.dataTemp).then((res) => {
            if (res) {
              this.loading = false;
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });

              const view = this.$router.resolve({ name: 'RoleCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);

              reloadRedirectToList('RolesList')
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              this.loading = false;
            }
          }).catch(err => {
            console.log(err);
            this.loading = false;
          });
        }
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {          
          this.loading = true;
          roleResource.update(this.dataTemp.id,this.dataTemp).then((res) => {
            reloadRedirectToList('RolesList');

            this.$message({
              type: 'success',
              message: 'Updated successfully',
            });

            this.loading = false;
          }).catch(err => {
            console.log(err);
            this.loading = false;
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
