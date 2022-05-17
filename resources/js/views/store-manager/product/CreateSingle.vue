<template>
  <div>
    <product-detail
      v-show="!loading"
      :is-edit="false"
      :data-temp="temp"
      :data-languages="languages"
      :data-step-content="stepContent"
      :data-component-info="componentInfo"
      :data-rules="rules"
    />
    <el-dialog
      :show-close="false"
      title="Please Choose store you want add product"
      :visible.sync="confirmStoreDialog"
      :before-close="handleConfirm"
      width="30%"
    >
      <div>
        <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index">
          {{ item.descriptions_current_lang[0].title }}
        </el-radio>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" :disabled="temp.store_id == 0" @click="confirmChooseStore">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import ProductDetail from './components/ProductDetail';
import LanguageResource from '@/api/languages';
import Cookies from 'js-cookie';

const languageResource = new LanguageResource();
export default {
  name: 'ProductCreateSingle',
  components: { ProductDetail },
  data(){
    return {
      confirmStoreDialog: false,
      loading: true,
      languages: {},
      componentInfo: {},
      stepContent: {},
      temp: {
        store_id: 0,
        id: 0,
        kind: 0,
        descriptions: {},
      },
      rules: {
        descriptions: [],
      },
    };
  },
  computed: {
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  created(){
    let store_ck = Cookies.get('store');
    if (store_ck) {
      store_ck = JSON.parse(store_ck);
    }
    if (!store_ck || store_ck.length !== 1 || this.temp.store_id != 0) {
      this.confirmStoreDialog = true;
      return false;
    } else {
      this.temp.store_id = store_ck[0];
    }
    this.fetchLanguages(this.temp.store_id);
  },
  methods: {
    confirmChooseStore(){
      this.fetchLanguages(this.temp.store_id);
      this.confirmStoreDialog = false;
    },
    handleConfirm(done){
      if (this.temp.store_id != 0) {
        done();
      }
    },
    fetchLanguages(store_id) {
      const loading = this.$loading({
        target: '.app-main',
      });
      languageResource.fetchLanguagesActive(store_id)
        .then(data => {
          this.languages = Object.assign({}, data.data);
          this.setTemp();
        })
        .catch(err => {
          console.log(err);
        });
      loading.close();
      this.loading = false;
    },
    setTemp(){
      var that = this;
      const data = Object.assign({}, this.languages);
      Object.keys(data).forEach(function(key, index) {
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
              message: 'Name ' + data[key] + ' is required',
              trigger: 'blur',
            },
            {
              min: 3,
              message: 'Length min 3',
              trigger: 'blur',
            },
          ]
        );

        that.$set(that.rules.descriptions[key], 'content', [
          {
            required: true,
            message: 'Content ' + data[key] + ' is required',
            trigger: 'blur',
          },
        ]);
      });
      // /// create step form
      this.stepContent = data;
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
    },
  },
};
</script>