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
              :on-change="handleChange"
            >
              <i class="el-icon-upload" />
              <div class="el-upload__text">Drop file here or <em>click to upload</em></div>

            </el-upload>

            <div class="el-upload el-upload--text" @click="handleVisibleStorage()">
              <div class="el-upload-dragger">
                <i class="el-icon-upload" />
                <div class="el-upload__text">Click here to Open <em>Storage</em></div>
              </div>
            </div>
          </div>
          <div class="image-uploading">
            <el-image v-for="(item,index) in fileUrl" :key="index" :style="'width:'+ item.width+'px; height:'+ item.height+'px'" :src="item.value" fit="cover">
              <div slot="error" class="image-slot">
                <i class="el-icon-picture-outline" />
              </div>
            </el-image>
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
            <el-button size="mini" type="text" @click="visiblePopover = false">Cancel</el-button>
            <el-button type="primary" size="mini" @click="done()">Done</el-button>
          </div>
          <el-button slot="reference" type="success" icon="el-icon-check">Upload</el-button>
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
        image: '',
        sort: 1,
        status: '1',
      },
      visiblePopover: false,
      fileUrl: [
        {
          value: 'https://via.placeholder.com/350x350',
          height: 350,
          width: 350,
        },
        {
          value: 'https://via.placeholder.com/450x350',
          height: 350,
          width: 450,
        },
        {
          value: 'https://via.placeholder.com/550x350',
          height: 350,
          width: 550,
        },
      ],
      dialogStorageVisible: false,
      componentUpload: '',
    };
  },
  created() {
    if (Object.keys(this.dataProduct).length > 0) {
      if (this.dataProduct.image){
        this.temp.image = this.dataProduct.image;
        this.fileUrl = [];
        this.fileUrl.push({ value: this.temp.image + '&w=350', height: 350, width: 350 });
        this.fileUrl.push({ value: this.temp.image + '&w=450', height: 350, width: 450 });
        this.fileUrl.push({ value: this.temp.image + '&w=550', height: 350, width: 550 });
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
        this.fileUrl = [];
        this.fileUrl.push({ value: data + '&w=350', height: 350, width: 350 });
        this.fileUrl.push({ value: data + '&w=450', height: 350, width: 450 });
        this.fileUrl.push({ value: data + '&w=550', height: 350, width: 550 });

        this.temp.image = data;
        this.dialogStorageClose();
      }
    },
    handleChange(file) {
      this.temp.image = file.raw;
      this.fileUrl = [];
      this.fileUrl.push({ value: URL.createObjectURL(file.raw), height: 350, width: 350 });
      this.fileUrl.push({ value: URL.createObjectURL(file.raw), height: 350, width: 450 });
      this.fileUrl.push({ value: URL.createObjectURL(file.raw), height: 350, width: 550 });
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
    resetImageUpload(){
      this.temp.image = '';
      this.componentUpload = '';
      this.fileUrl = '';
    },
  },
};
</script>
<style type="text/css">
  .image-uploading{
    margin: 20px 0px 20px 0px;
    display: flex;
    justify-content: space-around;
    align-items: center;
  }
  .el-main-form{
    height: calc(100vh - 360px);
    overflow-y: scroll;
  }
  .el-main-form::-webkit-scrollbar {
      width: 0;
      background: transparent;
  }
</style>
