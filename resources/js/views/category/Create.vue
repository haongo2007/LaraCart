<template>
  <category-detail v-if="!loading" :is-edit="false" :data-temp="temp" :data-rules="rules" :data-languages="languages" />
</template>

<script>
import CategoryDetail from './components/CategoryDetail';
import LanguageResource from '@/api/languages';

const languageResource = new LanguageResource();

export default {
  name: 'CategoryCreate',
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
    this.fetchLanguages();
  },
  methods: {
    fetchLanguages() {
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

