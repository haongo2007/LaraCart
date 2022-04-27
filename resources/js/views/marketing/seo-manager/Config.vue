<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:24px">
      <el-col :span="24">
        <el-tabs type="card" v-model="activeName">
          <el-tab-pane label="URL" name="url">
            <el-switch
              @change="handleActive()"
              v-model="value"
              active-text="Add language on URL"
              active-value="1"
              inactive-value="0">
            </el-switch>
          </el-tab-pane>
        </el-tabs>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import {updateGlobalConfig} from '@/api/global-config';
import SeoConfigtesource from '@/api/seo';

const seoConfigtesource = new SeoConfigtesource();
export default {
  name: 'SeoConfig',
  data(){
    return {
      value:'0',
      activeName: 'url'
    };
  },
  created(){
    this.getList();
  },
  methods: {
    async getList(){
      const data = await seoConfigtesource.list();
      this.value = String(data);
    },
    handleActive() {
      updateGlobalConfig({name:'url_seo_lang',value:this.value})
      .then(response => {
        this.$message({
          type: 'success',
          message: 'Update successfully',
        });
      })
      .catch(err => {
        console.log(err);
      });
    }
  },
};
</script>
<style type="text/css">
  .form-config-container{
    border: 1px solid #eee;
    width: 50%;
    padding: 20px;
    border-radius: 5px;
  }
</style>
