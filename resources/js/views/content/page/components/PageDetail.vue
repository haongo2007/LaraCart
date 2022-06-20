<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
      <el-page-header :content="$t('route.'+this.$route.meta.title)+(this.$route.params.id ? ' - ' + this.$route.params.id : '' )" @back="goBackList"/>
      <el-button-group>
			  <el-upload
			  	action="#"
				  class="upload-demo"
				  ref="upload"
				  :on-change="uploadDesign"
				  :show-file-list="false"
				  :auto-upload="false">
				  <el-button icon="el-icon-upload2" type="warning" :loading="loading" :disabled="loading"></el-button>
				</el-upload>
        <el-button type="primary" icon="el-icon-download" v-on:click="downloadDesign" :loading="loading" :disabled="loading"/>
        <el-button type="success" icon="el-icon-check" v-on:click="saveHtml" :loading="loading" :disabled="loading"/>
      </el-button-group>
    </div>
    <el-col :span="24" style="height: calc(100vh - 200px);">
      <EmailEditor
        ref="emailEditor"
        v-on:load="editorLoaded"
        :tools="tools"
        v-on:ready="editorReady"
        mode="web"
      />
    </el-col>

    <el-dialog
      :show-close="false"
      :title="$t('form.confirm_infomation_to_add_page')"
      :visible.sync="confirmStoreDialog"
      width="30%"
    >
      <el-form ref="dataForm" :model="dataTemp" :rules="dataRules" class="form-container" label-width="150px">

          <el-form-item :label="$t('form.store')" prop="store_id" v-if="!dataTemp.store_id">
            <el-radio v-for="(item,index) in storeList" :key="index" v-model="dataTemp.store_id" :label="index">
              {{ item.descriptions_current_lang[0].title }}
            </el-radio>
          </el-form-item>

          <el-form-item :label="$t('form.language')" prop="lang">
            <el-radio-group v-model="dataTemp.lang">
                <el-radio-button v-for="(lang,index) in languages" :label="index" :key="index">{{ lang }}</el-radio-button>
            </el-radio-group>
          </el-form-item>

          <el-form-item :label="$t('form.page')" prop="Page">
            <el-select v-model="dataTemp.page_id" :placeholder="$t('form.page')" clearable style="width: 100%" class="filter-item" @change="handSelectPage">
              <el-option v-for="item in pageList" :key="item.id" :label="item.title" :value="item.id" />
            </el-select>
          </el-form-item>

          <el-form-item :label="$t('form.name')" prop="title">
            <el-input v-model="dataTemp.title" :placeholder="$t('form.name')" />
          </el-form-item>

          <el-form-item :label="$t('form.tags')">
            <el-tag
              v-for="tag in dataTemp.keyword"
              :key="tag"
              closable
              :disable-transitions="false"
              @close="handleClose(tag)"
            >
              {{ tag }}
            </el-tag>

            <el-input
              v-if="inputTagsVisible"
              ref="savekeywordInput"
              v-model="dynamicTags"
              class="input-new-tag"
              size="mini"
              @keyup.enter.native="handleInputConfirm"
              @blur="handleInputConfirm"
            />
            <el-button v-else class="button-new-tag" size="small" @click="showTagsInput">+ New Tag</el-button>
          </el-form-item>

          <el-form-item :label="$t('form.description')">
            <el-input
              v-model="dataTemp.description"
              :rows="2"
              type="textarea"
              :placeholder="$t('form.description')"
            />
          </el-form-item>

          <el-form-item :label="$t('form.status')" prop="status" v-show="dataTemp.page_id == 0 || isEdit">
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

          <el-form-item :label="$t('form.banner')" v-show="dataTemp.page_id == 0 || isEdit">
            <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
            <div class="image-uploading pull-right">
              <el-image v-if="dataTemp.image" :src="dataTemp.image">
                <div slot="placeholder" class="image-slot">
                  <i class="el-icon-loading" />
                </div>
              </el-image>
              <i v-if="dataTemp.image" class="el-icon-close" @click="resetImageUpload()" />
            </div>
          </el-form-item>
        </div>

      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" :loading="actionState" :disabled="actionState" @click="isEdit ? updatePage() : CreatePage()">{{ $t('form.confirm') }}</el-button>
      </span>
    </el-dialog>

    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </el-row>
</template>

<script>

const defaultForm = {
};

import EmailEditor from '@/components/PageEditor';
import sample from '@/components/PageEditor/sample.json';
import Cookies from 'js-cookie';
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import PageResource from '@/api/page';
import LanguageResource from '@/api/languages';
import reloadRedirectToList from '@/utils';

const pageResource = new PageResource();
const languageResource = new LanguageResource();
const pageListDefault = {
        id: '0',
    		title:'Create new'
    	};
