<template>
  <div>
    <el-form ref="dataGeneralForm" :model="temp" class="form-container" label-width="150px">
      <el-row class="el-main-form">
        <el-col :span="24">
          <div style="display: flex;justify-content: space-evenly;align-items: center;">
            <el-upload
              drag
              :multiple="false"
              :show-file-list="false"
              action="/"
              accept="image/jpeg,image/gif,image/png"
              :auto-upload="false"
              multiple
              :on-change="handleChange"
            >
              <i class="el-icon-upload" />
              <div class="el-upload__text">{{ $t('form.drop_file') }}</em></div>

            </el-upload>

            <div class="el-upload el-upload--text" @click="handleVisibleStorage()">
              <div class="el-upload-dragger">
                <i class="el-icon-upload" />
                <div class="el-upload__text">{{ $t('form.choose_storage') }}</div>
              </div>
            </div>
          </div>
          <div class="image-uploading">
              <div class="block" v-for="(item,index) in temp.images" :key="index">
                  <i class="el-icon-close cl-remv-img" @click="removeImage(index)"></i>
                  <el-avatar shape="square" style="width: auto;height: 200px;" fit="cover" :src="(typeof item === 'string' ? item : renderBlog(item))" v-if=""></el-avatar>
              </div>
          </div>

          <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
            <component :is="componentUpload" :get-file="true" />
          </el-dialog>
        </el-col>
      </el-row>
    </el-form>
    <el-row>
      <div class="pull-right">
        <el-button type="warning" icon="el-icon-arrow-left" @click="backStep">
          Previous
        </el-button>
        <el-popover
          v-model="visiblePopover"
          placement="top"
          width="360"
        >
          <div style="margin-bottom: 15px;">
            <el-tooltip content="Sort" placement="left">
              <el-input v-model.number="temp.sort" :placeholder="$t('table.sort')" :min="1">
                <template slot="append">
                  <el-tooltip :content="'Status' + ( temp.status == 1 ? ' Active' : ' Deactive' )" placement="top">
                    <el-switch
                      v-model="temp.status"
                      active-color="#13ce66"
                      inactive-color="#ff4949"
                      active-value="1"
                      inactive-value="0"
                    />
                  </el-tooltip>
                </template>
              </el-input>
            </el-tooltip>
          </div>
          <div style="text-align: right; margin: 0">
            <el-button size="mini" type="text" @click="visiblePopover = false">{{ $t('form.prev') }}</el-button>
            <el-button type="primary" size="mini" @click="done()">Done</el-button>
          </div>
          <el-button slot="reference" type="success" icon="el-icon-check">{{ $t('form.upload') }}</el-button>
        </el-popover>
      </div>
    </el-row>
  </div>
</template>

<script>
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
export default {
  name: 'InfoThumbnail',
  components: {
    FileManager,
  },
  props: ['dataActive', 'dataProduct'],
  data() {
    return {
      temp: {
        images: [
          'https://via.placeholder.com/350x350',
          'https://via.placeholder.com/450x350',
          'https://via.placeholder.com/550x350',
        ],
        sort: 1,
        status: '1',
      },
      visiblePopover: false,
      dialogStorageVisible: false,
      componentUpload: '',
    };
  },
  created() {
    if (Object.keys(this.dataProduct).length > 0) {
      if (this.dataProduct.image){
        this.temp.images = this.dataProduct.image.split(',');
      }
      if (this.dataProduct.sort){
        this.temp.sort = parseInt(this.dataProduct.sort);
      }
      if (this.dataProduct.status){
        this.temp.status = String(this.dataProduct.status);
      }
    }
  },
  methods: {
    renderBlog(obj){
      return URL.createObjectURL(obj);
    },
    backStep() {
      const active = this.dataActive - 1;
      this.$emit('handleProcessActive', active);
    },
    done(){
      this.$emit('handleProcessTemp', this.temp);
      this.$emit('handleProcessActive', this.dataActive);
      this.visiblePopover = false;
    },
    handlerGeturl(data) {
      if (data) {
        this.temp.images = [...this.temp.images,...data];
        this.dialogStorageClose();
      }
    },
    handleChange(file) {
      this.temp.images.push(file.raw);
    },
    handleVisibleStorage(){
      EventBus.$on('getFileResponse', this.handlerGeturl);
      this.$store.commit('fm/setDisks', 'product');
      this.componentUpload = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
      EventBus.$off('getFileResponse');
      this.componentUpload = '';
      this.dialogStorageVisible = false;
    },
    removeImage(index){
      this.temp.images.splice(index,1);
    }
  },
};
</script>
<style type="text/css">
  .image-uploading{
    margin: 20px 0px 20px 0px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-evenly;
    align-items: center;
  }
  .image-uploading .block{
    position: relative;
  }
  .image-uploading .block:hover .cl-remv-img{
    display: flex;
  }
  .cl-remv-img{
    position: absolute;
    z-index: 11;
    width: 100%;
    height: 100%;
    text-align: center;
    display: none;
    background: #60626654;
    color: #fff;
    font-size: 30px;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .el-main-form{
    height: calc(100vh - 320px);
    overflow-y: scroll;
  }
  .el-main-form::-webkit-scrollbar {
      width: 0;
      background: transparent;
  }
</style>
