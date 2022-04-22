<template>
    <div>
      <el-row :gutter="12" style="padding:20px;">
        <el-col :span="4" v-for="(disk, index) in disks" v-bind:key="index">
          <el-card shadow="hover" v-on:click="selectDisk(disk)" v-bind:class="[disk === selectedDisk ? 'drive-active' : '']">
            <i class="far fa-hdd"></i>
            {{ disk }}
          </el-card>
        </el-col>
      </el-row>
    </div>
</template>

<script>
export default {
  name: 'DiskList',
  props: {
    // manager name - left or right
    manager: { type: String, required: true },
  },
  computed: {
    /**
     * Disk list
     * @returns {Array}
     */
    disks() {
      return this.$store.getters['fm/diskList'];
    },

    /**
     * Selected Disk for this manager
     * @returns {default.computed.selectedDisk|(function())|default.selectedDisk|null}
     */
    selectedDisk() {
      return this.$store.state.fm[this.manager].selectedDisk;
    },
  },
  methods: {
    /**
     * Select disk
     * @param disk
     */
    selectDisk(disk) {
      if (this.selectedDisk !== disk) {
        this.$store.dispatch('fm/selectDisk', {
          disk,
          manager: this.manager,
        });
      }
    },
  },
};
</script>

<style lang="scss">
    .drive-active{
      background-color: #409EFF;
      color: #fff;
      cursor: pointer;
      box-shadow: 0 0 10px 1px #409EFF;
    }
</style>
