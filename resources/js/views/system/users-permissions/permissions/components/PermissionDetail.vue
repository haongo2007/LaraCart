<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="20" :offset="2">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form v-show="!loading" ref="dataForm" :model="dataTemp" :rules="rules" class="form-container" label-width="200px">

        <el-form-item :label="$t('permission.name')" prop="name">
          <el-input
            style="width: 30%"
            v-model="dataTemp.name"
            placeholder="Please input"
            clearable/>
        </el-form-item>

        <el-form-item :label="$t('permission.slug')" prop="slug">
          <el-input
            style="width: 30%"
            v-model="dataTemp.slug"
            placeholder="Please input"
            clearable/>
        </el-form-item>

        <el-form-item :label="$t('permission.path')" prop="path">
          <div class="parent-options">
            <div class="child-options"  v-for="(info,index) in path" :key="index">
              <el-checkbox v-model="checkList[index]['value']" :indeterminate="isIndeterminatePath" @change="handleCheckAllChange">{{index}}</el-checkbox>
              <div class="child-child-options" v-if="info.length">
                <el-checkbox-group v-model="checkList[index]['children'][key]" v-for="(route,key) in info" :key="key">
                  <el-checkbox :label="route.uri">{{ route.uri }}</el-checkbox>
                </el-checkbox-group>
              </div>
            </div>
          </div>
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
const permissionsResource = new PermissionsResource();

export default {
  name: 'PermissionDetail',
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
      isIndeterminatePath:false,
      checkList:{},
      loading:true,
      path: [],
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
      },
    };
  },
  created() {
    this.getAllPath();
  },
  methods: {
    handleCheckAllChange(){

    },
    handleCheckedChangePath(){

    },
    getAllPath(){
      permissionsResource.getAllPath().then(data => {
        this.path = data.data;
        for(const prop in this.path){
          this.$set(this.checkList,prop,{});
          this.$set(this.checkList[prop],'value',false);
          this.$set(this.checkList[prop],'children',[]);

          for(const child in this.path[prop]){
            this.$set(this.checkList[prop]['children'],child,false);
          }
        }
        this.loading = false;
      });
    },
    goBackList(){
      this.$router.push({ name: 'PermissionsList' });
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
  .parent-options{
    display: flex;
    flex-wrap:wrap;
    .child-options{
      width: 32%;
      border: 1px solid #dcdfe6;
      border-radius: 5px;
      padding: 0 10px;
      margin: 5px;
      .child-child-options{
        margin-left: 25px;
      }
    }
  }
</style>
