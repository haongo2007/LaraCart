<template>
    <div>
      <ul class="el-upload-list el-upload-list--text ul-2">
          <li class="el-upload-list__item is-success" v-for="(item, index) in selectedItems" v-bind:key="index">
            <span v-if="item.type === 'dir'">
                <i class="far fa-folder"></i> {{ item.basename }}
            </span>
            <span v-else>
                <i class="far" v-bind:class="extensionToIcon(item.extension)"></i> {{ item.basename }}
            </span>
            <span v-if="item.type === 'file'">
                {{ bytesToHuman(item.size) }}
            </span>
          </li>
        </ul>
    </div>
</template>

<script>
import helper from './../../../mixins/helper';

export default {
  name: 'SelectedFileList',
  mixins: [helper],
  computed: {
    /**
     * Selected files and folders
     * @returns {*}
     */
    selectedItems() {
      return this.$store.getters['fm/selectedItems'];
    },
  },
};
</script>

<style lang="scss">
    .fm-additions-file-list {
        .far {
            padding-right: 0.5rem;
        }
    }
</style>
