<template>
  <div class="sidebar">
    <div class="sidebar-file-manager">
      <div class="redirect" style="text-align: center; padding-top: 20px;">
        <el-button-group>
          <el-button :disabled="backDisabled" @click="historyBack()" icon="el-icon-back"></el-button>
          <el-button @click="refreshAll()" icon="el-icon-refresh"></el-button>
          <el-button :disabled="forwardDisabled" @click="historyForward()" icon="el-icon-right"></el-button>
        </el-button-group>
      </div>
      <el-dropdown style="padding: 20px;width:100%;" @command="handleCommand" trigger="click">
        <el-button type="primary" style="width: 100%">
          Add New
        </el-button>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item command="NewFolder"><i class="el-icon-folder"></i> New folder</el-dropdown-item>
          <el-dropdown-item command="NewFile"><i class="el-icon-document"></i> New file</el-dropdown-item>
          <el-dropdown-item command="Upload"><i class="el-icon-upload"></i> Upload</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
      <div class="sidebar-list ps">
        <branch v-bind:parent-id="0"></branch>
      </div>
    </div>
  </div>
</template>

<script>
import Branch from './Branch.vue';

export default {
  name: 'FolderTree',
  components: {
    branch: Branch,
  },
  computed: {
    /**
     * Active manager name
     * @returns {default.computed.activeManager|(function())|string|activeManager}
     */
    activeManager() {
      return this.$store.state.fm.activeManager;
    },
    /**
     * Selected Disk
     * @returns {*}
     */
    selectedDisk() {
      return this.$store.getters['fm/selectedDisk'];
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
  },
  methods:{
    showModal(modalName) {
      // show selected modal
      this.$store.commit('fm/modal/setModalState', {
        modalName,
        show: true,
      });
    },
    handleCommand(command) {
      this.showModal(command);
    },
    
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
  }
};
</script>
