<template>
    <div>
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span><i class="far fa-hdd"></i> {{ selectedDisk }}</span>
          </div>
          <branch v-bind:parent-id="0"></branch>
        </el-card>
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
     * Selected Disk
     * @returns {*}
     */
    selectedDisk() {
      return this.$store.getters['fm/selectedDisk'];
    },
  },
};
</script>
