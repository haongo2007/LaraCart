<template>
  <div class="list-group">
      <div class="my-drive">
       <div class="tree-node" v-for="(directory, index) in subDirectories" v-bind:key="index">
          <div 
            v-bind:class="{'selected': isDirectorySelected(directory.path)}"
            v-on:click="selectDirectory(directory.path)"
            class="tree-node__content">
             <div class="tree-item" v-if="directory.props.hasSubdirectories" v-on:click.stop="showSubdirectories(directory.path,directory.props.showSubdirectories)">
                <i :class="'el-icon-arrow-'+caret"></i>
                <span class="tree-node__label"><i class="el-icon-folder"></i> {{ directory.basename }}</span>
             </div>
             <div class="tree-item" v-else>
                <span class="tree-node__label"><i class="el-icon-folder"></i> {{ directory.basename }}</span>
             </div>
          </div>

          <transition name="fade-tree">
            <branch v-show="arrowState(index)"
                    v-if="directory.props.hasSubdirectories"
                    v-bind:parent-id="directory.id" style="margin-left:20px">
            </branch>
          </transition>
       </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Branch',
  props: {
    parentId: { type: Number, required: true },
  },
  data() {
    return {
      caret: 'right',
    };
  },
  computed: {
    /**
     * Subdirectories for selected parent
     * @returns {*}
     */
    subDirectories() {
      return this.$store.getters['fm/tree/directories'].filter(item => item.parentId === this.parentId);
    },
  },
  methods: {
    /**
     * Check, is this directory selected?
     * @param path
     * @returns {boolean}
     */
    isDirectorySelected(path) {
      return this.$store.state.fm.left.selectedDirectory === path;
    },

    /**
     * Show subdirectories - arrow
     * @returns {boolean}
     * @param index
     */
    arrowState(index) {
      return this.subDirectories[index].props.showSubdirectories;
    },

    /**
     * Show/Hide subdirectories
     * @param path
     * @param showState
     */
    showSubdirectories(path, showState) {
      if (showState) {
        // hide
        this.caret = 'right';
        this.$store.dispatch('fm/tree/hideSubdirectories', path);
      } else {
        // show
        this.caret = 'down';
        this.$store.dispatch('fm/tree/showSubdirectories', path);
      }
    },

    /**
     * Load selected directory and show files
     * @param path
     */
    selectDirectory(path) {
      // only if this path not selected
      if (!this.isDirectorySelected(path)) {
        this.$store.dispatch('fm/left/selectDirectory', { path, history: true });
      }
    },
  },
};
</script>

<style lang="scss" >
  .tree-node{
    cursor: pointer;
    padding: 5px 0px;
    .tree-node__content{
      .tree-item{
        padding: 5px 20px;
      }
    }
  }
</style>
