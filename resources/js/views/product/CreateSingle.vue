<template>
  <product-detail :is-edit="false" :data-temp="temp" :data-languages="languages" :data-step-content="stepContent" :data-component-info="componentInfo" :data-rules="rules" v-if="!loading"/>
</template>

<script>
import ProductDetail from './components/ProductDetail';
import LanguageResource from '@/api/languages';

const languageResource = new LanguageResource();
export default {
  name: 'ProductCreateSingle',
  components: { ProductDetail },
  data(){
		return {
			loading:true,
    	languages: {},
      componentInfo: {},
      stepContent: {},
  		temp: {
			  id: 0,
			  kind: 0,
			  descriptions: {},
			},
      rules: {
        descriptions: [],
      },
  	}
  },
  created(){  	
    this.fetchLanguages();
  },
  methods:{
    fetchLanguages() {
      languageResource.fetchLanguagesActive()
        .then(data => {
          this.languages = Object.assign({}, data.data);
          this.setTemp(); 
        })
        .catch(err => {
          console.log(err);
        });
    },
	  setTemp(){
      var that = this;
      let data = Object.assign({}, this.languages);
      Object.keys(data).forEach(function(key, index) {

        that.$set(that.temp.descriptions, key, {});

        that.$set(that.temp.descriptions[key], 'description', '');
        that.$set(that.temp.descriptions[key], 'title', '');
        that.$set(that.temp.descriptions[key], 'keyword', '');
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

        that.$set(that.rules.descriptions[key],'content', [
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
