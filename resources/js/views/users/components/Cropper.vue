<template>
  <div class="components-container">
    <image-cropper
      v-show="cropper"
      :key="imagecropperKey"
      :width="300"
      :height="300"
      :url="urlUpdateAvatar"
      lang-type="en"
      @close="close"
      @crop-upload-success="cropSuccess"
    />
  </div>
</template>
<script>
import ImageCropper from '@/components/ImageCropper';
export default {
  name: 'Cropper',
  components: { ImageCropper },
  props: {
		    cropper: {
		      	type: Boolean,
		      	default: false,
		    },
	  	},
  data() {
    return {
      state_cropper: this.cropper,
      imagecropperKey: 0,
      image: 'https://eclectickoifish.files.wordpress.com/2015/01/7826_one_piece.jpg',
      urlUpdateAvatar: 'update/avatar',
    };
  },
  methods: {
    cropSuccess(resData) {
	      		this.close();
      this.imagecropperKey = this.imagecropperKey + 1;
      if (resData.status == 'success') {
        this.$store.dispatch('user/setAvatar', resData.avatar);
      }
    },
    close() {
      this.$emit('dadUpdatedCropper', false);
      this.state_cropper = !this.state_cropper;
    },
  },
};
</script>
