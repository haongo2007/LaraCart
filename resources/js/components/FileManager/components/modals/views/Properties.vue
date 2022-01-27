<template>
    <div class="properties-view">
        <ul class="el-upload-list el-upload-list--text ul-2">
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(selectedDisk)"
                    v-bind:title="lang.clipboard.copy"
                    class="far fa-hdd"></i> {{ lang.modal.properties.disk }}:
            </span>
            <span>
                {{ selectedDisk }}
            </span>
          </li>
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(selectedItem.basename)"
                    v-bind:title="lang.clipboard.copy"
                    class="far fa-copy"></i> {{ lang.modal.properties.name }}:
            </span>
            <span class="sort-text">
                {{ selectedItem.basename }}
            </span>
          </li>
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(selectedItem.path)"
                    v-bind:title="lang.clipboard.copy"
                    class="far fa-folder-open"></i> {{ lang.modal.properties.path }}:
            </span>
            <span class="sort-text">
                {{ selectedItem.path }}
            </span>
          </li>
        </ul>

        <ul class="el-upload-list el-upload-list--text ul-2" v-if="selectedItem.type === 'file'">
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(bytesToHuman(selectedItem.size))"
                    v-bind:title="lang.clipboard.copy"
                    class="far fa-copy"></i> {{ lang.modal.properties.size }}:
            </span>
            <span>
                {{ bytesToHuman(selectedItem.size) }}
            </span>
          </li>
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(bytesToHuman(selectedItem.size))"
                    v-bind:title="lang.clipboard.copy"
                    class="fas fa-link"></i> {{ lang.modal.properties.url }}:
            </span>
            <span v-if="url" class="sort-text">
                {{ url }}
            </span>
            <span v-else>
                <i class="fas fa-link" v-on:click="getUrl"> Get URL</i> 
            </span>
            <div v-if="url" class="col-1 text-right">
                <i v-on:click="copyToClipboard(url)"
                   v-bind:title="lang.clipboard.copy"
                   class="far fa-copy"></i>
            </div>
          </li>
        </ul>

        <ul class="el-upload-list el-upload-list--text ul-2" v-if="selectedItem.hasOwnProperty('timestamp')">
          <li class="el-upload-list__item is-success">
            <span>
                <i  v-on:click="copyToClipboard(timestampToDate(selectedItem.timestamp))"
                    v-bind:title="lang.clipboard.copy"
                    class="far fa-edit"></i> {{ lang.modal.properties.modified }}:
            </span>
            <span>
                {{ timestampToDate(selectedItem.timestamp) }}
            </span>
          </li>
        </ul>

        <ul class="el-upload-list el-upload-list--text ul-2" v-if="selectedItem.hasOwnProperty('acl')">
          <li class="el-upload-list__item is-success">
            <span>
                {{ lang.modal.properties.access }}:
            </span>
            <span>
                {{ lang.modal.properties['access_' + selectedItem.acl] }}
            </span>
          </li>
        </ul>
    </div>
</template>

<script>
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';
import helper from './../../../mixins/helper';
import EventBus from './../../../eventBus';

export default {
  name: 'Properties',
  mixins: [modal, translate, helper],
  data() {
    return {
      url: null,
    };
  },
  computed: {
    /**
     * Selected disk
     * @returns {*}
     */
    selectedDisk() {
      return this.$store.getters['fm/selectedDisk'];
    },

    /**
     * Selected file
     * @returns {*}
     */
    selectedItem() {
      return this.$store.getters['fm/selectedItems'][0];
    },
  },
  methods: {
    /**
     * Get URL
     */
    getUrl() {
      this.$store.dispatch('fm/url', {
        disk: this.selectedDisk,
        path: this.selectedItem.path,
      }).then((response) => {
        if (response.data.result.status === 'success') {
          this.url = response.data.url;
        }
      });
    },

    /**
     * Copy text to clipboard
     * @param text
     */
    copyToClipboard(text) {
      // create input
      const copyInputHelper = document.createElement('input');
      copyInputHelper.className = 'copyInputHelper';
      document.body.appendChild(copyInputHelper);
      // add text
      copyInputHelper.value = text;
      copyInputHelper.select();
      // copy text to clipboard
      document.execCommand('copy');
      // clear
      document.body.removeChild(copyInputHelper);

      // Notification
      this.$message({
        message: this.lang.notifications.copyToClipboard,
        type: 'success'
      });
    },
  },
};
</script>
<style lang="scss">
.properties-view{
  max-width:400px;
  margin:0 auto;
  .ul-2 li{
    display:flex; 
    justify-content: space-between;
    align-items: center;
    .sort-text{
      width: 65%;
      white-space: nowrap;
      overflow: hidden !important;
      text-overflow: ellipsis;
    }  
  }
}
</style>