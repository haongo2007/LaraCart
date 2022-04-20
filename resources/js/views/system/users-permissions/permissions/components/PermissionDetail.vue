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
            <div class="child-options" v-for="(info,index) in path" :key="index">
              <el-checkbox v-model="checkList[index]['value']" @change="handleCheckAllChange(index)" 
              :label="index">{{index | uppercaseFirst}}</el-checkbox>
              <div class="child-child-options" v-if="info.length">
                <el-checkbox-group v-for="(route,key) in info" :key="key" v-model="checkList[index]['children'][key]" @change="handleCheckChange(index,key)" 
                style="overflow: hidden;">
                  <el-tooltip :open-delay="500" class="item" effect="dark" :content="route.uri" placement="left">
                    <el-checkbox :key="route.uri" :label="route.uri" >{{ route.uri }}</el-checkbox>
                  </el-tooltip>
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
    handleCheckAllChange(key){
      let that = this;
      this.checkList[key].children.forEach(function(v,i){
        that.checkList[key].children[i] = that.checkList[key].value === true ? true : false;
      });
    },
    handleCheckChange(props,prop){
      let check = this.checkList[props].children.filter(item => item === false);
      if (check.length == 0) {
        this.checkList[props].value = true;
      }else{
        this.checkList[props].value = false;
      }
    },
    async getAllPath(){
      let uri = [];
      let that = this;
      if (this.isEdit) {
        uri = this.dataTemp.uri;
      }
      await permissionsResource.getAllPath().then(data => {
        this.path = data.data;
        for(const props in this.path){
          let leng = this.path[props].length;
          let deepLeng = 0;
          let checked = false;
          if (uri.length) {
            for(let i in uri){
              if (uri[i] == this.path[props].uri) {
                checked = true;
              }
            }
          }
          this.$set(this.checkList,props,{});
          this.$set(this.checkList[props],'value',checked);
          this.$set(this.checkList[props],'children',[]);
          for(const prop in this.path[props]){
            let checked = false;
            if (uri.length) {
              for(let i in uri){
                if (uri[i] == this.path[props][prop].uri) {
                  checked = true;
                  deepLeng++;
                }
              }
            }
            this.$set(this.checkList[props]['children'],prop,checked);
          }
          if (length != deepLeng) {
            this.checkList[props].value = true;
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
        margin: 0px 25px;
      }
    }
  }
</style>
