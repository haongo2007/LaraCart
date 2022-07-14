<template>
  <product-detail v-if="!loading" :is-edit="true" :data-temp="temp" :data-languages="languages" :data-step-content="stepContent" :data-component-info="componentInfo" :data-rules="rules" :data-product="product" />
</template>

<script>
import ProductDetail from './components/ProductDetail';
import LanguageResource from '@/api/languages';
import ProductResource from '@/api/product';

const productResource = new ProductResource();
const languageResource = new LanguageResource();
export default {
  name: 'ProductEditSingle',
  components: { ProductDetail },
  data: function() {
    return {
      loading: true,
      spiner: null,
      languages: {},
      componentInfo: {},
      stepContent: {},
      product: {},
      temp: {
        id: 0,
        kind: 0,
        store_id: 0,
        descriptions: {},
      },
      rules: {
        descriptions: [],
      },
    };
  },
  created(){
    this.spiner = this.$loading({
      target: '.app-main',
    });
    const id = this.$route.params && this.$route.params.id;
    productResource.get(id).then(({ data } = response) => {
      this.temp.id = data.id;
      this.temp.kind = data.kind;
      this.temp.store_id = data.store_id;
      this.product = data;

      this.fetchLanguages(data.store_id);
    }).catch(err => {
      console.log(err);
    });
  },
  methods: {
    fetchLanguages(id) {
      languageResource.fetchLanguagesActive(id)
        .then(data => {
          this.languages = data.data;
          this.setTemp();
        })
        .catch(err => {
          console.log(err);
        });
    },
    setTemp(){
      var that = this;
      const data = Object.assign({}, this.languages);
      Object.keys(data).forEach(function(key, index) {
        that.$set(that.temp.descriptions, key, {});
        if (that.product.descriptions.hasOwnProperty(key)) {
          that.$set(that.temp.descriptions[key], 'description', that.product.descriptions[key].description);
          that.$set(that.temp.descriptions[key], 'title', that.product.descriptions[key].name);
          that.$set(that.temp.descriptions[key], 'keyword', that.product.descriptions[key].keyword !== '' ? that.product.descriptions[key].keyword.split(',') : []);
          that.$set(that.temp.descriptions[key], 'content', that.product.descriptions[key].content);
        }

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
        title: 'general',
        icon: 'el-icon-view',
      };
      this.$set(this.componentInfo, 'info-general', '');

      this.stepContent['info-promotion'] = {
        title: 'promotion',
        icon: 'el-icon-s-promotion',
      };
      this.$set(this.componentInfo, 'info-promotion', '');

      this.stepContent['info-attribute'] = {
        title: 'attribute',
        icon: 'el-icon-news',
      };
      this.$set(this.componentInfo, 'info-attribute', '');

      this.stepContent['info-property'] = {
        title: 'property',
        icon: 'el-icon-menu',
      };
      this.$set(this.componentInfo, 'info-property', '');

      this.stepContent['info-thumbnail'] = {
        title: 'thumbnail',
        icon: 'el-icon-picture-outline',
      };
      this.$set(this.componentInfo, 'info-thumbnail', '');
      this.loading = false;
      this.spiner.close();
    },
  },
};
</script>
