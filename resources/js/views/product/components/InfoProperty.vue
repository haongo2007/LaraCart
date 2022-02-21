<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form-item :label="$t('table.weight_unit')" prop="weight_unit">
          <el-autocomplete
            v-model="temp.weight_unit"
            style="width: 100%"
            value-key="description"
            class="inline-input"
            :fetch-suggestions="querySearchWeightAsync"
            placeholder="Please Input"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('table.weight')" prop="weight">
          <el-input-number v-model="temp.weight" style="width: 100%" :controls="false" />
        </el-form-item>

        <el-form-item :label="$t('table.length_unit')" prop="length_unit">
          <el-autocomplete
            v-model="temp.length_unit"
            style="width: 100%"
            value-key="description"
            class="inline-input"
            :fetch-suggestions="querySearchLengthAsync"
            placeholder="Please Input"
            @select="handleSelect"
          />
        </el-form-item>

        <el-form-item :label="$t('table.longs')" prop="longs">
          <el-input-number v-model="temp.longs" style="width: 100%" :controls="false" />
        </el-form-item>

        <el-form-item :label="$t('table.height')" prop="height">
          <el-input-number v-model="temp.height" style="width: 100%" :controls="false" />
        </el-form-item>

        <el-form-item :label="$t('table.width')" prop="width">
          <el-input-number v-model="temp.width" style="width: 100%" :controls="false" />
        </el-form-item>

      </el-col>
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
  props: ['dataActive'],
  data() {
    return {
      temp: {
        weight_unit: '',
        weight: '',
        length_unit: '',
        length: '',
        longs: '',
        height: '',
        width: '',
      },
      weight_units: [],
      length_units: [],
    };
  },
  created() {

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
    querySearchWeightAsync(queryString, cb){
      var weight_units = this.weight_units;
      var results = queryString ? weight_units.filter(this.createFilter(queryString)) : weight_units;

      if (results.length == 0) {
        weightResource.list({ keyword: queryString }).then(response => {
          this.weight_units = [...this.weight_units, ...response.data];
          results = response.data;
          cb(results);
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        cb(results);
      }
    },
    querySearchLengthAsync(queryString, cb){
      var length_units = this.length_units;
      var results = queryString ? length_units.filter(this.createFilter(queryString)) : length_units;

      if (results.length == 0) {
        lengthResource.list({ keyword: queryString }).then(response => {
          this.length_units = [...this.length_units, ...response.data];
          results = response.data;
          cb(results);
        })
          .catch(err => {
            console.log(err);
          });
      } else {
        cb(results);
      }
    },
    createFilter(queryString) {
      return (data) => {
        return (data.description.toLowerCase().includes(queryString.toLowerCase()) === true);
      };
    },
    handleSelect(item){

    },
  },
};
</script>
