<template>
  <div>
    <form class="el-form demo-ruleForm">
        <div class="el-form-item is-required" v-bind:class="fileExist ? 'is-error' : '' ">
          <label for="fm-zip-name" class="el-form-item__label">{{ lang.modal.zip.fieldName }} (.Zip)</label>
          <div class="el-form-item__content">
             <div class="el-input">
                <input type="text" id="fm-zip-name"
                      v-focus
                      v-bind:class="{'is-invalid': archiveExist}"
                      v-model="archiveName"
                      v-on:keyup="validateArchiveName"
                      class="el-input__inner">
             </div>
             <div class="el-form-item__error" v-show="archiveExist">
                {{ lang.modal.zip.fieldFeedback }}
             </div>
          </div>
        </div>
        <selected-file-list></selected-file-list>
        <div class="el-form-item">
          <div class="el-form-item__content">
              <el-button type="primary" v-on:click="createArchive" v-bind:disabled="!submitActive">{{ lang.btn.submit }}</el-button>
              <el-button v-on:click="hideModal">{{ lang.btn.cancel }}</el-button>
          </div>
        </div>
    </form>
  </div>
</template>

<script>
import SelectedFileList from '../additions/SelectedFileList.vue';
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';

export default {
  name: 'Zip',
  mixins: [modal, translate],
  components: { SelectedFileList },
  data() {
    return {
      // name for new archive
      archiveName: '',

      // archive exist
      archiveExist: false,
    };
  },
  computed: {
    /**
     * Submit button - active or no
     * @returns {string|boolean}
     */
    submitActive() {
      return this.archiveName && !this.archiveExist;
    },
  },
  methods: {
    /**
     * Check the archive name if it exists or not.
     */
    validateArchiveName() {
      if (this.archiveName) {
        this.archiveExist = this.$store.getters[`fm/${this.activeManager}/fileExist`](`${this.archiveName}.zip`);
      } else {
        this.archiveExist = false;
      }
    },

    /**
     * Create new archive
     */
    createArchive() {
      this.$store.dispatch('fm/zip', `${this.archiveName}.zip`).then(() => {
        // close modal window
        this.hideModal();
      });
    },
  },
};
</script>
