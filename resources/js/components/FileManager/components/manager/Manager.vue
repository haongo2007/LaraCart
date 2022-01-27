<template>
    <div>
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <disk-list v-bind:manager="manager"></disk-list>
          <breadcrumb v-bind:manager="manager"></breadcrumb>
          <navbar></navbar>
        </div>
        <table-view v-if="viewType === 'table'" v-bind:manager="manager"></table-view>
        <grid-view v-else v-bind:manager="manager"></grid-view>
        <info-block></info-block>
      </el-card>
    </div>
</template>

<script>
// Components
import DiskList from './DiskList.vue';
import Navbar from '../blocks/Navbar.vue';
import Breadcrumb from './Breadcrumb.vue';
import TableView from './TableView.vue';
import GridView from './GridView.vue';
import InfoBlock from '../blocks/InfoBlock.vue';

export default {
  name: 'Manager',
  components: {
    DiskList,
    Breadcrumb,
    Navbar,
    TableView,
    InfoBlock,
    GridView,
  },
  props: {
    manager: { type: String, required: true },
  },
  computed: {
    /**
     * view type - grid or table
     * @returns {default.computed.viewType|(function())|string}
     */
    viewType() {
      return this.$store.state.fm[this.manager].viewType;
    },
  },
};
</script>