<template>
  <el-row :gutter="20">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col class="el-content-form" :span="22" :offset="1">
      <!-- <el-skeleton :rows="20" animated/> -->
      <el-steps :space="200" simple :active="active" finish-status="success" style="margin-bottom: 20px;">
        <el-step v-for="(step,key,index) in dataStepContent" :key="index" :title="(step.title ? step.title : step)" :icon="(step.icon ? step.icon : 'el-icon-edit')" />
      </el-steps>

      <div v-for="(content,key,index) in dataStepContent" :key="key">

        <div v-if="dataComponentInfo.hasOwnProperty(key)" v-show="index === active">
          <component :is="dataComponentInfo[key]" :data-product="product" :data-active="active" @handleProcessTemp="handleProcessTemp" @handleProcessActive="handleProcessActive" />
        </div>

        <div v-else v-show="index === active">
          <el-form ref="dataForm" :model="temp" :rules="dataRules" class="form-container" label-width="150px">
            <el-row class="el-main-form">
              <el-col :span="24">
                <el-form-item :label="$t('table.name')" :prop="'descriptions.'+key+'.title'">
                  <el-input v-model="temp.descriptions[key].title" />
                </el-form-item>

                <el-form-item :label="$t('table.tags')">
                  <el-tag
                    v-for="tag in temp.descriptions[key].keyword"
                    :key="tag"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag,key)"
                  >
                    {{ tag }}
                  </el-tag>

                  <el-input
                    v-if="inputTagsVisible"
                    ref="savekeywordInput"
                    v-model="dynamicTags"
                    class="input-new-tag"
                    size="mini"
                    @keyup.enter.native="handleInputConfirm(key)"
                    @blur="handleInputConfirm(key)"
                  />
                  <el-button v-else class="button-new-tag" size="small" @click="showTagsInput">+ New Tag</el-button>
                </el-form-item>

                <el-form-item :label="$t('table.description')">
                  <el-input
                    v-model="temp.descriptions[key].description"
                    :rows="2"
                    type="textarea"
                    placeholder="Please input"
                  />
                </el-form-item>

                <el-form-item :label="$t('table.content')" :prop="'descriptions.'+key+'.content'">
                  <Tinymce ref="editor" v-model="temp.descriptions[key].content" :height="400" :style="{'width': 'calc(100% - 2px)'}" />
                </el-form-item>
              </el-col>
            </el-row>
          </el-form>
          <el-row>
            <el-button-group class="pull-right">
              <el-button v-if="active > 0" type="warning" icon="el-icon-arrow-left" @click="backStep">
                Previous
              </el-button>
              <el-button v-if="!action" type="primary" icon="el-icon-arrow-right" @click="nextStep">
                Next
              </el-button>
            </el-button-group>
          </el-row>
        </div>

      </div>
    </el-col>
  </el-row>
</template>

<script>
import Tinymce from '@/components/Tinymce';
import Sticky from '@/components/Sticky'; // Sticky header
import InfoAttribute from './InfoAttribute';
import InfoGeneral from './InfoGeneral';
import InfoProperty from './InfoProperty';
import InfoThumbnail from './InfoThumbnail';
import InfoPromotion from './InfoPromotion';
import ProductResource from '@/api/product';

const productResource = new ProductResource();

