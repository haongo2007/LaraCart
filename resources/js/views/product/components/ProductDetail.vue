<template>
  <el-row :gutter="20" style="margin:0px 0px 60px 0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="20" :offset="2">
      <el-skeleton :rows="20" animated :loading="loading" />
      <el-form ref="dataForm" :model="temp" :rules="rules" class="form-container" label-width="150px">
        <el-steps v-show="!loading" :space="200" simple :active="active" finish-status="success" style="margin-bottom: 20px;">
          <el-step v-for="(step,key,index) in stepContent" :key="index" :title="(step.title ? step.title : step)" :icon="(step.icon ? step.icon : 'el-icon-edit')" />
        </el-steps>

        <div v-for="(content,key,index) in stepContent" :key="key">

          <div v-if="componentInfo.hasOwnProperty(key)" v-show="index === active">
            <component :data-refs="$refs['dataForm']" :data-active="active" @handleProcessTemp="handleProcessTemp" @handleProcessActive="handleProcessActive" :is="componentInfo[key]" />
          </div>

          <div v-else v-show="index === active">
            <el-row>
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
                  <Tinymce ref="editor" v-model="temp.descriptions[key].content" :height="400" />
                </el-form-item>
              </el-col>
            </el-row>
            <el-button-group class="pull-right">
              <el-button v-if="active > 0" type="warning" icon="el-icon-arrow-left" @click="backStep">
                Previous
              </el-button>
              <el-button v-if="!action" type="primary" icon="el-icon-arrow-right" @click="nextStep">
                Next
              </el-button>
            </el-button-group>
          </div>

        </div>
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import Tinymce from '@/components/Tinymce';
import Sticky from '@/components/Sticky'; // Sticky header
import { fetchLanguagesActive } from '@/api/languages';
import InfoAttribute from './InfoAttribute';
import InfoGeneral from './InfoGeneral';
import InfoProperty from './InfoProperty';
import InfoThumbnail from './InfoThumbnail';
import InfoPromotion from './InfoPromotion';
import ProductResource from '@/api/product';

const productResource = new ProductResource();

const defaultForm = {
  id: '',
  kind: '',
  descriptions: {
  },
};

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
    kind: {
      type: Number,
      default: false,
    },
  },
  data() {
    return {
      loading: true,
      action: false,
      languages: [],
      stepContent: [],
      active: 0,
      componentInfo: {},
      temp: Object.assign({}, defaultForm),
      rules: {
        descriptions: [],
      },
      inputTagsVisible: false,
      dynamicTags: '',
    };
  },
  created() {
    this.fetchLanguages();
    this.temp.kind = this.kind;
    if (this.isEdit){
      const id = this.$route.params && this.$route.params.id;
      this.getRecursive(id);
      this.fetchCategory(id);
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
      const keyLang = Object.keys(this.languages)[this.active];
      const keyStep = Object.keys(this.stepContent)[this.active + 1];
      if (this.componentInfo.hasOwnProperty(keyStep)){
        this.componentInfo[keyStep] = keyStep;   
      }
      if (keyLang){
        this.$refs['dataForm'].validateField('descriptions.' + keyLang + '.title', this._checkValidate);
      } else {
        const stepNum = _.size(this.stepContent) - 1;
        if (++this.active == stepNum){
          this.action = true;
        }
      }
    },
    _checkValidate(msg){
      if (!msg) {
        this.active++;
      }
    },
    fetchCategory(id) {
      const that = this;
      categoryResource.get(id)
        .then(({ data } = response) => {
          const desc = data.descriptions;
          desc.forEach(function(v, i) {
            that.temp.descriptions[v.lang].title = v.title;
            that.temp.descriptions[v.lang].description = v.description;
            that.temp.descriptions[v.lang].keyword = v.keyword != '' ? v.keyword.split(',') : [];
          });
          that.temp.id = data.id;
        })
        .catch(err => {
          console.log(err);
        });
    },
    fetchLanguages() {
      fetchLanguagesActive()
        .then(data => {
          this.languages = Object.assign({}, data.data);
          var that = this;
          Object.keys(data.data).forEach(function(key, index) {
            that.$set(that.temp.descriptions, key, {});
            that.$set(that.temp.descriptions[key], 'description', '');
            that.$set(that.temp.descriptions[key], 'title', '');
            that.$set(that.temp.descriptions[key], 'keyword', []);
            that.$set(that.temp.descriptions[key], 'content', '');

            that.$set(that.rules.descriptions, key, []);

            that.$set(that.rules.descriptions[key], 'title',
              [
                {
                  required: true,
                  message: 'Name ' + data.data[key] + ' is required',
                  trigger: 'blur',
                },
                {
                  min: 3,
                  message: 'Length min 3',
                  trigger: 'blur',
                },
              ]
            );
          });
          // /// create step form
          this.stepContent = data.data;
          this.stepContent['info-general'] = {
            title: 'General',
            icon: 'el-icon-view',
          };
          this.$set(this.componentInfo, 'info-general', '');

          this.stepContent['info-promotion'] = {
            title: 'Promotion',
            icon: 'el-icon-s-promotion',
          };
          this.$set(this.componentInfo, 'info-promotion', '');

          this.stepContent['info-attribute'] = {
            title: 'Attribute',
            icon: 'el-icon-news',
          };
          this.$set(this.componentInfo, 'info-attribute', '');

          this.stepContent['info-property'] = {
            title: 'Property',
            icon: 'el-icon-menu',
          };
          this.$set(this.componentInfo, 'info-property', '');

          this.stepContent['info-thumbnail'] = {
            title: 'Thumbnail',
            icon: 'el-icon-picture-outline',
          };
          this.$set(this.componentInfo, 'info-thumbnail', '');

          this.loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    createData() {
      const loading = this.$loading({
        target: '.el-form',
      });
      const form_data = new FormData();
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
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          const form_data = new FormData();
          for (var key in this.temp) {
            if ((typeof this.temp[key] === 'object' || typeof this.temp[key] === 'array') && key != 'image') {
              form_data.append(key, JSON.stringify(this.temp[key]));
            } else {
              form_data.append(key, this.temp[key]);
            }
          }
          form_data.append('_method', 'PUT');

          categoryResource.update(this.temp.id, form_data).then((res) => {
            this.reloadRedirectToList('CategoryList');

            this.$message({
              type: 'success',
              message: 'Updated successfully',
            });

            loading.close();
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
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
        this.createData()
      }else if(this.active < data){
        this.nextStep();
      }else{
        this.backStep();
      }
    },
    handleProcessTemp(data){
      this.temp = {...this.temp,...data};
    }
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
</style>
