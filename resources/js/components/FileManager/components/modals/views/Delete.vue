<template>
    <div class="el-message-box el-message-box--center" style="width:100%">
     <div class="el-message-box__header">
        <div class="el-message-box__title">
           <div class="el-message-box__status el-icon-warning"></div>
           <span>Warning</span>
        </div>
     </div>
     <div class="el-message-box__content">
        <div class="el-message-box__container">
           <!---->
           <div class="el-message-box__message" v-if="selectedItems.length">
              <selected-file-list></selected-file-list>
           </div>
           <div class="el-message-box__message" v-else>
              <p>{{ lang.modal.delete.noSelected }}</p>
           </div>
        </div>
     </div>
     <div class="el-message-box__btns">
        <button type="button" class="el-button el-button--default el-button--small" v-on:click="hideModal">
          <span>{{ lang.btn.cancel }}</span>
        </button>
        <button type="button" class="el-button el-button--default el-button--small el-button--primary " v-on:click="deleteItems">
          <span>{{ lang.modal.delete.title }}</span>
        </button>
     </div>
  </div>
</template>

<script>
import SelectedFileList from '../additions/SelectedFileList.vue';
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';

export default {
  name: 'Delete',
  mixins: [modal, translate],
  components: { SelectedFileList },
  computed: {
    /**
     * Files and folders for deleting
     * @returns {*}
     */
    selectedItems() {
      return this.$store.getters['fm/selectedItems'];
    },
  },
  methods: {
    /**
     * Delete selected directories and files
     */
    deleteItems() {
      // create items list for delete
      const items = this.selectedItems.map(item => ({
        path: item.path,
        type: item.type,
      }));

      this.$store.dispatch('fm/delete', items).then(() => { this.hideModal(); });
    },
  },
};
</script>