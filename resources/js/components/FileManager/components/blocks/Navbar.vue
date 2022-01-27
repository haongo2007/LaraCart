<template>
  <div>
    <el-row :gutter="20" style="margin-top:10px;">
      <el-col :span="24">
        <el-menu class="el-menu-demo" mode="horizontal">

          <el-menu-item index="1" :disabled="backDisabled" @click="historyBack()"><i class="fas fa-step-backward" /></el-menu-item>
          <el-menu-item index="2" @click="refreshAll()"><i class="fas fa-sync-alt" /></el-menu-item>
          <el-menu-item index="3" :disabled="forwardDisabled" @click="historyForward()"><i class="fas fa-step-forward" /></el-menu-item>

          <el-submenu index="4">
            <template slot="title">Clipboard</template>
            <el-menu-item
              index="4-1"
              :disabled="!isAnyItemSelected"
              @click="toClipboard('copy')"
            >
              <i class="fas fa-copy" /> {{ lang.btn.copy }}</el-menu-item>
            <el-menu-item
              index="4-2"
              :disabled="!isAnyItemSelected"
              @click="toClipboard('cut')"
            >
              <i class="fas fa-cut" /> {{ lang.btn.cut }}</el-menu-item>
            <el-menu-item
              index="4-3"
              :disabled="!clipboardType"
              @click="paste"
            >
              <i class="fas fa-paste" /> {{ lang.btn.paste }}</el-menu-item>
          </el-submenu>

          <el-submenu index="5">
            <template slot="title">Action</template>
            <el-menu-item
              index="5-1"
              @click="showModal('NewFile')"
            >
              <i class="far fa-file" /> {{ lang.btn.file }}</el-menu-item>
            <el-menu-item
              index="5-2"
              @click="showModal('NewFolder')"
            >
              <i class="far fa-folder" /> {{ lang.btn.folder }}</el-menu-item>
            <el-menu-item
              v-if="uploading"
              index="5-3"
              disabled
            >
              <i class="fas fa-upload" /> {{ lang.btn.upload }}</el-menu-item>
            <el-menu-item
              v-else
              index="5-3"
              @click="showModal('Upload')"
            >
              <i class="fas fa-upload" /> {{ lang.btn.upload }}</el-menu-item>
            <el-menu-item
              index="5-4"
              :disabled="!isAnyItemSelected"
              @click="showModal('Delete')"
            >
              <i class="fas fa-trash-alt" /> {{ lang.btn.delete }}</el-menu-item>
            <el-menu-item
              index="5-5"
              @click="toggleHidden"
            >
              <i class="fas" :class="[hiddenFiles ? 'fa-eye': 'fa-eye-slash']" /> {{ lang.btn.hidden }}</el-menu-item>
          </el-submenu>

          <el-submenu index="6">
            <template slot="title">View mode</template>
            <el-menu-item
              index="6-1"
              :disabled="viewType == 'table' "
              @click="selectView('table')"
            >
              <i class="fas fa-th-list" /> List</el-menu-item>
            <el-menu-item
              index="6-2"
              :disabled="viewType == 'grid'"
              @click="selectView('grid')"
            >
              <i class="fas fa-th" /> Grid</el-menu-item>
          </el-submenu>
          <el-menu-item index="7" @click="screenToggle">Full Screen</el-menu-item>
        </el-menu>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import translate from './../../mixins/translate';
import EventBus from './../../eventBus';

export default {
  mixins: [translate],
  computed: {
    /**
     * Active manager name
     * @returns {default.computed.activeManager|(function())|string|activeManager}
     */
    activeManager() {
      return this.$store.state.fm.activeManager;
    },

    /**
     * Back button state
     * @returns {boolean}
     */
    backDisabled() {
      return !this.$store.state.fm[this.activeManager].historyPointer;
    },

    /**
     * Forward button state
     * @returns {boolean}
     */
    forwardDisabled() {
      return this.$store.state.fm[this.activeManager].historyPointer ===
          this.$store.state.fm[this.activeManager].history.length - 1;
    },

    /**
     * Is any files or directories selected?
     * @returns {boolean}
     */
    isAnyItemSelected() {
      return this.$store.state.fm[this.activeManager].selected.files.length > 0 ||
          this.$store.state.fm[this.activeManager].selected.directories.length > 0;
    },

    /**
     * Manager view type - grid or table
     * @returns {default.computed.viewType|(function())|string}
     */
    viewType() {
      return this.$store.state.fm[this.activeManager].viewType;
    },

    /**
     * Upload files
     * @returns {boolean}
     */
    uploading() {
      return this.$store.state.fm.messages.actionProgress > 0;
    },

    /**
     * Clipboard - action type
     * @returns {null}
     */
    clipboardType() {
      return this.$store.state.fm.clipboard.type;
    },

    /**
     * Full screen status
     * @returns {default.computed.fullScreen|(function())|boolean|fullScreen|*|string}
     */
    fullScreen() {
      return this.$store.state.fm.fullScreen;
    },

    /**
     * Show or Hide hidden files
     * @returns {boolean}
     */
    hiddenFiles() {
      return this.$store.state.fm.settings.hiddenFiles;
    },
  },
  methods: {
    /**
     * Refresh file manager
     */
    refreshAll() {
      this.$store.dispatch('fm/refreshAll');
    },

    /**
     * History back
     */
    historyBack() {
      this.$store.dispatch(`fm/${this.activeManager}/historyBack`);
    },

    /**
     * History forward
     */
    historyForward() {
      this.$store.dispatch(`fm/${this.activeManager}/historyForward`);
    },

    /**
     * Copy
     * @param type
     */
    toClipboard(type) {
      this.$store.dispatch('fm/toClipboard', type);

      // show notification
      if (type === 'cut') {
        EventBus.$emit('addNotification', {
          status: 'success',
          message: this.lang.notifications.cutToClipboard,
        });
      } else if (type === 'copy') {
        EventBus.$emit('addNotification', {
          status: 'success',
          message: this.lang.notifications.copyToClipboard,
        });
      }
    },

    /**
     * Paste
     */
    paste() {
      this.$store.dispatch('fm/paste');
    },

    /**
     * Set Hide or Show hidden files
     */
    toggleHidden() {
      this.$store.commit('fm/settings/toggleHiddenFiles');
    },

    /**
     * Show modal window
     * @param modalName
     */
    showModal(modalName) {
      // show selected modal
      this.$store.commit('fm/modal/setModalState', {
        modalName,
        show: true,
      });
    },

    /**
     * Select view type
     * @param type
     */
    selectView(type) {
      if (this.viewType !== type) {
        this.$store.commit(`fm/${this.activeManager}/setView`, type);
      }
    },

    /**
     * Full screen toggle
     */
    screenToggle() {
      const fm = document.getElementsByClassName('fm')[0];

      if (!this.fullScreen) {
        if (fm.requestFullscreen) {
          fm.requestFullscreen();
        } else if (fm.mozRequestFullScreen) {
          fm.mozRequestFullScreen();
        } else if (fm.webkitRequestFullscreen) {
          fm.webkitRequestFullscreen();
        } else if (fm.msRequestFullscreen) {
          fm.msRequestFullscreen();
        }
      } else if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }

      this.$store.commit('fm/screenToggle');
    },
  },
};
</script>
