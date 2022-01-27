<template>
  <transition name="fm-modal">
    <div ref="fmModal">
      <el-dialog
        append-to-body
        :title="modalName"
        :visible.sync="centerDialogVisible"
        :before-close="hideModal"
        role="document"
        :class="modalSize"
        @click.stop
      >
        <component :is="modalName" />
      </el-dialog>
    </div>
  </transition>
</template>

<script>
import NewFile from './views/NewFile.vue';
import NewFolder from './views/NewFolder.vue';
import Upload from './views/Upload.vue';
import Delete from './views/Delete.vue';
import Clipboard from './views/Clipboard.vue';
import Status from './views/Status.vue';
import Rename from './views/Rename.vue';
import Properties from './views/Properties.vue';
import Preview from './views/Preview.vue';
import TextEdit from './views/TextEdit.vue';
import AudioPlayer from './views/AudioPlayer.vue';
import VideoPlayer from './views/VideoPlayer.vue';
import Zip from './views/Zip.vue';
import Unzip from './views/Unzip.vue';

export default {
  name: 'Modal',
  components: {
    NewFile,
    NewFolder,
    Upload,
    Delete,
    Clipboard,
    Status,
    Rename,
    Properties,
    Preview,
    TextEdit,
    AudioPlayer,
    VideoPlayer,
    Zip,
    Unzip,
  },
  data() {
    return {
      centerDialogVisible: this.$store.state.fm.modal.showModal,
    };
  },
  computed: {
    /**
     * Selected modal name
     * @returns {null|*}
     */
    modalName() {
      return this.$store.state.fm.modal.modalName;
    },

    /**
     * Modal size style
     * @returns {{'modal-lg': boolean, 'modal-sm': boolean}}
     */
    modalSize() {
      return {
        'modal-xl': this.modalName === 'Preview' || this.modalName === 'TextEdit',
        'modal-lg': this.modalName === 'VideoPlayer',
        'modal-sm': false,
      };
    },
  },
  mounted() {
    // set height
    this.$store.commit('fm/modal/setModalBlockHeight', this.$refs.fmModal.offsetHeight);
  },
  methods: {
    /**
     * Hide modal window
     */
    hideModal() {
      this.$store.commit('fm/modal/clearModal');
    },
  },
};
</script>
<style lang="scss">
    .modal-xl {
        .el-dialog{
          width:80%;
        }
    }
</style>
