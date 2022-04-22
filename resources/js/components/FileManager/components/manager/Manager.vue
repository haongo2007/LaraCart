<template>
  <div class="content-wrapper">
    <div class="file-manager-main-content">
      <div class="file-manager-content-header">
        <breadcrumb v-bind:manager="manager"></breadcrumb>
        <!-- <navbar></navbar> -->
      </div>
      <div class="file-manager-content-body">
        <disk-list v-bind:manager="manager"></disk-list>
        <table-view v-if="viewType === 'table'" v-bind:manager="manager"></table-view>
        <grid-view v-else v-bind:manager="manager"></grid-view>        
      </div>
    </div>
  </div>
</template>

<script>
// Components
import DiskList from './DiskList.vue';
import Navbar from '../blocks/Navbar.vue';
import Breadcrumb from './Breadcrumb.vue';
import TableView from './TableView.vue';
import GridView from './GridView.vue';

export default {
  name: 'Manager',
  components: {
    DiskList,
    Breadcrumb,
    Navbar,
    TableView,
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