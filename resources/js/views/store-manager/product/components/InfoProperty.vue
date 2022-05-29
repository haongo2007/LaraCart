<template>
  <div>
    <el-form ref="dataGeneralForm" :model="temp" class="form-container" label-width="150px">
      <el-row class="el-main-form">
        <el-col :span="24">
          <el-form-item :label="$t('table.weight_unit')" prop="weight_unit">
            <el-autocomplete
              v-model="temp.weight_class.description"
              style="width: 100%"
              value-key="description"
              class="inline-input"
              :fetch-suggestions="querySearchWeightAsync"
              placeholder="Please Input"
              @select="handleSelectWeight"
            />
          </el-form-item>

          <el-form-item :label="$t('table.weight')" prop="weight">
            <el-input-number v-model="temp.weight" style="width: 100%" :controls="false" />
          </el-form-item>

          <el-form-item :label="$t('table.length_unit')" prop="length_unit">
            <el-autocomplete
              v-model="temp.length_class.description"
              style="width: 100%"
              value-key="description"
              class="inline-input"
              :fetch-suggestions="querySearchLengthAsync"
              placeholder="Please Input"
              @select="handleSelectLength"
            />
          </el-form-item>

          <el-form-item :label="$t('table.length')" prop="length">
            <el-input-number v-model="temp.length" style="width: 100%" :controls="false" />
          </el-form-item>

          <el-form-item :label="$t('table.height')" prop="height">
            <el-input-number v-model="temp.height" style="width: 100%" :controls="false" />
          </el-form-item>

          <el-form-item :label="$t('table.width')" prop="width">
            <el-input-number v-model="temp.width" style="width: 100%" :controls="false" />
          </el-form-item>

        </el-col>
      </el-row>
    </el-form>
    <el-row>
      <el-button-group class="pull-right">
        <el-button type="warning" icon="el-icon-arrow-left" @click="backStep">
          Previous
        </el-button>
        <el-button type="primary" icon="el-icon-arrow-right" @click="nextStep">
          Next
        </el-button>
      </el-button-group>
    </el-row>
  </div>
</template>

<script>
import WeightResource from '@/api/weight';
import LengthResource from '@/api/length';

const weightResource = new WeightResource();
const lengthResource = new LengthResource();
export default {
  name: 'InfoProperty',
  props: ['dataActive', 'dataProduct'],
  data() {
    return {
      temp: {
        weight_class: {
          description: '',
          value: '',
        },
        weight: '',
        length_class: {
          description: '',
          value: '',
        },
        length: '',
        height: '',
        width: '',
      },
      weight_units: [],
      length_units: [],
    };
  },
  created() {
    if (Object.keys(this.dataProduct).length > 0) {
      if (this.dataProduct.weight){
        this.temp.weight = this.dataProduct.weight;
      }
      if (this.dataProduct.width){
        this.temp.width = this.dataProduct.width;
      }
      if (this.dataProduct.height){
        this.temp.height = this.dataProduct.height;
      }
      if (this.dataProduct.length){
        this.temp.length = this.dataProduct.length;
      }
      if (this.dataProduct.weight_class){
        this.querySearchWeightAsync();
      }
      if (this.dataProduct.length_class){
        this.querySearchLengthAsync();
      }
    }
  },
  methods: {
    backStep() {
      const active = this.dataActive - 1;
      this.$emit('handleProcessActive', active);
    },
    nextStep() {
      const active = this.dataActive + 1;
      this.$emit('handleProcessActive', active);
      this.$emit('handleProcessTemp', this.temp);
    },
    cbGetWeightCl(res){
      const selectedWeightUnit = this.weight_units.filter(unit => unit.name == this.dataProduct.weight_class);
      this.temp.weight_class.value = selectedWeightUnit[0].name;
      this.temp.weight_class.description = selectedWeightUnit[0].description;
    },
    cbGetLengthCl(res){
      const selectedLengthClass = this.length_units.filter(unit => unit.name == this.dataProduct.length_class);
      this.temp.length_class.value = selectedLengthClass[0].name;
      this.temp.length_class.description = selectedLengthClass[0].description;
    },
    querySearchWeightAsync(queryString, cb){
      var weight_units = this.weight_units;
      var results = queryString ? weight_units.filter(this.createFilter(queryString)) : weight_units;

      if (results.length == 0) {
        weightResource.list({ keyword: queryString }).then(response => {
          this.weight_units = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetWeightCl(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        if (cb) {
          cb(results);
        }
      }
    },
    querySearchLengthAsync(queryString, cb){
      var length_units = this.length_units;
      var results = queryString ? length_units.filter(this.createFilter(queryString)) : length_units;

      if (results.length == 0) {
        lengthResource.list({ keyword: queryString }).then(response => {
          this.length_units = response.data;
          results = response.data;
          if (cb){
            cb(results);
          } else {
            this.cbGetLengthCl(results);
          }
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        if (cb) {
          cb(results);
        }
      }
    },
    createFilter(queryString) {
      return (data) => {
        return (data.description.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    handleSelectWeight(item){
      this.temp.weight_class.value = item.name;
    },
    handleSelectLength(item){
      this.temp.length_class.value = item.name;
    },
  },
};
</script>
