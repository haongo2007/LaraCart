<template>
    <div>
      <el-breadcrumb separator-class="el-icon-arrow-right" v-bind:class="[manager === activeManager ? 'active-manager' : 'bg-light']" class="my-breadcrumb">
        <el-breadcrumb-item v-on:click="selectMainDirectory" ><i class="far fa-hdd"></i></el-breadcrumb-item>
        <el-breadcrumb-item v-for="(item, index) in breadcrumb"
            v-bind:key="index"
            v-bind:class="[breadcrumb.length === index + 1 ? 'active' : '']"
            v-on:click="selectDirectory(index)">
            {{ item }}
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    manager: { type: String, required: true },
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
  },
  methods: {
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
     * Select main directory
     */
    selectMainDirectory() {
      if (this.selectedDirectory) {
        this.$store.dispatch(`fm/${this.manager}/selectDirectory`, { path: null, history: true });
      }
    },
  },
};
</script>
<style scoped="sass">
  .my-breadcrumb{
    padding: 10px 0;
    border-top: 1px solid;
    border-bottom: 1px solid;
    margin-top: 10px;
    border-color: #e6ebf5;
  }
</style>