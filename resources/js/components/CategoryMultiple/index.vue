<template>
  <div >
    <div v-if="isMultiple" class="category-list">
      <div style="margin: 0px 20px 20px 0px;" v-for="(value,key) in categoryMultiple" :key="key">
        <el-select
          v-model="categoryMultipleValue[key]"
          multiple
          filterable
          remote
          reserve-keyword
          value-key="name"
          placeholder="Please enter a keyword"
          @change="handleSelectCategoryMultiple($event, key)"
          :remote-method="remoteMethod"
          :loading="loading">
          <el-option
            v-for="item in categoryMultiple[key]"
            :key="item.id"
            :label="item.name"
            :value="item">
          </el-option>
        </el-select>
      </div>
    </div>
    <div v-else class="category-list">
      <div style="margin: 0px 20px 20px 0px;" v-for="(item,index) in category" :key="index">
        <el-autocomplete
          :debounce="300"
          v-model="item.name"
          :fetch-suggestions="querySearchAsync"
          @focus="checkParentFocus(index)"
          @blur="checkToReName(index)"
          placeholder="Please choose children"
          @select="handleSelectCategory"
          value-key="name"/>
          <i style="margin-left: 10px;" v-if="index == categoryLevel" class="el-icon-arrow-right"></i>
      </div>
    </div>
  </div>
</template>

<script>
import EventBus from '@/components/FileManager/eventBus';
import CategoryResource from '@/api/category';
const categoryResource = new CategoryResource();

