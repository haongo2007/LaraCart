<template>
    <div>
      <el-row style="text-align:center">
        <el-col :span="12" v-bind:style="{'height': maxHeight+'px'}">
          <img v-bind:src="imgSrc"
               ref="fmCropper"
               v-bind:alt="selectedItem.basename"
               v-bind:style="{'width': '100%'}">
        </el-col>
        <el-col :span="12" v-bind:style="{'height': maxHeight+'px'}">
          <div class="cropper-preview"></div>
        </el-col>
        <el-col style="text-align:center" :span="24" class="el-custom-form">
          <el-form :label-position="`${'top'}`" label-width="100px" >
            <el-col :span="3" style="padding:10px">
              <el-form-item label="X (px)">
                <el-input v-model.number="x" id="dataX"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="Y (px)">
                <el-input v-model.number="y" id="dataY"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="Width (px)">
                <el-input v-model.number="width" id="dataWidth"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="Height (px)">
                <el-input v-model.number="height" id="dataHeight"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="Rotate (deg)">
                <el-input v-model.number="rotate" id="dataRotate"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="ScaleX (deg)">
                <el-input v-model.number="scaleX" id="dataScaleX"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-form-item label="ScaleY (deg)">
                <el-input v-model.number="scaleY" id="dataScaleY"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="3" style="padding:10px">
              <el-button type="primary" 
                          icon="el-icon-check" 
                          v-on:click="setData()"
                          v-bind:title="lang.modal.cropper.apply"></el-button>
            </el-col>
          </el-form>
        </el-col>
        <el-col :span="24" style="padding:10px 0">
            <el-button-group>
              <el-button type="primary" icon="el-icon-back" v-on:click="cropMove(10, 0)"></el-button>
              <el-button type="primary" icon="el-icon-right" v-on:click="cropMove(-10, 0)"></el-button>
              <el-button type="primary" icon="el-icon-bottom" v-on:click="cropMove(0, -10)"></el-button>
              <el-button type="primary" icon="el-icon-top" v-on:click="cropMove(0, 10)"></el-button>
            </el-button-group>
            <el-button-group>
              <el-button type="primary" icon="el-icon-refresh-left" v-on:click="cropRotate(-45)"></el-button>
              <el-button type="primary" icon="el-icon-refresh-right" v-on:click="cropRotate(45)"></el-button>
            </el-button-group>
            <el-button-group>
              <el-button type="primary" v-on:click="cropScaleX()"><i class="fas fa-arrows-alt-h"></i></el-button>
              <el-button type="primary" v-on:click="cropScaleY()"><i class="fas fa-arrows-alt-v"></i></el-button>
            </el-button-group>
            <el-button-group>
              <el-button type="primary" icon="el-icon-zoom-in" v-on:click="cropZoom(0.1)"></el-button>
              <el-button type="primary" icon="el-icon-zoom-out" v-on:click="cropZoom(-0.1)"></el-button>
            </el-button-group>
            <el-button-group>
              <el-button type="primary" icon="el-icon-refresh" v-on:click="cropReset()" v-bind:title="lang.modal.cropper.reset"></el-button>
              <el-button type="primary" icon="el-icon-upload" v-on:click="cropSave()" v-bind:title="lang.modal.cropper.save"></el-button>
            </el-button-group>
            <el-button-group>
              <el-button type="primary" v-on:click="$emit('closeCropper')">{{ lang.btn.back }}</el-button>
            </el-button-group>
          </el-col>
        </el-row>
    </div>
</template>

<script>
import Cropper from 'cropperjs';
import translate from './../../../mixins/translate';

export default {
  name: 'Cropper',
  mixins: [translate],
  props: {
    imgSrc: { required: true },
    maxHeight: { type: Number, required: true },
  },
  data() {
    return {
      cropper: {},
      height: 0,
      width: 0,
      x: 0,
      y: 0,
      rotate: 0,
      scaleX: 1,
      scaleY: 1,
    };
  },
  mounted() {
    // set cropper instance
    this.cropper = new Cropper(this.$refs.fmCropper, {
      preview: '.cropper-preview',
      crop: (e) => {
        console.log(e);
        this.x = Math.round(e.detail.x);
        this.y = Math.round(e.detail.y);
        this.height = Math.round(e.detail.height);
        this.width = Math.round(e.detail.width);
        this.rotate = typeof e.detail.rotate !== 'undefined' ? e.detail.rotate : '';
        this.scaleX = typeof e.detail.scaleX !== 'undefined' ? e.detail.scaleX : '';
        this.scaleY = typeof e.detail.scaleY !== 'undefined' ? e.detail.scaleY : '';
      },
    });
  },
  beforeDestroy() {
    this.cropper.destroy();
  },
  computed: {
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
     * Move
     * @param x
     * @param y
     */
    cropMove(x, y) {
      this.cropper.move(x, y);
    },

    /**
     * Scale - mirroring Y
     */
    cropScaleY() {
      this.cropper.scale(1, this.cropper.getData().scaleY === 1 ? -1 : 1);
    },

    /**
     * Scale - mirroring X
     */
    cropScaleX() {
      this.cropper.scale(this.cropper.getData().scaleX === 1 ? -1 : 1, 1);
    },

    /**
     * Rotate
     * @param grade
     */
    cropRotate(grade) {
      this.cropper.rotate(grade);
    },

    /**
     * Zoom
     * @param ratio
     */
    cropZoom(ratio) {
      this.cropper.zoom(ratio);
    },

    /**
     * Reset
     */
    cropReset() {
      this.cropper.reset();
    },

    /**
     * Set data from form
     */
    setData() {
      this.cropper.setData({
        x: this.x,
        y: this.y,
        width: this.width,
        height: this.height,
        rotate: this.rotate,
        scaleX: this.scaleX,
        scaleY: this.scaleY,
      });
    },

    /**
     * Save cropped image
     */
    cropSave() {
      this.cropper.getCroppedCanvas().toBlob(
        (blob) => {
          const formData = new FormData();
          // add disk name
          formData.append('disk', this.$store.getters['fm/selectedDisk']);
          // add path
          formData.append('path', this.selectedItem.dirname);
          // new image
          formData.append('file', blob, this.selectedItem.basename);

          this.$store.dispatch('fm/updateFile', formData).then((response) => {
          // if file updated successfully
            if (response.data.result.status === 'success') {
            // close cropper
              this.$emit('closeCropper');
            }
          });
        },
        this.selectedItem.extension !== 'jpg'
          ? `image/${this.selectedItem.extension}`
          : 'image/jpeg',
      );
    },
  },
};
</script>

<style lang="scss">
    @import "~cropperjs/dist/cropper.css";
    .cropper-preview {
          width:100%;
          height:100%;
          margin: 0px auto;
          overflow:hidden;
          border: 1px solid;
          border: 1px solid rgba(197, 197, 197, 0.933);
          border-radius: 6px;
          img {
            width:100%;
            height:100%;
            object-fit: contain;
          }
    }
    .el-custom-form{
      .el-form-item__label{
        padding:0 !important;
      }
      .el-form{
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-end;
        .el-col{
          float:unset;
          .el-form-item{
            margin-bottom:unset;  
          }
        }
      }
    }
</style>
