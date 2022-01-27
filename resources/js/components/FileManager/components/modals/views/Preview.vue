<template>
  <div>
    <el-row style="text-align:center">
      <el-col :span="24" style="padding:10px 0">
        <div>
          <template v-if="showCropperModule">
                  <cropper-module 
                          v-bind:imgSrc="imgSrc"
                          v-bind:maxHeight="maxHeight"
                          v-on:closeCropper="closeCropper" />
          </template>
          <transition v-else name="fade" mode="out-in">
              <i v-if="!imgSrc" class="fas fa-spinner fa-spin fa-5x p-5 text-muted"></i>
              <img v-else
                   v-bind:src="imgSrc"
                   v-bind:alt="selectedItem.basename"
                   v-bind:style="{'max-height': maxHeight+'px'}">
          </transition>
        </div>
        <span v-show="!showCropperModule">{{ selectedItem.basename }}</span>
      </el-col>
      <el-col :span="24" style="padding:10px 0">
        <el-button-group>
          <el-button type="primary" icon="el-icon-crop" 
          v-bind:title="lang.modal.cropper.title" 
          v-on:click="showCropperModule = true" 
          v-bind:disabled="showCropperModule"></el-button>
          <el-button type="primary" v-on:click="hideModal" icon="el-icon-close"></el-button>
        </el-button-group>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import CropperModule from './../additions/Cropper.vue';
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';
import helper from './../../../mixins/helper';
import GET from './../../../http/get';

export default {
  name: 'Preview',
  mixins: [modal, translate, helper],
  components: { CropperModule },
  data() {
    return {
      showCropperModule: false,
      imgSrc: '',
    };
  },
  created() {
    this.loadImage();
  },
  computed: {
    /**
     * Authorization required
     * @return {*}
     */
    auth() {
      return this.$store.getters['fm/settings/authHeader'];
    },

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

    /**
     * Show modal footer
     * @return boolean
     */
    showFooter() {
      return this.canCrop(this.selectedItem.extension) && !this.showCropperModule;
    },

    /**
     * Calculate the max height for image
     * @returns {number}
     */
    maxHeight() {
      if (this.$store.state.fm.modal.modalBlockHeight) {
        return this.$store.state.fm.modal.modalBlockHeight - 170;
      }

      return 300;
    },
  },
  methods: {
    /**
     * Can we crop this image?
     * @param extension
     * @returns {boolean}
     */
    canCrop(extension) {
      return this.$store.state.fm.settings.cropExtensions.includes(extension.toLowerCase());
    },

    /**
     * Close cropper
     */
    closeCropper() {
      this.showCropperModule = false;
      this.loadImage();
    },

    /**
     * Load image
     */
    loadImage() {
      // if authorization required
      if (this.auth) {
        GET.preview(
          this.selectedDisk,
          this.selectedItem.path,
        ).then((response) => {
          const mimeType = response.headers['content-type'].toLowerCase();
          const imgBase64 = Buffer.from(response.data, 'binary').toString('base64');

          this.imgSrc = `data:${mimeType};base64,${imgBase64}`;
        });
      } else {
        this.imgSrc = `${this.$store.getters['fm/settings/baseUrl']}preview?disk=${this.selectedDisk}&path=${encodeURIComponent(this.selectedItem.path)}&v=${this.selectedItem.timestamp}`;
      }
    },
  },
};
</script>