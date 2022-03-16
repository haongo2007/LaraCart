<template>
	<category-detail :is-edit="true" :data-temp="temp" :data-rules="rules" :data-languages="languages" v-if="!loading"/>
</template>

<script>

import CategoryDetail from './components/CategoryDetail';
import CategoryResource from '@/api/category';
import LanguageResource from '@/api/languages';

const languageResource = new LanguageResource();
const categoryResource = new CategoryResource();

export default {
  name: 'CategoryEdit',
  components: { CategoryDetail },
  data() {
    return {
    	loading:true,
    	languages:[],
			temp : {
			  id: '',
			  alias: '',
			  sort: '',
			  top: '1',
			  parent: '0',
			  status: '1',
			  image: '',
			  descriptions: {
			  },
			  fileUrl:'',
			},
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
    }
	},
	created() {		
    const id = this.$route.params && this.$route.params.id;
    this.fetchCategory(id);
  },
  methods:{  	
    fetchCategory(id) {
      const loading = this.$loading({
        target: '.el-row',
      });
      categoryResource.get(id)
        .then(({ data } = response) => {
          const desc = data.descriptions;
          
          this.temp.fileUrl = data.image + '&w=644';
          this.temp.image = data.image;
          this.temp.parent = data.parent ? data.parent : String(data.parent);
          this.temp.alias = data.alias;
          this.temp.sort = data.sort;
          this.temp.top = String(data.top);
          this.temp.status = String(data.status);
          this.temp.id = data.id;

          const codes = [];
          for (var i = 0; i < desc.length; i++) {
            codes.push(data.descriptions[i].lang);
          }

          // get current language
          languageResource.list({code:codes}).then(({data} = response) => {
            const newLang = [];
            for (var i = 0; i < data.length; i++) {
              newLang[data[i].code] = data[i].name;
            }
            this.languages = Object.assign({},newLang);
            this.languages['last'] = 'Done';
            // set temp follow current language
            this.fetchLanguages(desc);
            loading.close();
          });
        })
        .catch(err => {
          console.log(err);
        });
    },
    fetchLanguages(desc) {
      const that = this;
      desc.forEach(function(v, i) {
        that.$set(that.temp.descriptions, v.lang, {});
        that.$set(that.temp.descriptions[v.lang], 'description', '');
        that.$set(that.temp.descriptions[v.lang], 'title', '');
        that.$set(that.temp.descriptions[v.lang], 'keyword', []);

        that.$set(that.rules.descriptions, v.lang, []);

        that.$set(that.rules.descriptions[v.lang], 'title',
          [
            {
              required: true,
              message: 'Name ' + that.languages[v.lang] + ' is required',
              trigger: 'blur',
            },
            {
              min: 3,
              message: 'Length min 3',
              trigger: 'blur',
            },
          ]
        );

        that.temp.descriptions[v.lang].title = v.title;
        that.temp.descriptions[v.lang].description = v.description;
        that.temp.descriptions[v.lang].keyword = v.keyword != '' ? v.keyword.split(',') : [];
      });
      this.loading = false;
    },
  }
};
</script>

