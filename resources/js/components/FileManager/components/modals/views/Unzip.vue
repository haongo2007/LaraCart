<template>
    <div>
      <form class="el-form demo-ruleForm">
        <div class="el-form-item is-required">
          <label for="fm-zip-name" class="el-form-item__label">{{ lang.modal.unzip.fieldRadioName }}</label>
          <div class="el-form-item__content">
              <div class="el-input">
                <el-radio v-model.number="createFolder" name="uploadOptions" :label="0">{{ lang.modal.unzip.fieldRadio1 }}</el-radio>
                <el-radio v-model.number="createFolder" name="uploadOptions" :label="1">{{ lang.modal.unzip.fieldRadio2 }}</el-radio>
              </div>

              <div class="el-form-item is-required" v-if="createFolder" v-bind:class="directoryExist ? 'is-error' : '' ">
                <label for="fm-folder-name" class="el-form-item__label">{{ lang.modal.unzip.fieldName }}</label>
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
                      {{ lang.modal.unzip.fieldFeedback }}
                   </div>
                </div>
              </div>
              <p v-else>{{ lang.modal.unzip.warning }}</p>
            </div>
          </div>
          <el-button type="primary" v-on:click="unpackArchive" v-bind:disabled="!submitActive">{{ lang.btn.submit }}</el-button>
          <el-button v-on:click="hideModal">{{ lang.btn.cancel }}</el-button>
      </form>
    </div>
</template>

<script>
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';

export default {
  name: 'Unzip',
  mixins: [modal, translate],
  data() {
    return {
      createFolder: 0,

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
      if (this.createFolder) {
        return this.directoryName && !this.directoryExist;
      }

      return true;
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
     * Unpack selected archive
     */
    unpackArchive() {
      this.$store.dispatch('fm/unzip', this.createFolder ? this.directoryName : null).then(() => {
        // close modal window
        this.hideModal();
      });
    },
  },
};
</script>
