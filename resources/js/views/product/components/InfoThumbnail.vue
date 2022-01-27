<template>
  <div>
    <el-row>
      <el-col :span="20" :offset="2">
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
  data() {
    return {
      temp: {
        image: '',
      },
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
    EventBus.$on('getFileResponse', this.handlerGeturl);
  },
  methods: {
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
      this.$store.commit('fm/setDisks', 'category');
      this.componentUpload = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
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
</style>
