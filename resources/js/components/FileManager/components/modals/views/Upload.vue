<template>
    <div>
      <el-row style="text-align:center">
        <el-col :span="24" style="padding:10px 0">
          <div class="upload-storage">
            <div class="el-upload el-upload--text" @click="trigger" @dragover.prevent @drop.stop.prevent="onDrop">
              <div class="el-upload-dragger" v-show="!progressBar">
                <i class="el-icon-upload"></i> 
                <div class="el-upload__text">
                  Drop file here or 
                  <em>click to upload</em>
                  <p>(jpg/png files with a size less than 500kb)</p>
                </div>
                <input name="myfile" v-on:change="selectFiles($event)" type="file" ref="fileInput" multiple class="el-upload__input">
              </div>
            </div>
            <div v-if="countFiles">
              <ul class="el-upload-list el-upload-list--text">
                <li v-for="(item, index) in newFiles"
                    :key="index"
                    class="el-upload-list__item is-success">
                  <a class="el-upload-list__item-name"><i class="el-icon-document"></i>
                  {{ item.name }} / {{ bytesToHuman(item.size) }}
                  </a>
                  <label class="el-upload-list__item-status-label">
                    <i class="el-icon-upload-success el-icon-circle-check"></i>
                  </label>
                  <i class="el-icon-close" @click="onRemove(index)"></i>
                </li>
              </ul>
              <hr>
              <ul class="el-upload-list el-upload-list--text ul-2">
                <li class="el-upload-list__item is-success">
                  <a><strong>{{ lang.modal.upload.selected }}</strong> {{ newFiles.length }}</a>
                  <a><strong>{{ lang.modal.upload.size }}</strong> {{ allFilesSize }}</a>
                </li>
                <li class="el-upload-list__item is-success">
                  <div>
                    <strong>{{ lang.modal.upload.ifExist }}</strong> 
                  </div>
                  <div>
                    <el-radio name="uploadOptions" v-model="overwrite" :label="0">{{ lang.modal.upload.skip }}</el-radio>
                    <el-radio name="uploadOptions" v-model="overwrite" label="1">{{ lang.modal.upload.overwrite }}</el-radio>
                  </div>
                </li>
              </ul>
            </div>
            <div class="el-upload__tip"  v-else>{{ lang.modal.upload.noSelected }}</div>
            <el-progress :percentage="percentage" v-bind:color="increase(progressBar)" v-show="countFiles"></el-progress>
          </div>
        </el-col>
        <el-col :span="24" style="padding:10px 0">
          <el-button-group>
            <el-button type="primary" icon="el-icon-upload" 
              v-bind:type="countFiles ? 'primary' : 'default'"
              v-bind:disabled="!countFiles"
              v-on:click="uploadFiles" />
            <el-button type="primary" v-on:click="hideModal" icon="el-icon-close" />
          </el-button-group>
        </el-col>
      </el-row>
    </div>
</template>

<script>
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';
import helper from './../../../mixins/helper';

export default {
  name: 'Upload',
  mixins: [modal, translate, helper],
  data() {
    return {
      // selected files
      newFiles: [],

      // overwrite if exists
      overwrite: 0,
      // progress bar
      percentage: 0,
      customColor: '#6f7ad3',
    };
  },
  computed: {

    /**
     * Progress bar value - %
     * @returns {number}
     */
    progressBar() {
      return this.$store.state.fm.messages.actionProgress;
    },

    /**
     * Count of files selected for upload
     * @returns {number}
     */
    countFiles() {
      return this.newFiles.length;
    },

    /**
     * Calculate the size of all files
     * @returns {*|string}
     */
    allFilesSize() {
      let size = 0;

      for (let i = 0; i < this.newFiles.length; i += 1) {
        size += this.newFiles[i].size;
      }

      return this.bytesToHuman(size);
    },

  },
  methods: {
    increase(percentage) {
      this.percentage = percentage;
    },
    onRemove : function(index){
      this.newFiles.splice(index,1);
    },
    onDrop(e) {
      if (this.newFiles.length === 0) {
        this.newFiles = Array.from(event.dataTransfer.files);
      }else{
        Array.from(event.dataTransfer.files).forEach(e => this.newFiles.push(e));
      }
    },
    trigger () {
      this.$refs.fileInput.click()
    },
    /**
     * Select file or files
     * @param event
     */
    selectFiles(event) {
      // files selected?
      if (this.newFiles.length === 0) {
        this.newFiles = Array.from(event.target.files);
      }else{
        Array.from(event.target.files).forEach(e => this.newFiles.push(e));
      }
    },

    /**
     * Upload new files
     */
    uploadFiles() {
      // if files exists
      if (this.countFiles) {
        // upload files
        this.$store.dispatch('fm/upload', {
          files: this.newFiles,
          overwrite: this.overwrite,
        }).then((response) => {
          // if upload is successful
          if (response.data.result.status === 'success') {
            // close modal window
            this.hideModal();
          }
        });
      }
    },
  },
};
</script>

<style lang="scss">
.upload-storage{
  max-width:400px;
  margin:0 auto;
  .el-upload--text{
    width:100%;
    .el-upload-dragger{
      width:100%;
    }
  }
  .ul-2 li{
    display:flex; 
    justify-content: space-between;
    align-items: center;
  }
}
</style>
