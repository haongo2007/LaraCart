<template>
    <div>
      <div class="header-tool">
        <el-breadcrumb separator-class="el-icon-arrow-right" v-bind:class="[manager === activeManager ? 'active-manager' : 'bg-light']" class="my-breadcrumb">
          <el-breadcrumb-item v-on:click="selectMainDirectory" ><i class="far fa-hdd"></i></el-breadcrumb-item>
          <el-breadcrumb-item v-for="(item, index) in breadcrumb"
              v-bind:key="index"
              v-bind:class="[breadcrumb.length === index + 1 ? 'active' : '']"
              v-on:click="selectDirectory(index)">
              {{ item }}
          </el-breadcrumb-item>
        </el-breadcrumb>
        <div class="action" v-if="isAnyItemSelected || clipboardType">
          <el-button-group>
            <el-tooltip placement="top" effect="dark">
              <div slot="content">{{ lang.btn.copy }}</div>
              <el-button @click="toClipboard('copy')" size="mini" :disabled="!isAnyItemSelected" icon="el-icon-document-copy"></el-button>
            </el-tooltip>
            <el-tooltip placement="top" effect="dark">
              <div slot="content">{{ lang.btn.cut }}</div>
              <el-button @click="toClipboard('cut')" size="mini" :disabled="!isAnyItemSelected" icon="el-icon-scissors"></el-button>
            </el-tooltip>
            <el-tooltip placement="top" effect="dark">
              <div slot="content">{{ lang.btn.paste }}</div>
              <el-button @click="paste" size="mini" :disabled="!clipboardType" icon="el-icon-s-check"></el-button>
            </el-tooltip>
          </el-button-group>
        </div>
        <div class="view-mode">
          <el-button-group>
            <el-button type="danger" @click="showModal('Delete')" v-if="isAnyItemSelected" icon="el-icon-delete" size="mini"></el-button>
            <el-tooltip placement="top" effect="dark">
              <div slot="content">{{ lang.btn.hidden}}</div>
              <el-button @click="toggleHidden" size="mini"><i class="fas" :class="[hiddenFiles ? 'fa-eye': 'fa-eye-slash']" /></el-button>
            </el-tooltip>
            <el-tooltip placement="top" content="Full Screen" effect="dark">
              <el-button @click="screenToggle" :disabled="viewType == 'grid'" icon="el-icon-full-screen" size="mini"></el-button>
            </el-tooltip>
            <el-button @click="selectView('table')" :disabled="viewType == 'table'" icon="el-icon-s-unfold" size="mini"></el-button>
            <el-button @click="selectView('grid')" :disabled="viewType == 'grid'" icon="el-icon-menu" size="mini"></el-button>
          </el-button-group>
        </div>
      </div>
    </div>
</template>

<script>
import translate from './../../mixins/translate';
export default {
  name: 'Breadcrumb',
  mixins: [translate],
  props: {
    manager: { type: String, required: true },
  },
  computed: {

    /**
     * Clipboard - action type
     * @returns {null}
     */
    clipboardType() {
      return this.$store.state.fm.clipboard.type;
    },
    /**
     * Show or Hide hidden files
     * @returns {boolean}
     */
    hiddenFiles() {
      return this.$store.state.fm.settings.hiddenFiles;
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
     * Active manager name
     * @returns {default.computed.activeManager|(function())|string|activeManager}
     */
    activeManager() {
      return this.$store.state.fm.activeManager;
    },

    /**
     * Selected Disk for this manager
     * @returns {default.computed.selectedDisk|(function())|default.selectedDisk|null}
     */
    selectedDisk() {
      return this.$store.state.fm[this.manager].selectedDisk;
    },

    /**
     * Selected directory for this manager
     * @returns {default.computed.selectedDirectory|(function())|default.selectedDirectory|null}
     */
    selectedDirectory() {
      return this.$store.state.fm[this.manager].selectedDirectory;
    },

    /**
     * Breadcrumb
     * @returns {*}
     */
    breadcrumb() {
      return this.$store.getters[`fm/${this.manager}/breadcrumb`];
    },
    /**
     * Full screen status
     * @returns {default.computed.fullScreen|(function())|boolean|fullScreen|*|string}
     */
    fullScreen() {
      return this.$store.state.fm.fullScreen;
    },
  },
  methods: {
    /**
     * Set Hide or Show hidden files
     */
    toggleHidden() {
      this.$store.commit('fm/settings/toggleHiddenFiles');
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
    /**
     * Load selected directory
     * @param index
     */
    selectDirectory(index) {
      const path = this.breadcrumb.slice(0, index + 1).join('/');

      // only if this path not selected
      if (path !== this.selectedDirectory) {
        // load directory
        this.$store.dispatch(`fm/${this.manager}/selectDirectory`, { path, history: true });
      }
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
     * Select main directory
     */
    selectMainDirectory() {
      if (this.selectedDirectory) {
        this.$store.dispatch(`fm/${this.manager}/selectDirectory`, { path: null, history: true });
      }
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
  },
};
</script>
<style lang="scss" >
  .header-tool{
    display: flex;
    justify-content:space-between;
    align-items:center;
    border-bottom: 1px solid #ebe9f1;
    padding: 10px;
    .my-breadcrumb{
      font-size: 1rem;
    }
  }
</style>