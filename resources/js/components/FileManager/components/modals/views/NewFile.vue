<template>
  <div>
    <form class="el-form demo-ruleForm">
       <div class="el-form-item is-required" v-bind:class="fileExist ? 'is-error' : '' ">
          <label for="fm-file-name" class="el-form-item__label">{{ lang.modal.newFile.fieldName }}</label>
          <div class="el-form-item__content">
             <div class="el-input">
                <input type="text" id="fm-file-name"
                      v-focus
                      v-bind:class="{'is-invalid': fileExist}"
                      v-model="fileName"
                      v-on:keyup="validateFileName"
                      class="el-input__inner">
             </div>
             <div class="el-form-item__error" v-show="fileExist">
                {{ lang.modal.newFile.fieldFeedback }}
             </div>
          </div>
       </div>
       <div class="el-form-item">
          <div class="el-form-item__content">
              <el-button type="primary" v-on:click="addFile" v-bind:disabled="!submitActive">{{ lang.btn.submit }}</el-button>
              <el-button v-on:click="hideModal">{{ lang.btn.cancel }}</el-button>
          </div>
       </div>
    </form>
  </div>
</template>

<script>
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';

export default {
  name: 'NewFile',
  mixins: [modal, translate],
  data() {
    return {
      // name for new file
      fileName: '',

      // file exist
      fileExist: false,
    };
  },
  computed: {
    /**
     * Submit button - active or no
     * @returns {string|boolean}
     */
    submitActive() {
      return this.fileName && !this.fileExist;
    },
  },
  methods: {
    /**
     * Check the file name if it exists or not.
     */
    validateFileName() {
      if (this.fileName) {
        this.fileExist = this.$store.getters[`fm/${this.activeManager}/fileExist`](this.fileName);
      } else {
        this.fileExist = false;
      }
    },

    /**
     * Create new file
     */
    addFile() {
      this.$store.dispatch('fm/createFile', this.fileName).then((response) => {
        // if new directory created successfully
        if (response.data.result.status === 'success') {
          // close modal window
          this.hideModal();
        }
      });
    },
  },
};
</script>
