<template>
  <div>
    <el-row :gutter="20" class="grid-view">

      <el-col v-if="!isRootPath" :span="4">
        <div class="grid-content bg-purple" v-on:click="levelUp">
          <div class="block">
            <svg-icon class="folder" icon-class="level-up"/>
          </div>
        </div>
      </el-col>

      <el-col v-for="(directory, index) in directories"
              v-bind:key="`d-${index}`"
              v-bind:title="directory.basename"
              v-bind:class="{'active': checkSelect('directories', directory.path)}"
              :span="4">
        <div class="grid-content bg-purple" 
              v-on:click="selectItem('directories', directory.path, $event)"
              v-on:dblclick.stop="selectDirectory(directory.path)"
              v-on:contextmenu.prevent="contextMenu(directory, $event)">
          <div class="block">
            <svg-icon class="folder" v-bind:icon-class="(acl && directory.acl === 0) ? 'unlock' : 'storage'"/>
            <div class="box-txt" style="display:none;">
              <p class="title">{{ directory.basename }}</p>
            </div>
          </div>
        </div>
      </el-col>

      <el-col v-for="(file, index) in files"
              v-bind:key="`f-${index}`"
              v-bind:title="file.basename"
              v-bind:class="{'active': checkSelect('files', file.path)}"
              :span="4">
        <div class="grid-content bg-purple" 
              v-on:click="selectItem('files', file.path, $event)"
              v-on:dblclick="selectAction(file.path, file.extension)"
              v-on:contextmenu.prevent="contextMenu(file, $event)">
          <div class="block">
            <svg-icon v-if="acl && file.acl === 0" icon-class="unlock"/>
            <thumbnail v-else-if="thisImage(file.extension)"
                      v-bind:disk="disk"
                      v-bind:file="file">
            </thumbnail>
            <svg-icon v-else v-bind:icon-class="'file-extension'"/>
            <div class="box-txt" style="display:none;">
              <p class="title">{{ `${file.filename}.${file.extension}` }}</p>
              <p class="title">{{ bytesToHuman(file.size) }}</p>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
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
  .el-col-4{
    cursor:pointer;
    padding:10px;
    .grid-content{
      .block{
        text-align:center;
        position:relative;
        .box-txt{
          p{
            margin:0;
            display: inline-block;
            width: 80%;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
          }
          align-content: center;
          position: absolute;
          width: 100%;
          height: 100%;
          flex-wrap:wrap;
          top: 0;
          justify-content: center;
          align-items: center;
          background: #ffffff75;
          color: #000000;
        }
        svg{
          font-size:100px;
        }
      }
    }
    &:hover{
      .grid-content .block .box-txt{
        display:flex !important;
      }
    }
  }
  .active{
    border: 1px solid #b4bccc;
    border-radius: 5px;
    box-shadow: 0px 3px 8px 0px;
    .grid-content .block .box-txt{
      display:flex !important;
    }
  }
}
</style>