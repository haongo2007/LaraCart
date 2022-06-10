<template>
  <blog-detail v-if="!loading" :is-edit="true" :data-temp="temp" :data-rules="rules" :data-languages="languages" />
</template>

<script>

import BlogDetail from './components/BlogDetail';
import LanguageResource from '@/api/languages';
import BlogsResource from '@/api/blogs';

const blogsResource = new BlogsResource();
const languageResource = new LanguageResource();

export default {
  name: 'BlogEdit',
  components: { BlogDetail },
  data() {
    return {
    	loading: true,
    	languages: [],
      temp: {
        store_id: 0,
			  id: '',
			  alias: '',
			  sort: '',
			  status: '1',
			  image: '',
			  descriptions: {
			  },
      },
      rules: {
        sort: [
          {
            type: 'number',
            message: 'sort must be a number',
            trigger: 'blur',
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
      blogsResource.get(id)
        .then(({ data } = response) => {
          this.temp.image = data.image;
          this.temp.alias = data.alias;
          this.temp.sort = data.sort;
          this.temp.status = String(data.status);
          this.temp.id = data.id;
          this.temp.store_id = data.store_id;
          const desc = data.descriptions;
          this.fetchLanguages(desc);
          loading.close();
        })
        .catch(err => {
          console.log(err);
        });
    },
    fetchLanguages(desc) {
      languageResource.fetchLanguagesActive(this.temp.store_id)
        .then(data => {
          var that = this;
          Object.keys(data.data).forEach(function(key, index) {
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
            let findIndex = desc.findIndex((item) => item.lang == key);
            if (desc[findIndex].lang == key) {
              that.temp.descriptions[key].title = desc[findIndex].title;
              that.temp.descriptions[key].description = desc[findIndex].description;
              that.temp.descriptions[key].keyword = (desc[findIndex].keyword != '' ? desc[findIndex].keyword.split(',') : []);
              that.temp.descriptions[key].content = desc[findIndex].content;
            }
          });
          this.languages = data.data;
          this.languages['last'] = 'done';
          this.loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
  },
};
</script>

