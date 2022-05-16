<template>
  <el-row :gutter="20">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="18" :offset="3">
      <el-form ref="dataForm" :model="dataTemp" :rules="dataRules" class="form-container" label-width="150px">
        <el-steps :space="200" simple :active="active" finish-status="success" style="margin-bottom: 20px;">
          <el-step v-for="(lang,key,index) in dataLanguages" :key="index" :title="lang" :icon="key == 'last' ? 'el-icon-picture' : 'el-icon-edit'" />
        </el-steps>
        <div v-for="(lang,key,index) in dataLanguages" :key="key">
          <div v-if="key != 'last'" v-show="index === active">
            <el-row class="el-main-form">
              <el-col :span="24">
                <el-form-item :label="$t('table.name')" :prop="'descriptions.'+key+'.title'">
                  <el-input v-model="dataTemp.descriptions[key].title" />
                </el-form-item>

                <el-form-item :label="$t('table.tags')">
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

                <el-form-item :label="$t('table.description')">
                  <el-input
                    v-model="dataTemp.descriptions[key].description"
                    :rows="2"
                    type="textarea"
                    placeholder="Please input"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <div v-if="key === 'last'" v-show="index === active">
            <el-row class="el-main-form">
              <el-col :span="24">
                <el-form-item :label="$t('table.sort')" prop="sort">
                  <el-input-number v-model.number="dataTemp.sort" :min="1" />
                </el-form-item>

                <el-form-item :label="$t('table.top')" prop="top">
                  <el-tooltip :content="'Switch value: ' + dataTemp.top" placement="top">
                    <el-switch
                      v-model="dataTemp.top"
                      active-color="#13ce66"
                      inactive-color="#ff4949"
                      active-value="1"
                      inactive-value="0"
                    />
                  </el-tooltip>
                </el-form-item>

                <el-form-item :label="$t('table.parent')" prop="parent">
                  <!-- <el-cascader
                    v-model="dataTemp.parent"
                    :options="listRecursive"
                    :props="cateRecurProps"
                    :show-all-levels="false"
                    clearable
                    filterable
                  /> -->
                  <el-autocomplete
                    style="margin-right: 20px"
                    v-for="(item,index) in categories"
                    :key="index"
                    v-model="item.name"
                    :fetch-suggestions="querySearchAsync"
                    @focus="checkParentFocus(index)"
                    placeholder="Please input"
                    @select="handleSelectCategory"
                    value-key="name"/>

                </el-form-item>

                <el-form-item :label="$t('table.status')" prop="status">
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

                <el-form-item :label="$t('table.banner')">
                  <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
                </el-form-item>
                <div class="image-uploading">
                  <el-image v-if="dataTemp.fileUrl" :src="dataTemp.fileUrl">
                    <div slot="placeholder" class="image-slot">
                      <i class="el-icon-loading" />
                    </div>
                  </el-image>
                  <i v-if="dataTemp.fileUrl" class="el-icon-close" @click="resetImageUpload()" />
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
            Previous
          </el-button>
          <el-button v-if="!action" type="primary" icon="el-icon-arrow-right" @click="nextStep">
            Next
          </el-button>
          <el-button v-else="action" type="success" icon="el-icon-check" @click="isEdit?updateData():createData()">
            Done
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
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import CategoryResource from '@/api/category';
const categoryResource = new CategoryResource();

export default {
  name: 'CategoryDetail',
  components: {
    Sticky,
    FileManager,
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
      categories:[{
          'name':'',
          'id':'0',
          'parent':'0',
      }],
      category:[],
      cateLevel:0,
      timeout:  null,
      // cateRecurProps: {
      //   children: 'children',
      //   label: 'title',
      //   value: 'id',
      //   checkStrictly: true,
      // },
      // listRecursive: [{
      //   id: '0',
      //   parent: 0,
      //   title: 'Is parent',
      // }],
      inputTagsVisible: false,
      dynamicTags: '',
    };
  },
  created() {
    let id = '';
    if (this.isEdit){
      id = this.$route.params && this.$route.params.id;
    }
    this.getCategory();
    EventBus.$on('getFileResponse', this.handlerGeturl);
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'CategoryList' });
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
        this.dataTemp.fileUrl = data + '&w=644';
        this.dataTemp.image = data;
        this.dialogStorageClose();
      }
    },
    async getCategory(id){
      let obj = {
          parent:0,
          store_id : this.dataTemp.store_id
      };

      if (id) {
        obj['id'] = id;
      }
      const { data } = await categoryResource.list(obj);
      // data.unshift(this.listRecursive[0]);
      this.category.push(data);
      if (this.cateLevel == 0) {
        this.category[this.cateLevel].unshift({
          id: '0',
          parent: 0,
          name: 'Is parent',
        })
      }
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            target: '.el-form',
          });
          const form_data = new FormData();
          for (var key in this.dataTemp) {
            if ((typeof this.dataTemp[key] === 'object' || typeof this.dataTemp[key] === 'array') && key != 'image') {
              form_data.append(key, JSON.stringify(this.dataTemp[key]));
            } else {
              form_data.append(key, this.dataTemp[key]);
            }
          }
          categoryResource.store(form_data).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              const view = this.$router.resolve({ name: 'CategoryCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);
              reloadRedirectToList('CategoryList');
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
          const form_data = new FormData();
          for (var key in this.dataTemp) {
            if ((typeof this.dataTemp[key] === 'object' || typeof this.dataTemp[key] === 'array') && key != 'image') {
              form_data.append(key, JSON.stringify(this.dataTemp[key]));
            } else {
              form_data.append(key, this.dataTemp[key]);
            }
          }
          form_data.append('_method', 'PUT');

          categoryResource.update(this.dataTemp.id, form_data).then((res) => {
            reloadRedirectToList('CategoryList');

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
      this.$store.commit('fm/setDisks', 'category');
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
      this.dataTemp.fileUrl = '';
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
    querySearchAsync(queryString, cb) {
      var category = this.category[this.cateLevel];
      var results = queryString ? category.filter(this.createFilter(queryString)) : category;
      
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        cb(results);
      }, 500 * Math.random());
    },
    createFilter(queryString) {
      return (category) => {
        return (category.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    checkParentFocus(level){
      this.cateLevel = level;
    },
    async handleSelectCategory(item){
      if (item.hasChildren) {
        this.cateLevel++;
        const { data } = await categoryResource.getChildren(item.id);
        this.category.push(data);
        this.categories.push({
          id:String(this.cateLevel),
          parent:item.id,
          name:''
        });
      }
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

  .el-main-form{
    height: calc(100vh - 320px);
    overflow-y: scroll;
  }
  .el-main-form::-webkit-scrollbar {
      width: 0;
      background: transparent;
  }
</style>
