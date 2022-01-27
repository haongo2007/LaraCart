<template>
    <div style="margin-top:10px;">
        <el-tag>
            <span v-show="selectedCount">
                {{ `${lang.info.selected} ${selectedCount}` }}
                {{ `${lang.info.selectedSize} ${selectedFilesSize}` }}
            </span>
            <span v-show="!selectedCount">
                {{ `${lang.info.directories} ${directoriesCount}` }}
                {{ `${lang.info.files} ${filesCount}` }}
                {{ `${lang.info.size} ${filesSize}`}}
            </span>
        </el-tag>
        <el-tag v-show="progressBar">
            <span>
                {{progressBar}}
            </span>
        </el-tag>
        <el-tag v-show="loadingSpinner">
            <span>
                <i class="fas fa-spinner fa-pulse"></i>
            </span>
        </el-tag>

        <el-tag v-show="clipboardType">
            <span
                  v-on:click="showModal('Clipboard')"
                  v-bind:title="[ lang.clipboard.title + ' - ' + lang.clipboard[clipboardType] ]">
                <i class="far fa-clipboard"></i>
            </span>
        </el-tag>
        <el-tag>
            <span v-on:click="showModal('Status')"
                  v-bind:class="[hasErrors ? 'text-danger' : 'text-success']"
                  v-bind:title="lang.modal.status.title">
                <i class="fas fa-info-circle"></i>
            </span>
        </el-tag>
    </div>
</template>

<script>
import translate from './../../mixins/translate';
import helper from './../../mixins/helper';

export default {
  name: 'InfoBlock',
  mixins: [translate, helper],
  computed: {
    /**
     * Active manager
     * @returns {default.computed.activeManager|(function())|string|activeManager}
     */
    activeManager() {
      return this.$store.state.fm.activeManager;
    },

    /**
     * Progress bar value - %
     * @returns {number}
     */
    progressBar() {
      return this.$store.state.fm.messages.actionProgress;
    },

    /**
     * App has errors
     * @returns {boolean}
     */
    hasErrors() {
      return !!this.$store.state.fm.messages.errors.length;
    },

    /**
     * Files count in selected directory
     * @returns {*}
     */
    filesCount() {
      return this.$store.getters[`fm/${this.activeManager}/filesCount`];
    },

    /**
     * Directories count in selected directory
     * @returns {*}
     */
    directoriesCount() {
      return this.$store.getters[`fm/${this.activeManager}/directoriesCount`];
    },

    /**
     * Files size in selected directory
     * @returns {*|string}
     */
    filesSize() {
      return this.bytesToHuman(this.$store.getters[`fm/${this.activeManager}/filesSize`]);
    },

    /**
     * Count files and folders
     * @returns {*}
     */
    selectedCount() {
      return this.$store.getters[`fm/${this.activeManager}/selectedCount`];
    },

    /**
     * Calculate selected files size
     * @returns {*|string}
     */
    selectedFilesSize() {
      return this.bytesToHuman(this.$store.getters[`fm/${this.activeManager}/selectedFilesSize`]);
    },

    /**
     * Clipboard - action type
     * @returns {null}
     */
    clipboardType() {
      return this.$store.state.fm.clipboard.type;
    },

    /**
     * Spinner
     * @returns {number}
     */
    loadingSpinner() {
      return this.$store.state.fm.messages.loading;
    },
  },
  methods: {
    /**
     * Show modal window
     * @param modalName
     */
    showModal(modalName) {
      this.$store.commit('fm/modal/setModalState', {
        modalName,
        show: true,
      });
    },
  },
};
</script>