export default {
  name: 'ProductDetail',
  components: {
    Sticky,
    Tinymce,
    InfoAttribute,
    InfoGeneral,
    InfoProperty,
    InfoThumbnail,
    InfoPromotion,
  },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    dataTemp: {
      type: Object,
      default: false,
    },
    dataLanguages: {
      type: Object,
      default: false,
    },
    dataComponentInfo: {
      type: Object,
      default: false,
    },
    dataStepContent: {
      type: Object,
      default: false,
    },
    dataRules: {
      type: Object,
      default: false,
    },
    dataProduct: {
      type: Object,
      default: undefined,
    },
  },
  data() {
    return {
      action: false,
      active: 0,
      inputTagsVisible: false,
      dynamicTags: '',
      temp: {},
      product: {},
    };
  },
  created() {
    this.temp = this.dataTemp;
    if (this.dataProduct) {
      this.product = this.dataProduct;
    }
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'ProductList' });
    },
    backStep() {
      if (--this.active < 0){
        return false;
      } else {
        this.action = false;
      }
    },
    nextStep() {
      const keyLang = Object.keys(this.dataLanguages)[this.active];
      const keyStep = Object.keys(this.dataStepContent)[this.active + 1];
      if (this.dataComponentInfo.hasOwnProperty(keyStep)){
        this.dataComponentInfo[keyStep] = keyStep;
      }
      if (keyLang){
        this.$refs['dataForm'][this.active].validate((valid) => {
          if (valid) {
            this.active++;
          }
        });
      } else {
        const stepNum = _.size(this.dataStepContent) - 1;
        if (++this.active == stepNum){
          this.action = true;
        }
      }
    },
    createData() {
      const loading = this.$loading({
        target: '.el-row',
      });
      let form_data = new FormData();
      for (var key in this.temp) {
        if ((typeof this.temp[key] === 'object' || typeof this.temp[key] === 'array') && key != 'image') {
          form_data.append(key, JSON.stringify(this.temp[key]));
        } else {
          form_data.append(key, this.temp[key]);
        }
      }
      productResource.store(form_data).then((res) => {
        if (res) {
          loading.close();
          this.$message({
            type: 'success',
            message: 'Create successfully',
          });
          const view = this.$router.resolve({ name: 'ProductCreateSingle' }).route;
          this.$store.dispatch('tagsView/delCachedView', view);
          this.reloadRedirectToList('ProductList');
        } else {
          this.$message({
            type: 'error',
            message: 'Create failed',
          });
          loading.close();
        }
      }).catch(err => {
        loading.close();
      });
    },
    updateData() {
      const loading = this.$loading({
        target: '.el-row',
      });
      console.log(this.temp);
      let form_data = new FormData();
      for (var key in this.temp) {
        if ((typeof this.temp[key] === 'object' || typeof this.temp[key] === 'array') && key != 'image') {
          form_data.append(key, JSON.stringify(this.temp[key]));
        } else {
          form_data.append(key, this.temp[key]);
        }
      }
      form_data.append('_method', 'PUT');
      productResource.update(this.temp.id, form_data).then((res) => {
        this.reloadRedirectToList('ProductList');

        this.$message({
          type: 'success',
          message: 'Updated successfully',
        });

        loading.close();
      }).catch(err => {
        loading.close();
      });
    },
    reloadRedirectToList(cpn){
      const view = this.$router.resolve({ name: cpn }).route;
      this.$store.dispatch('tagsView/delCachedView', view).then(() => {
        const { fullPath } = view;
        this.$router.replace({
          path: '/redirect' + fullPath,
        });
      });
    },
    handleClose(tag, key) {
      this.temp.descriptions[key].keyword.splice(this.temp.descriptions[key].keyword.indexOf(tag), 1);
      this.inputTagsVisible = true;
      this.inputTagsVisible = false;
    },
    showTagsInput() {
      this.inputTagsVisible = true;
      this.$nextTick(_ => {
        this.$refs.savekeywordInput[this.active].$refs.input.focus();
      });
    },
    handleInputConfirm(key) {
      const inputTags = this.dynamicTags;
      if (inputTags) {
        if (!this.temp.descriptions[key].keyword.includes(inputTags)) {
          this.temp.descriptions[key].keyword.push(inputTags);
        }
      }
      this.inputTagsVisible = false;
      this.dynamicTags = '';
    },
    handleProcessActive(data){
      if (this.active == data) {
        if (this.isEdit){
          this.updateData();
        } else {
          this.createData();
        }
      } else if (this.active < data){
        this.nextStep();
      } else {
        this.backStep();
      }
    },
    handleProcessTemp(data){
      this.temp = { ...this.temp, ...data };
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .image-uploading{
    position: relative;
    .el-icon-close{
        cursor: pointer;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 18px;
        opacity: 0;
        transition:all .5s;
    }
    &:hover {
      .el-icon-close{
        opacity: 1;
      }
    }
  }
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    vertical-align: bottom;
  }
  .el-steps--simple{
    padding:13px 1%;
  }
  .el-content-form{
    padding: 10px;
    border: 1px solid #eee;
  }
</style>
