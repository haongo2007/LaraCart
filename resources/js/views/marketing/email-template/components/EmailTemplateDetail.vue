<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
      <el-page-header :content="$t('route.'+this.$route.meta.title)+(this.$route.params.id ? ' - ' + this.$route.params.id : '' )" @back="goBackList"/>
			  <el-upload
			  	action="#"
				  class="upload-demo"
				  ref="upload"
				  :on-change="uploadDesign"
				  :show-file-list="false"
				  :auto-upload="false">
	    		<el-button type="warning" slot="trigger" icon="el-icon-upload2" :loading="loading" :disabled="loading">{{ $t('form.import') }}</el-button>
	        <el-button type="info" icon="el-icon-download" v-on:click="downloadDesign" :loading="loading" :disabled="loading">{{ $t('form.export') }}</el-button>
	        <el-button type="primary" icon="el-icon-check" v-on:click="saveDesign" :loading="loading" :disabled="loading">{{ $t('form.save') }}</el-button>
	        <el-button type="success" icon="el-icon-upload" v-on:click="saveHtml" :loading="loading" :disabled="loading">{{ $t('form.upload') }}</el-button>
	      </el-upload>
	    	</el-upload>
    </div>
    <el-col :span="24" style="height: calc(100vh - 200px);">
      <EmailEditor
        ref="emailEditor"
        v-on:load="editorLoaded"
        :tools="tools"
        v-on:ready="editorReady"
        mode="email"
      />
    </el-col>

    <el-dialog
      :show-close="false"
      :title="isEdit ? $t('form.confirm_infomation_to_edit_emailTemplate') : $t('form.confirm_infomation_to_add_emailTemplate')"
      :visible.sync="confirmStoreDialog"
      :before-close="resetTemp"
      width="40%"
    >
        <el-form ref="dataForm" :model="temp" :rules="dataRules" class="form-container" label-width="150px">

            <el-form-item :label="$t('form.store')" prop="store_id">
              <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index">
                {{ item.descriptions_current_lang[0].title }}
              </el-radio>
            </el-form-item>

            <el-form-item :label="$t('form.group')" prop="group">
              <el-select v-model="temp.group" :placeholder="$t('form.group')" clearable style="width: 100%" class="filter-item" @change="handSelectGroup">
                <el-option v-for="(item,index) in groupList" :key="index" :label="item.title" :value="index" />
              </el-select>
            </el-form-item>

            <el-form-item :label="$t('form.name')" prop="name">
              <el-input v-model="temp.name" :placeholder="$t('form.name')" />
            </el-form-item>

            <el-form-item :label="$t('form.status')" prop="status">
              <el-tooltip :content="'Switch value: ' + temp.status" placement="top">
                <el-switch
                  v-model="temp.status"
                  active-color="#13ce66"
                  inactive-color="#ff4949"
                  active-value="1"
                  inactive-value="0"
                />
              </el-tooltip>
            </el-form-item>
            <el-form-item >
              <el-button type="primary" :loading="actionState" :disabled="actionState" @click="isEdit ? update() : create()">{{ $t('form.confirm') }}</el-button>
            </el-form-item>
        </el-form>
    </el-dialog>

  </el-row>
</template>

<script>

const defaultForm = {
};

import EmailEditor from '@/components/PageEditor';
import sample from '@/components/PageEditor/sample_email.json';
import EmailTemplateResource from '@/api/email-template';
import reloadRedirectToList from '@/utils';

const emailTemplateResource = new EmailTemplateResource();

export default {
  name: 'EmailTemplateDetail',
  props: {
    isEdit: Boolean,
    dataTemp: Object,
    dataRules: Object,
  },
  components: {
    EmailEditor,
  },
  data() {
    return {
      confirmStoreDialog: false,
      loading:false,
      actionState:false,
      groupList:[],
      currentVariable:[],
      temp:{},
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
  	this.temp = Object.assign({},this.dataTemp);
    this.fetchGroup();
  },
  watch:{
    confirmStoreDialog(newVal){
      this.loading = newVal;
    },
	},
  computed:{
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  methods: {
    async fetchGroup(){
      const {data} = await emailTemplateResource.getGroups();
      this.groupList = data;
    },
  	handSelectGroup(item){
      this.$notify.closeAll();
  		this.currentVariable = this.groupList[item].required;
      let content = this.currentVariable.map((item) => {
        return '<p>'+item+'</p>';
      })
      content = content.join('');
      this.$notify.warning({
        title: 'Warning',
        message: content,  
        position: 'bottom-left',
        dangerouslyUseHTMLString: true,
        duration: 0
      });
  	},
    update(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
        	this.actionState = true;
          	emailTemplateResource.update(this.temp.id,this.temp).then((res) => {
	            if (res) {
								  this.$message({
  									type: 'success',
  									message: 'Update successfully',
  								});
  								const view = this.$router.resolve({ name: 'EmailTemplateEdit' }).route;
  								this.$store.dispatch('tagsView/delCachedView', view);
  								reloadRedirectToList('EmailTemplateList');
  	      				this.confirmStoreDialog = false;
                  this.$notify.closeAll();
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
    create(){
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
        	this.actionState = true;
          	emailTemplateResource.store(this.temp).then((res) => {
	            if (res) {
      					this.$message({
      						type: 'success',
      						message: 'Create successfully',
      					});
      					const view = this.$router.resolve({ name: 'EmailTemplateCreate' }).route;
      					this.$store.dispatch('tagsView/delCachedView', view);
      					reloadRedirectToList('EmailTemplateList');
                this.$notify.closeAll();
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
    resetTemp(done){
    	this.temp = Object.assign({},this.dataTemp);
      this.actionState = false;
    	done();
    },
    goBackList(){
      this.$router.push({ name: 'EmailTemplateList' });
    },
    // called when the editor is created
    editorLoaded() {
    	this.loading = true;
    	let design = sample;
    	if (Object.keys(this.temp.design).length) {
    		design = this.temp.design;
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
    	if(file.raw.type != 'application/json'){
    		this.$message({
          type: 'error',
          message: 'File import must be application/json type!',
        });
        return false;
    	};
    	let reader = new FileReader();
			reader.onload = e => {
				let json = JSON.parse(e.target.result);
				this.temp.design = json;
				this.editorLoaded();
	    	this.loading = false;
			};
     	reader.readAsText(file.raw);
    },
    saveDesign() {
      this.$refs.emailEditor.editor.saveDesign((design) => {
      	this.temp.design = design;
      });
    },
    downloadDesign(){
      	let text = JSON.stringify(this.temp.design);
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
        this.temp.content = data.html;
        this.temp.design = data.design;
      });
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
  .el-notification{
    width: unset!important;
  }
</style>