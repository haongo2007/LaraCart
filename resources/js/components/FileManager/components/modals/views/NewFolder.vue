<template>
  <div>
    <form class="el-form demo-ruleForm">
       <div class="el-form-item is-required" v-bind:class="directoryExist ? 'is-error' : '' ">
          <label for="fm-folder-name" class="el-form-item__label">{{ lang.modal.newFolder.fieldName }}</label>
          <div class="el-form-item__content">
             <div class="el-input">
                <input type="text" id="fm-folder-name"
                      v-focus
                      v-bind:class="{'is-invalid': directoryExist}"
                      v-model="directoryName"
                      v-on:keyup="validateDirName"
                      class="el-input__inner">
             </div>
             <div class="el-form-item__error" v-show="directoryExist">
                {{ lang.modal.newFolder.fieldFeedback }}
             </div>
          </div>
       </div>
       <div class="el-form-item">
          <div class="el-form-item__content">
              <el-button type="primary" v-on:click="addFolder" v-bind:disabled="!submitActive">{{ lang.btn.submit }}</el-button>
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
  name: 'NewFolder',
  mixins: [modal, translate],
  data() {
    return {
      // name for new directory
      directoryName: '',

      // directory exist
      directoryExist: false,
    };
  },
  computed: {
    /**
     * Submit button - active or no
     * @returns {string|boolean}
     */
    submitActive() {
      return this.directoryName && !this.directoryExist;
    },
  },
  methods: {
    /**
     * Check the folder name if it exists or not.
     */
    validateDirName() {
      if (this.directoryName) {
        this.directoryExist = this.$store.getters[`fm/${this.activeManager}/directoryExist`](this.directoryName);
      } else {
        this.directoryExist = false;
      }
    },

    /**
     * Create new directory
     */
    addFolder() {
      this.$store.dispatch('fm/createDirectory', this.directoryName).then((response) => {
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
