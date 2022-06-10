<template>
  <el-row :gutter="20">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="18" :offset="3">
      <el-form ref="dataForm" :model="dataTemp" :rules="dataRules" class="form-container" label-width="150px">
        <el-steps :space="200" simple :active="active" finish-status="success" style="margin-bottom: 20px;">
          <el-step v-for="(lang,key,index) in dataLanguages" :key="index" :title="lang == 'done' ? $t('form.'+lang) : lang" :icon="key == 'last' ? 'el-icon-picture' : 'el-icon-edit'" />
        </el-steps>
        <div v-for="(lang,key,index) in dataLanguages" :key="key">
          <div v-if="key != 'last'" v-show="index === active">
            <el-row class="el-main-form">
              <el-col :span="24">
                <el-form-item :label="$t('form.name')" :prop="'descriptions.'+key+'.title'">
                  <el-input v-model="dataTemp.descriptions[key].title" :placeholder="$t('form.name')" />
                </el-form-item>

                <el-form-item :label="$t('form.tags')">
                  <el-tag
                    v-for="tag in dataTemp.descriptions[key].keyword"
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

                <el-form-item :label="$t('form.description')">
                  <el-input
                    v-model="dataTemp.descriptions[key].description"
                    :rows="2"
                    type="textarea"
                    :placeholder="$t('form.description')"
                  />
                </el-form-item>

                <el-form-item :label="$t('form.content')">
                  <Tinymce ref="editor" v-model="dataTemp.descriptions[key].content" :height="400" :style="{'width': 'calc(100% - 2px)'}" />
                </el-form-item>

              </el-col>
            </el-row>
          </div>

          <div v-if="key === 'last'" v-show="index === active">
            <el-row class="el-main-form">
              <el-col :span="24">

                <el-form-item :label="$t('form.url_config')">
                  <el-input
                    v-model="dataTemp.alias"
                    :placeholder="$t('form.url_config')"
                  />
                </el-form-item>

                <el-form-item :label="$t('form.sort')" prop="sort">
                  <el-input-number v-model.number="dataTemp.sort" :min="1" />
                </el-form-item>

                <el-form-item :label="$t('form.status')" prop="status">
                  <el-tooltip :content="'Switch value: ' + dataTemp.status" placement="top">
                    <el-switch
                      v-model="dataTemp.status"
                      active-color="#13ce66"
                      inactive-color="#ff4949"
                      active-value="1"
                      inactive-value="0"
                    />
                  </el-tooltip>
                </el-form-item>

                <el-form-item :label="$t('form.banner')">
                  <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
                </el-form-item>
                <div class="image-uploading">
                  <el-image v-if="dataTemp.image" :src="dataTemp.image+'&w=644'">
                    <div slot="placeholder" class="image-slot">
                      <i class="el-icon-loading" />
                    </div>
                  </el-image>
                  <i v-if="dataTemp.image" class="el-icon-close" @click="resetImageUpload()" />
                </div>
                <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
                  <component :is="componentStorage" :get-file="true" />
                </el-dialog>
              </el-col>
            </el-row>
          </div>
        </div>

      </el-form>
      <el-row>
        <el-button-group class="pull-right">
          <el-button v-if="active > 0" type="warning" icon="el-icon-arrow-left" @click="backStep">
            {{ $t('form.prev') }}
          </el-button>
          <el-button v-if="!action" type="primary" icon="el-icon-arrow-right" @click="nextStep">
            {{ $t('form.next') }}
          </el-button>
          <el-button v-else="action" type="success" icon="el-icon-check" @click="isEdit?updateData():createData()">
            {{ $t('form.done') }}
          </el-button>
        </el-button-group>
      </el-row>
    </el-col>
    <slot />
  </el-row>
</template>

<script>
import reloadRedirectToList from '@/utils';
import Sticky from '@/components/Sticky'; // Sticky header
import Tinymce from '@/components/Tinymce';
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import BlogsResource from '@/api/blogs';

const blogsResource = new BlogsResource();

export default {
  name: 'BlogDetail',
  components: {
    Sticky,
    FileManager,
    Tinymce
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
    dataRules: {
      type: Object,
      default: false,
    },
    dataLanguages: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      dialogStorageVisible: false,
      action: false,
      active: 0,
      componentStorage: '',
      disableUseStorage: false,
      inputTagsVisible: false,
      dynamicTags: '',
    };
  },
  created() {
    EventBus.$on('getFileResponse', this.handlerGeturl);
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'BlogList' });
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
      this.$refs['dataForm'].validateField('descriptions.' + keyLang + '.title', this._checkValidate);
    },
    _checkValidate(msg){
      if (!msg) {
        const langNum = _.size(this.dataLanguages) - 1;
        if (++this.active == langNum){
          this.action = true;
        }
      }
    },
    handlerGeturl(data) {
      if (data) {
        this.dataTemp.image = data;
        this.dialogStorageClose();
      }
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });

          blogsResource.store(this.dataTemp).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              const view = this.$router.resolve({ name: 'BlogCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);
              reloadRedirectToList('BlogList');
            } else {
              this.$message({
                type: 'error',
                message: 'Create failed',
              });
              loading.close();
            }
          }).catch(err => {
            console.log(err);
            loading.close();
          });
        }
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });

          blogsResource.update(this.dataTemp.id, this.dataTemp).then((res) => {
            reloadRedirectToList('BlogList');

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
    handleVisibleStorage(){
      this.$store.commit('fm/setDisks', 'blogs');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    resetImageUpload(){
      this.dataTemp.image = '';
      this.componentStorage = '';
    },
    handleClose(tag, key) {
      this.dataTemp.descriptions[key].keyword.splice(this.dataTemp.descriptions[key].keyword.indexOf(tag), 1);
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
        if (!this.dataTemp.descriptions[key].keyword.includes(inputTags)) {
          this.dataTemp.descriptions[key].keyword.push(inputTags);
        }
      }
      this.inputTagsVisible = false;
      this.dynamicTags = '';
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
  .el-main-form{
    height: calc(100vh - 320px);
    overflow-y: scroll;
  }
  .el-main-form::-webkit-scrollbar {
      width: 0;
      background: transparent;
  }
</style>
