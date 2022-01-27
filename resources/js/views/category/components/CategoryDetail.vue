<template>
  <el-row :gutter="20" style="margin:0px;">
    <div style="padding: 24px;">
      <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
    </div>
    <el-col :span="12" :offset="6">
      <el-skeleton :rows="6" animated :loading="loading" />
      <el-form ref="dataForm" :model="temp" :rules="rules" class="form-container" label-width="120px">
        <el-steps v-show="!loading" :space="200" simple :active="active" finish-status="success" style="margin-bottom: 20px;">
          <el-step v-for="(lang,key,index) in languages" :key="index" :title="lang" :icon="key == 'last' ? 'el-icon-picture' : 'el-icon-edit'" />
        </el-steps>
        <div v-for="(lang,key,index) in languages" :key="key">
          <div v-if="key != 'last'" v-show="index === active">
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
          </div>

          <div v-if="key === 'last'" v-show="index === active">

            <el-form-item :label="$t('table.sort')" prop="sort">
              <el-input-number v-model.number="temp.sort" :min="1" />
            </el-form-item>

            <el-form-item :label="$t('table.top')" prop="top">
              <el-tooltip :content="'Switch value: ' + temp.top" placement="top">
                <el-switch
                  v-model="temp.top"
                  active-color="#13ce66"
                  inactive-color="#ff4949"
                  active-value="1"
                  inactive-value="0"
                />
              </el-tooltip>
            </el-form-item>

            <el-form-item :label="$t('table.parent')" prop="parent">
              <el-cascader
                v-model="temp.parent"
                :options="listRecursive"
                :props="cateRecurProps"
                :show-all-levels="false"
                clearable
                filterable
              />
            </el-form-item>

            <el-form-item :label="$t('table.status')" prop="status">
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

            <el-form-item :label="$t('table.banner')">
              <el-button size="small" type="success" @click="handleVisibleStorage()">Pick Image</el-button>
            </el-form-item>
            <div class="image-uploading">
              <el-image v-if="fileUrl" :src="fileUrl">
                <div slot="placeholder" class="image-slot">
                  <i class="el-icon-loading" />
                </div>
              </el-image>
              <i v-if="fileUrl" class="el-icon-close" @click="resetImageUpload()" />
            </div>
            <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
              <component :is="componentStorage" :get-file="true" />
            </el-dialog>
          </div>
        </div>

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
      </el-form>
    </el-col>
  </el-row>
</template>

<script>
import Sticky from '@/components/Sticky'; // Sticky header
import { fetchLanguagesActive } from '@/api/languages';
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import CategoryResource from '@/api/category';
const categoryResource = new CategoryResource();

const defaultForm = {
  id: '',
  alias: '',
  sort: '',
  top: '1',
  parent: '0',
  status: '1',
  image: '',
  descriptions: {
  },
};

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
  },
  data() {
    return {
      loading: true,
      dialogStorageVisible: false,
      action: false,
      languages: [],
      active: 0,
      componentStorage: '',
      fileUrl: '',
      disableUseStorage: false,
      cateRecurProps: {
        children: 'children',
        label: 'title',
        value: 'id',
        checkStrictly: true,
      },
      listRecursive: [{
        id: '0',
        parent: 0,
        title: 'Is parent',
      }],
      temp: Object.assign({}, defaultForm),
      rules: {
        sort: [
          {
            type: 'number',
            message: 'sort must be a number',
            trigger: 'blur',
          },
        ],
        parent: [
          {
            required: true,
            message: 'parent is required',
            trigger: 'change',
          },
        ],
        status: [
          {
            required: true,
            message: 'status is required',
            trigger: 'change',
          },
        ],
        descriptions: [],
      },
      inputTagsVisible: false,
      dynamicTags: '',
    };
  },
  created() {
    this.fetchLanguages();
    if (this.isEdit){
      const id = this.$route.params && this.$route.params.id;
      this.getRecursive(id);
      this.fetchCategory(id);
    } else {
      this.getRecursive();
    }
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
      const keyLang = Object.keys(this.languages)[this.active];
      this.$refs['dataForm'].validateField('descriptions.' + keyLang + '.title', this._checkValidate);
    },
    _checkValidate(msg){
      if (!msg) {
        const langNum = _.size(this.languages) - 1;
        if (++this.active == langNum){
          this.action = true;
        }
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
          that.fileUrl = data.image + '&w=644';
          that.temp.image = data.image;
          that.temp.parent = data.parent;
          that.temp.alias = data.alias;
          that.temp.sort = data.sort;
          that.temp.top = String(data.top);
          that.temp.status = String(data.status);
          that.temp.id = data.id;
        })
        .catch(err => {
          console.log(err);
        });
    },
    fetchLanguages() {
      fetchLanguagesActive()
        .then(data => {
          var that = this;
          Object.keys(data.data).forEach(function(key, index) {
            that.$set(that.temp.descriptions, key, {});
            that.$set(that.temp.descriptions[key], 'description', '');
            that.$set(that.temp.descriptions[key], 'title', '');
            that.$set(that.temp.descriptions[key], 'keyword', []);

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
          this.languages = data.data;
          this.languages['last'] = 'Done';
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    handlerGeturl(data) {
      if (data) {
        this.fileUrl = data + '&w=644';
        this.temp.image = data;
        this.dialogStorageClose();
      }
    },
    async getRecursive(id){
      const { data } = await categoryResource.getRecursive(id);
      data.unshift(this.listRecursive[0]);
      this.listRecursive = data;
    },
    createData() {
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
          categoryResource.store(form_data).then((res) => {
            if (res) {
              loading.close();
              this.$message({
                type: 'success',
                message: 'Create successfully',
              });
              const view = this.$router.resolve({ name: 'CategoryCreate' }).route;
              this.$store.dispatch('tagsView/delCachedView', view);
              this.reloadRedirectToList('CategoryList');
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
      this.temp.image = '';
      this.componentStorage = '';
      this.fileUrl = '';
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
</style>
