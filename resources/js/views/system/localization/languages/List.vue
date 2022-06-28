<template>
  <div class="app-container">
    <div class="filter-container">  
        <el-button type="primary" icon="el-icon-plus" class="filter-item" @click="createForm()" />
    </div>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%"
      border
    >

      <el-table-column :label="$t('table.id')" prop="id" sortable align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Store" min-width="150" v-if="checkOnlyStore">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop" />
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.name')" min-width="120px">
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.code')" min-width="150px">
        <template slot-scope="scope">
          {{ scope.row.code }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.flag')" min-width="100px">
        <template slot-scope="scope">
          <el-image :src="scope.row.icon">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.rtl')" min-width="100px" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.rtl | statusFilter">
            {{ row.rtl | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sort')" min-width="120px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.sort }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" min-width="100px" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter('name') }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column fixed="right" :label="$t('table.actions')" align="center" min-width="250px" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" icon="el-icon-edit" @click="updateForm(row)" />
          <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="$t('form.'+dialogStatus)" :visible.sync="dialogFormVisible" :before-close="handleReset" class="dialog-custom">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px" style="width: 75%; margin:0 auto;">

        <el-form-item :label="$t('form.name')" prop="name">
          <el-input v-model="temp.name" />
        </el-form-item>

        <el-form-item :label="$t('form.store')" prop="store_id" v-if="checkOnlyStore">
          <el-radio v-for="(item,index) in storeList" :key="index" v-model="temp.store_id" :label="index" @change="handleChangeStore(index)">
            {{ item.descriptions_current_lang[0].title }}
          </el-radio>
        </el-form-item>

        <el-form-item :label="$t('form.select_country')" prop="country">
          <el-select v-model="temp.code" placeholder="Select" prop="country" filterable style="width: 80%;" @change="handleSelect">
            <el-option
              v-for="(item,index) in CountriesOptions"
              :key="index"
              :label="item"
              :value="index"
            />
          </el-select>
          <el-image v-if="temp.icon" style="width: auto;height: 36px;vertical-align: middle;" :src="temp.icon" fit="contain"></el-image>
        </el-form-item>

        <el-form-item :label="$t('form.status')" prop="status">
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

        <el-form-item :label="$t('form.rtl')" prop="status">
          <el-tooltip :content="'Switch value: ' + temp.rtl" placement="top">
            <el-switch
              v-model="temp.rtl"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0"
            />
          </el-tooltip>
        </el-form-item>

        <el-form-item :label="$t('form.sort')" prop="sort">
          <el-input-number v-model.number="temp.sort" :min="1" />
        </el-form-item>
        
        <el-form-item>
          <el-button class="pull-right" :loading="loading" type="primary" @click="dialogStatus==='create'?createData():updateData()">
            {{ $t('form.confirm') }}
          </el-button>
        </el-form-item>
      </el-form>
    </el-dialog>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="paginationInit" />
  </div>
</template>

<script>
import { statusFilter } from '@/filters';
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination';
import EventBus from '@/components/FileManager/eventBus';
import { checkOnlyStore } from '@/utils';
import LanguageResource from '@/api/languages';
import CountryResource from '@/api/country';

const languageResource = new LanguageResource();
const countryResource = new CountryResource();

const defaultTemp = {
  id:0,
  name:'',
  code:'',
  status:'0',
  rtl:'0',
  sort:'0',
  store_id:'',
  icon:'',
}

export default {
  name: 'LanguagesList',
  components: { Pagination },
  data() {
    return {
      dialogStatus:'create',
      dialogFormVisible:false,
      list: [],
      CountriesOptions: [],
      total: 0,
      loading: true,
      temp:Object.assign({},defaultTemp),
      rules:{},
      listQuery: {
        page: 1,
        limit: 20,
        status: [],
        sort: 'id__desc',
        name: '',
      },
    };
  },
  watch:{
    async dialogFormVisible(newVal){
      if (!this.CountriesOptions.length) {
        let {data} = await countryResource.list();
        this.CountriesOptions = data;
      }
    }
  },
  computed: {
    checkOnlyStore,
    storeList(){
      const storeList = this.$store.state.user.storeList;
      return storeList;
    },
  },
  created() {
    this.getList();
  },
  methods: {
    handleChangeStore(index){
      this.temp.store = this.storeList[index];
    },
    paginationInit(data){
      this.loading = true;
      this.listQuery.page = data.page;
      this.listQuery.limit = data.limit;
    },
    async getList() {
      const data = await languageResource.list(this.dataQuery);
      this.list = data.data;
      this.total = data.meta.total;
      this.loading = false;
    },
    async createForm(){
      if (this.checkOnlyStore == false) {
        this.temp.store_id = this.$store.state.user.currentStore;
      }
      this.dialogFormVisible = true;
    },
    async updateForm(row){
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.temp.store_id = String(row.store.id);
      this.temp.id = row.id;
      this.temp.code = row.code.toUpperCase();
      this.temp.status = String(row.status);
      this.temp.rtl = String(row.rtl);
      this.temp.sort = row.sort;
      this.temp.name = row.name;
      this.temp.icon = await countryResource.getFlags(row.code);
      this.handleChangeStore(this.temp.store_id);
    },
    async handleSelect(item){
      let data = await countryResource.getFlags(item.toLowerCase());
      this.temp.icon = data;
    },
    createData(){
      this.loading = true;
      const loading = this.$loading({
        target: '.el-form',
      });
      languageResource.store(this.temp).then((res) => {    
        if (res.success) {
          this.temp.id = res.data.id;
          this.list = [this.temp,...this.list];
          this.$message({
            type: 'success',
            message: 'Create successfully',
          });
        }
        this.loading = false;    
        loading.close();
        this.dialogFormVisible = false;
      }).catch(() => {
        this.loading = false;    
        loading.close();
      })
    },
    updateData(){
      this.loading = true;
      const loading = this.$loading({
        target: '.el-form',
      });
      languageResource.update(this.temp.id,this.temp).then((res) => {
        if (res.success) {
          const index = this.list.findIndex((item) => item.id == this.temp.id);
          if (index > -1) {
            this.$set(this.list,index,this.temp);
          }
          this.$message({
            type: 'success',
            message: 'Update successfully',
          });
        }
        this.dialogFormVisible = false;
        this.loading = false; 
        loading.close();
      }).catch(() => {
        this.dialogFormVisible = false;
        this.loading = false; 
        loading.close();
      })
    },
    handleDeleting(row){
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        languageResource.destroy(row.id).then((res) => {
          if (res) {
            const index = this.list.indexOf(row);
            this.list.splice(index, 1);
            this.$message({
              type: 'success',
              message: 'Delete successfully',
            });
          }
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleReset(done){
      this.dialogStatus = 'create';
      this.temp = Object.assign({},defaultTemp);
      done();
    }
  },
};
</script>
<style>
  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
