<template>
  <div>
    <form class="el-form demo-ruleForm">
        <div class="el-form-item is-required" v-bind:class="fileExist ? 'is-error' : '' ">
          <label for="fm-input-rename" class="el-form-item__label">{{ lang.modal.rename.fieldName }}</label>
          <div class="el-form-item__content">
             <div class="el-input">
                <input type="text" id="fm-input-rename"
                      v-focus
                      v-bind:class="{'is-invalid': checkName}"
                      v-model="name"
                      v-on:keyup="validateName"
                      class="el-input__inner">
             </div>
             <div class="el-form-item__error" v-show="checkName">
                {{ lang.modal.rename.fieldFeedback }}
                {{ directoryExist ? ` - ${lang.modal.rename.directoryExist}` : ''}}
                {{ fileExist ? ` - ${lang.modal.rename.fileExist}` : ''}}
             </div>
          </div>
        </div>
        <div class="el-form-item">
          <div class="el-form-item__content">
              <el-button type="primary" v-on:click="rename" v-bind:disabled="submitDisable">{{ lang.btn.submit }}</el-button>
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
  name: 'Rename',
  mixins: [modal, translate],
  data() {
    return {
      name: '',
      directoryExist: false,
      fileExist: false,
    };
  },
  computed: {
    /**
     * Selected item
     * @returns {*}
     */
    selectedItem() {
      return this.$store.getters[`fm/${this.activeManager}/selectedList`][0];
    },

    /**
     * Check new name
     * @returns {boolean}
     */
    checkName() {
      return this.directoryExist || this.fileExist || !this.name;
    },

    /**
     * Submit button disable
     * @returns {*|boolean}
     */
    submitDisable() {
      return this.checkName || this.name === this.selectedItem.basename;
    },
  },
  mounted() {
    // initiate item name
    this.name = this.selectedItem.basename;
  },
  methods: {
    /**
     * Validate item name
     */
    validateName() {
      if (this.name !== this.selectedItem.basename) {
        // if item - folder
        if (this.selectedItem.type === 'dir') {
          // check folder name matches
          this.directoryExist = this.$store.getters[`fm/${this.activeManager}/directoryExist`](this.name);
        } else {
          // check file name matches
          this.fileExist = this.$store.getters[`fm/${this.activeManager}/fileExist`](this.name);
        }
      }
    },

    /**
     * Rename item
     */
    rename() {
      // create new name with path
      const newName = this.selectedItem.dirname ?
        `${this.selectedItem.dirname}/${this.name}` :
        this.name;

      this.$store.dispatch('fm/rename', {
        type: this.selectedItem.type,
        newName,
        oldName: this.selectedItem.path,
      }).then(() => {
        // close modal window
        this.hideModal();
      });
    },
  },
};
</script>
