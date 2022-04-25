<template>
  <div>
    <div class="grid-view">
      <div class="card file-manager-item levelUp" v-if="!isRootPath" v-on:click="levelUp">
          <i class="el-icon-top"></i>
      </div>

      <div class="card file-manager-item folder" 
        v-for="(directory, index) in directories" 
        v-bind:key="`d-${index}`"
        v-bind:title="directory.basename" 
        v-bind:class="{'active': checkSelect('directories', directory.path)}"
        v-on:click="selectItem('directories', directory.path, $event)"
        v-on:dblclick.stop="selectDirectory(directory.path)"
        v-on:contextmenu.prevent="contextMenu(directory, $event)">
        <div class="file-logo-wrapper">
          <div class="icon">
            <i class="el-icon-folder" />
          </div>
        </div>
        <div class="card-body">
          <div class="content-wrapper">
            <p class="card-text file-name">{{ directory.basename }}</p>
            <p class="card-text file-size"></p>
          </div>
          <small class="file-accessed text-muted">Last modified: {{ timestampToDate(directory.timestamp) }}</small>
        </div>
      </div>



      <div class="card file-manager-item file" 
          v-for="(file, index) in files"
          v-bind:key="`f-${index}`"
          v-bind:title="file.basename"
          v-bind:class="{'active': checkSelect('files', file.path)}"
          v-on:click="selectItem('files', file.path, $event)"
          v-on:dblclick="selectAction(file.path, file.extension)"
          v-on:contextmenu.prevent="contextMenu(file, $event)">
          <div class="file-logo-wrapper">
            <div class="icon" v-if="acl && file.acl === 0" >
              <svg-icon class="unlock"/>
            </div>
            <thumbnail v-else-if="thisImage(file.extension)"
                      v-bind:disk="disk"
                      v-bind:file="file">
            </thumbnail>
            <div class="icon" v-else>
              <svg-icon v-bind:icon-class="'file-extension'"/>
            </div>
          </div>
          <div class="card-body">
            <div class="content-wrapper">
              <p class="card-text file-name">{{ `${file.filename}.${file.extension}` }}</p>
              <p class="card-text file-size">{{ bytesToHuman(file.size) }}</p>
            </div>
            <small class="file-accessed text-muted">Last modified: {{ timestampToDate(file.timestamp) }}</small>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import translate from './../../mixins/translate';
import helper from './../../mixins/helper';
import managerHelper from './mixins/manager';
import Thumbnail from './Thumbnail.vue';

export default {
  name: 'grid-view',
  components: { Thumbnail },
  mixins: [translate, helper, managerHelper],
  data() {
    return {
      disk: '',
    };
  },
  props: {
    manager: { type: String, required: true },
  },
  mounted() {
    this.disk = this.selectedDisk;
  },
  beforeUpdate() {
    // if disk changed
    if (this.disk !== this.selectedDisk) {
      this.disk = this.selectedDisk;
    }
  },
  computed: {
    /**
     * Image extensions list
     * @returns {*}
     */
    imageExtensions() {
      return this.$store.state.fm.settings.imageExtensions;
    },
  },
  methods: {
    /**
     * Check file extension (image or no)
     * @param extension
     * @returns {boolean}
     */
    thisImage(extension) {
      // extension not found
      if (!extension) return false;

      return this.imageExtensions.includes(extension.toLowerCase());
    },
  },
};
</script>
<style lang="scss">
.grid-view{
  display: flex;
  padding: 20px;
  .active{
    box-shadow: 0 0 3px 1px #409EFF;
  }
  .card{
    cursor: pointer;
    width: 12.5%;
    background: #f8f8f8;
    border: 1px solid #ebe9f1;
    border-radius: 0.428rem;
    margin: 10px;
    .file-logo-wrapper{
      font-size: 0px;
      .icon{
        display: flex;
        align-items:center;
        justify-content: center;
        height: 150px;
        font-size: 80px;
      }
    }
    .card-body{
      background:#ffff;
      padding:10px;
      border-radius:0rem 0rem 0.428rem 0.428rem;
      .content-wrapper{
        display: flex;
        justify-content: space-between;
        align-items: center;
        .card-text{
          margin: 0px; 
        }
        .file-name{
          width: 60%;
          font-weight: 600;
          margin: 5px 0;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
      }
    }
  }
  .levelUp{
    display: flex;
    justify-content: center;
    align-items:center;
    font-size: 55px;
    padding:10px;
  }
}
</style>