export default {
  name: 'PageDetail',
  props: {
    isEdit: Boolean,
    dataTemp: Object,
    dataRules: Object,
    design: Object,
  },
  components: {
    EmailEditor,
    FileManager,
  },
  data() {
    return {
      dialogStorageVisible: false,
      inputTagsVisible: false,
      confirmStoreDialog: false,
      loading:false,
      actionState:false,
      componentStorage: '',
      dynamicTags: '',
      languages: [],
      pageList:[
      	pageListDefault
      ],
      tools: {
        video: {
          enabled: true
        },
        social: {
          enabled: true
        }
      },
    };
  },
  created() {
    let store_ck = Cookies.get('store');
    if (store_ck) {
      store_ck = JSON.parse(store_ck);
    }
    if (store_ck && store_ck.length == 1) {
      this.dataTemp.store_id = store_ck[0];
    }
    EventBus.$on('getFileResponse', this.handlerGeturl);
    if (this.isEdit) {
        this.langList(this.dataTemp.store_id);
    }
  },
  watch:{
    confirmStoreDialog(newVal){
      this.loading = newVal;
    },
    'dataTemp.store_id'(newVal) {
      if (newVal) {
        this.langList(newVal);
      	this.dataTemp.lang = '';
      	this.pageList = [Object.assign({},pageListDefault)];
      }
    },
    'dataTemp.lang'(newVal) {
      if (newVal) {
      	const data = pageResource.list({except_lang:newVal,store_id:this.dataTemp.store_id}).then((res)=>{
      		this.pageList = [...res.data,Object.assign({},pageListDefault)];
      	});
      }
    },
	},
  computed:{
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  methods: {
  	handSelectPage(item){
  		this.dataTemp.page_id = item;
  	},
  	langList(store_id){
  		languageResource.fetchLanguagesActive(store_id)
        .then(({data}) => {
          this.languages = data;
        })
  	},
    updatePage(){
      this.$refs['dataForm'].validate((valid) => {
      	console.log(valid);
        if (valid) {
        	this.actionState = true;
          	pageResource.update(this.dataTemp.page_id,this.dataTemp).then((res) => {
	            if (res) {
								this.$message({
									type: 'success',
									message: 'Update successfully',
								});
								const view = this.$router.resolve({ name: 'PageEdit' }).route;
								this.$store.dispatch('tagsView/delCachedView', view);
								reloadRedirectToList('PageList');
	      				this.confirmStoreDialog = false;
	            } else {
	              this.$message({
	                type: 'error',
	                message: 'Create failed',
	              });
	            }
          	}).catch(err => {

          	});
        	this.actionState = true;
        }
      });
    },
    CreatePage(){
      this.$refs['dataForm'].validate((valid) => {
      	console.log(valid);
        if (valid) {
        	this.actionState = true;
          	pageResource.store(this.dataTemp).then((res) => {
	            if (res) {
					this.$message({
						type: 'success',
						message: 'Create successfully',
					});
					const view = this.$router.resolve({ name: 'PageCreate' }).route;
					this.$store.dispatch('tagsView/delCachedView', view);
					reloadRedirectToList('PageList');
	      			this.confirmStoreDialog = false;
	            } else {
	              this.$message({
	                type: 'error',
	                message: 'Create failed',
	              });
	            }
          	}).catch(err => {

          	});
        	this.actionState = true;
        }
      });
    },
    goBackList(){
      this.$router.push({ name: 'PageList' });
    },
    // called when the editor is created
    editorLoaded() {
    	this.loading = true;
    	let design = sample;
    	if (Object.keys(this.dataTemp.design).length) {
    		design = this.dataTemp.design;
    	}
			// Pass the template JSON here
			this.$refs.emailEditor.editor.loadDesign(design);
    },
    // called when the editor has finished loading
    editorReady() {
    	this.loading = false;
    	this.saveDesign();
    },
    uploadDesign(file){
    	let reader = new FileReader();
			reader.onload = e => {
				let json = JSON.parse(e.target.result);
				this.dataTemp.design = json;
				this.editorLoaded();
	    	this.loading = false;
			};
     	reader.readAsText(file.raw);
    },
    saveDesign() {
      this.$refs.emailEditor.editor.saveDesign((design) => {
      	this.dataTemp.design = design;
      });
    },
    downloadDesign(){
      	let text = JSON.stringify(this.dataTemp.design);
				let filename = 'design.json';
				let element = document.createElement('a');
				element.setAttribute('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(text));
				element.setAttribute('download', filename);

				element.style.display = 'none';
				document.body.appendChild(element);
				element.click();
				document.body.removeChild(element); 
    },
    saveHtml() {
      this.$refs.emailEditor.editor.exportHtml((data) => {
        this.confirmStoreDialog = true;
        this.dataTemp.content = data.html;
        this.dataTemp.design = data.design;
      });
    },
    handleInputConfirm() {
      const inputTags = this.dynamicTags;
      if (inputTags) {
        if (!this.dataTemp.keyword.includes(inputTags)) {
          this.dataTemp.keyword.push(inputTags);
        }
      }
      this.inputTagsVisible = false;
      this.dynamicTags = '';
    },
    handleClose(tag) {
      this.dataTemp.keyword.splice(this.dataTemp.keyword.indexOf(tag), 1);
      this.inputTagsVisible = true;
      this.inputTagsVisible = false;
    },
    showTagsInput() {
      this.inputTagsVisible = true;
      this.$nextTick(_ => {
        this.$refs.savekeywordInput.$refs.input.focus();
      });
    },
    handlerGeturl(data) {
      if (data) {
        this.dataTemp.image = data[0];
        this.dialogStorageClose();
      }
    },
    handleVisibleStorage(){
      this.$store.commit('fm/setDisks', 'page');
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
  },
};
</script>
<style type="text/css">
  .unlayer-editor{
    height: 100%;
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
  .image-uploading .el-icon-close{
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
  }
</style>