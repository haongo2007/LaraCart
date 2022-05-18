<template>
  <div>
    <category-detail v-if="!loading && !confirmStoreDialog" :is-edit="false" :data-temp="temp" :data-rules="rules" :data-languages="languages" />
    <el-dialog
      :show-close="false"
      title="Please Choose store you want add category"
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
import CategoryDetail from './components/CategoryDetail';
import LanguageResource from '@/api/languages';
import Cookies from 'js-cookie';

const languageResource = new LanguageResource();

export default {
  name: 'CategoryCreate',
  components: { CategoryDetail },
  data() {
    return {
    	loading: true,
    	languages: [],
      temp: {
        store_id: 0,
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
      confirmStoreDialog: false,
    };
  },
  computed: {
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  created() {
    let store_ck = Cookies.get('store');
    if (store_ck) {
      store_ck = JSON.parse(store_ck);
    }
    if (store_ck && store_ck.length == 1) {
      this.temp.store_id = store_ck[0];
    }else{
      this.confirmStoreDialog = true;
      return false;
    }
    this.fetchLanguages(this.temp.store_id);
  },
  methods: {
    fetchLanguages(id) {
      languageResource.fetchLanguagesActive(id)
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
    confirmChooseStore(){
      this.fetchLanguages(this.temp.store_id);
      this.confirmStoreDialog = false;
    },
    handleConfirm(done){
      if (this.temp.store_id != 0) {
        done();
      }
    },
  },
};
</script>