export default {
  name: 'CategoryMultiple',
  props: {
    storeId: {
      type: Number,
      default: 1,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
    isMultiple: {
      type: Boolean,
      default: false,
    },
    dataTempMultiple: {
      type: Array,
    },
    dataTemp: {
      type: Object,
      default: () => ({
        parent_list:0,
        parent:0,
        id:0,
      })
    },
  },
  data() {
    return {
      categoryMultipleValue: [],//value
      categoryMultipleTotal: [],//option
      categoryMultiple: [],//option
      loading: false,
      categoryLevel:0,
      category:[
        {
          'name':null,
          'id':null,
          'parent':null,
        }
      ],
      categories:[
        [{
          'name':'Is parent',
          'id':0,
          'parent':0,
        }]
      ],
    };
  },
  created() {
    if (this.isMultiple) {
      this.getCategoryMultiple();

      if(this.isEdit){
        let ids = this.dataTempMultiple.join(',');
        if (ids.length == 0) {
          return false;
        }
        let params = {
          id_list : ids
        }
        this.getListCategory(params).then((data) => {
          this.retrieveCategory(0,data);
        });
      }
    }else{
      this.getCategory();
    }
  },
  methods: {
    retrieveCategory(level = 0 ,data,parent = 0){
      data.forEach((item,index) => {
        if (item.parent_id == parent) {
          if(!this.categoryMultiple.hasOwnProperty(level)){
            this.$set(this.categoryMultiple,level,[]);
          }else{
            for(let cat in this.categoryMultiple[level]){
              if (this.categoryMultiple[level][cat].id == item.id) {
                this.categoryMultiple[level].splice(cat,1);
              }
            }
          }         
          this.categoryMultiple[level].push(item);
          if(!this.categoryMultipleValue.hasOwnProperty(level)){
            this.$set(this.categoryMultipleValue,level,[]);
          }
          this.categoryMultipleValue[level].push(item);
          this.retrieveCategory(level+1,data,item.id);
        }
      })
    },
    async getListCategory(params){
      params['limit'] = 10;
      params['store_id'] = this.storeId;
      let { data } = await categoryResource.list(params);
      return data;
    },
    /// multiple for product menu
    handleRemoveCategoryMultipleChild(key,oldValue = null,next = false){
      let that = this;
      let del = [];
      let nextdel = [];
      if (next == true) {
        del = this.categoryMultipleValue[key].filter(function(obj) {
            return oldValue.some(function(obj2) {
                return obj.parent_id === obj2.id;
            });
        });
        del = del.map((item)=>{
          return item.id;
        });
      }else{
        del = this.categoryMultipleValue[key].filter(function(obj) {
            return !oldValue.some(function(obj2) {
                return obj.id === obj2.id;
            });
        });
        del = del.map((item)=>{
          return item.id;
        });
      }
      this.categoryMultipleValue[key].forEach((child,index) => {
        if (del.includes(child.id)) {
          nextdel.push(that.categoryMultipleValue[key][index]);
          delete that.categoryMultipleValue[key][index];
        }
      })
      let pare = oldValue.map((item)=>{
        return item.id;
      });
      this.categoryMultiple[key] = this.categoryMultiple[key].filter( function(element, index) {
        return !pare.includes(element.parent_id);
      })
      if (this.categoryMultiple[key].length == 0) {
        this.categoryMultiple.splice(key,this.categoryMultiple.length)
      }
      if(this.categoryMultiple.hasOwnProperty(key + 1)){
        this.handleRemoveCategoryMultipleChild(key+1,nextdel,true);
      }
    },
    handleSelectCategoryMultipleChild(value,key,added){
      this.$set(this.categoryMultiple,key,value);
      if (!this.categoryMultipleValue.hasOwnProperty(key)) {
        this.$set(this.categoryMultipleValue,key,[]);
      }else{
        if (added !== true) {
          this.handleRemoveCategoryMultipleChild(key,value);
        }
      }  
    },
    async handleSelectCategoryMultiple(value,key){
      let that = this;
      let parent = value.map(function(val, i) {
        return val.id;
      })
      if (parent == '') {
        this.categoryMultipleValue.splice(key+1,this.categoryMultipleValue.length);
        this.categoryMultiple.splice(key+1,this.categoryMultiple.length);
        return;
      }
      let parents = parent.join(',');
      let params = {
        'parent_list':parents
      };
      let { data } = await categoryResource.list(params);
      let added = true;
      if (this.categoryMultipleTotal.hasOwnProperty(key)) {
        if (this.categoryMultipleTotal[key] > value.length) {
          added = false;
        }
      }
      this.$set(this.categoryMultipleTotal,key,value.length);
      if (data.length) {
        let next = key + 1;
        this.handleSelectCategoryMultipleChild(data,next,added);
      }else{
        this.categoryMultipleValue.splice(key+1,this.categoryMultipleValue.length);
        this.categoryMultiple.splice(key+1,this.categoryMultiple.length);
      }
      this.$emit('handleProcessCategory', this.categoryMultipleValue);
    },
    remoteMethod(query) {
      let key = this.categoryLevel;
      if (query !== '') {
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
          this.categoryMultiple[key] = this.categoryMultiple[key].filter(item => {
            return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1;
          });
        }, 200);
      } else {
        this.categoryMultiple[this.categoryLevel] = [];
      }
    },
    getCategoryMultiple(){
      let params = {
        'parent':0,
      };
      this.getListCategory(params).then((data) => {
        this.$set(this.categoryMultipleValue,this.categoryLevel,[]);
        this.$set(this.categoryMultiple,this.categoryLevel,data);
      });
    },
    /// single for categories menu
    getCategory(){
      let params = {};
      if (this.isEdit) {
        if (this.dataTemp.parent == 0) {
          this.$set(this.category,this.categoryLevel, {
            'name':'Is parent',
            'id':0,
            'parent':0,
          });
          params['parent'] = 0;
          this.getListCategory(params).then((data) => {
            this.categories[this.categoryLevel] = [...data,...this.categories[this.categoryLevel]];
          });
          return;
        }
        params['id_list'] = this.dataTemp.parent_list;
        
        this.getListCategory(params).then((data) => {
          let sort;
          let id = this.dataTemp.parent_list;
          if( String(id).indexOf(',') != -1 ){
            sort = id.split(',').filter(Boolean);
          }else{
            sort = [id];
          }
          data = sort.map((i) => data.find((j) => parseInt(j.id) === parseInt(i) )); // sort array

          for(let cate in data){
            if (!this.category.hasOwnProperty(cate)) {
              this.$set(this.category,cate,[]);
              this.$set(this.categories,cate,[]);
            }
            if (this.categories[cate].hasOwnProperty(cate)) {
              this.categories[cate].push({...data[cate]});
            }
            this.$set(this.category,cate,data[cate])
          }
        });
      }else{
        params['parent'] = 0;
        this.getListCategory(params).then((data) => {
          this.categories[this.categoryLevel] = [...data,...this.categories[this.categoryLevel]];
        });
      }
    },
    querySearchAsync(queryString, cb) {
      const categories = this.categories[this.categoryLevel];
      var results = queryString ? categories.filter(this.categoriesFilter(queryString)) : categories;

      // check if not exist search in server
      if (results.length == 0 || queryString == '') {
        let params = {
          'name':queryString,
          'except_id':this.dataTemp.id,
        };
        if (this.categoryLevel > 0) {
          params['parent'] = this.category[this.categoryLevel - 1].id
        }else{
          params['parent'] = 0;
        }
        let data = this.getListCategory(params).then((data) => {
          this.categories[this.categoryLevel] = data;
          results = data;
          if (this.categoryLevel == 0) {
            results.push({
              'name':'Is parent',
              'id':0,
              'parent':0,
            });
          }
        });
      }
      cb(results);
    },
    categoriesFilter(queryString) {
      return (categories) => {
        return (categories.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    checkParentFocus(level){
      this.categoryLevel = level;
    },
    checkToReName(level){
      if(this.category[level].name == ''){
        let that = this;
        this.categories[level].forEach( function(val, index) {
          if (val.id == that.category[level].id) {
            that.category[level].name = val.name;
          }
        });
      }
    },
    handleSelectCategory(item){

      if (parseInt(this.category[this.categoryLevel].id) == parseInt(item.id)) { // check itself return
        return;
      }

      let leng = this.category.length - 1;
      if (this.categoryLevel < leng) { // check change parent remove children
        this.category.splice(this.categoryLevel+1,leng);
        this.categories.splice(this.categoryLevel+1,leng);
      }

      this.$set(this.category,this.categoryLevel,{...item});// set for current
      this.$emit('handleProcessCategory', this.category);

      if (item.hasOwnProperty('hasChildren') && item.hasChildren == true) {
        this.categoryLevel++; // for child
        this.$set(this.category,this.categoryLevel,{
            'name':null,
            'id':null,
            'parent':null,
        });
        let params = {
          'parent':this.category[this.categoryLevel - 1].id,
          'except_id':this.dataTemp.id,
        };
        let data = this.getListCategory(params).then((data) => {
          if (!this.categories.hasOwnProperty(this.categoryLevel)) {
            this.$set(this.categories,this.categoryLevel,[]);
          }
          this.categories[this.categoryLevel] = data;
        });
      }
    }
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .category-list{
    display: flex;
    flex-wrap:wrap;
    justify-content: flex-start;
  }
</style>
