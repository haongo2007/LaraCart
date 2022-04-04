<template>
  <category-detail v-if="!loading" :is-edit="true" :data-temp="temp" :data-rules="rules" :data-languages="languages" />
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
    	loading: true,
    	languages: [],
      temp: {
			  id: '',
			  alias: '',
			  sort: '',
			  top: '1',
			  parent: '0',
			  status: '1',
			  image: '',
			  descriptions: {
			  },
			  fileUrl: '',
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
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchCategory(id);
  },
  methods: {
    fetchCategory(id) {
      const loading = this.$loading({
        target: '.app-main',
      });
      categoryResource.get(id)
        .then(({ data } = response) => {
          this.temp.fileUrl = data.image + '&w=644';
          this.temp.image = data.image;
          this.temp.parent = data.parent ? data.parent : String(data.parent);
          this.temp.alias = data.alias;
          this.temp.sort = data.sort;
          this.temp.top = String(data.top);
          this.temp.status = String(data.status);
          this.temp.id = data.id;
          // const codes = [];
          // for (var i = 0; i < desc.length; i++) {
          //   codes.push(data.descriptions[i].lang);
          // }

          // get current language
          // languageResource.list({ code: codes }).then(({ data } = response) => {
          //   const newLang = [];
          //   for (var i = 0; i < data.length; i++) {
          //     newLang[data[i].code] = data[i].name;
          //   }
          //   this.languages = Object.assign({}, newLang);
          //   // set temp follow current language
          // });
          const desc = data.descriptions;
          this.fetchLanguages(desc);
          loading.close();
        })
        .catch(err => {
          console.log(err);
        });
    },
    fetchLanguages(desc) {
      languageResource.fetchLanguagesActive()
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
            if (desc.hasOwnProperty(index)) {
              if (desc[index].lang == key) {
                that.temp.descriptions[key].title = desc[index].title;
                that.temp.descriptions[key].description = desc[index].description;
                that.temp.descriptions[key].keyword = (desc[index].keyword != '' ? desc[index].keyword.split(',') : []);
              }
            }
          });
          this.languages = data.data;
          this.languages['last'] = 'Done';
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
  },
};
</script>

