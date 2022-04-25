<template>
    <div>
      <el-row :gutter="12" style="padding:20px;margin: 0">
        <el-col :span="4" v-for="(disk, index) in disks" v-bind:key="index">
          <el-card shadow="hover" v-on:click="selectDisk(disk.driver)" v-bind:class="[disk.driver === selectedDisk ? 'drive-active' : '']">
            <i class="far fa-hdd"></i>
            <h2 class="title">{{ disk.driver }}</h2>
            <div class="custom-text">
              <span class="used">{{ disk.capacity - disk.free_space }}GB USED</span>
              <span class="capacity">{{ disk.capacity }}GB</span>
            </div>
            <el-progress :percentage="percentage(disk.capacity,disk.free_space) ? percentage(disk.capacity,disk.free_space) : 0" :color="customColors"></el-progress>
          </el-card>
        </el-col>
      </el-row>
    </div>
</template>

<script>
export default {
  name: 'DiskList',
  data() {
    return {
      customColors: [
        {color: '#5cb87a', percentage: 30},
        {color: '#ff9f43', percentage: 60},
        {color: '#e36f00', percentage: 80},
        {color: '#f56c6c', percentage: 100},
      ]
    };
  },
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
      return this.$store.getters['fm/diskListFull'];
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
     * caculator disk size
     * @returns {Array}
     */
    percentage(capacity,free_space){
      return Math.round(100 - (( capacity - (capacity - free_space) ) / (capacity / 100)));
    },
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
      cursor: pointer;
      box-shadow: 0 0 3px 1px #409EFF;
    }
    .el-card__body{
      .title{
        margin: 8px 0px;
        color: #5e5873;
      }
      i,img,svg{
        font-size:40px;
        width: 40px;
      }
      .custom-text{
        margin-top: 15px;
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        .capacity{
          color: #c8cacf;
        }
        .used{
          color:#909399;
        }
      }
    }
</style>
