<template>
  <div>
    <el-form ref="dataGeneralForm" :model="temp" class="form-container" label-width="150px">
      <el-row class="el-main-form">
        <el-col :span="12">
          <el-form-item :label="$t('table.promotion_price')" prop="price_promotion">
            <el-input-number v-model="temp.price_promotion" style="width: 100%" :controls="false" />
          </el-form-item>
        </el-col>

        <el-col :span="12">
          <el-form-item :label="$t('table.sale_date')" prop="date_promotion">
            <el-date-picker
              v-model="date_promotion"
              style="width: 100%"
              type="datetimerange"
              align="right"
              unlink-panels
              range-separator="To"
              start-placeholder="Start date"
              end-placeholder="End date"
              :picker-options="pickerOptions"
            />
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
    </el-row>.
  </div>
</template>

<script>
import { parseTime } from '@/filters';
export default {
  name: 'InfoPromotion',
  props: ['dataActive', 'dataProduct'],
  data() {
    return {
      temp: {
        price_promotion: 0,
        date_promotion: {
          start: '',
          end: '',
        },
      },
      date_promotion: [],
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          },
        }],
      },
    };
  },
  created() {
    if (Object.keys(this.dataProduct).length > 0) {
      if (this.dataProduct.promotion_price){
        this.temp.price_promotion = this.dataProduct.promotion_price.price_promotion;
        this.date_promotion[0] = parseTime(this.dataProduct.promotion_price.date_start, '{y}-{m}-{d} {h}:{i}:{s}');
        this.date_promotion[1] = parseTime(this.dataProduct.promotion_price.date_end, '{y}-{m}-{d} {h}:{i}:{s}');
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
      if (this.date_promotion.length) {
        this.temp.date_promotion.start = parseTime(this.date_promotion[0], '{d}-{m}-{y} {h}:{i}:{s}');
        this.temp.date_promotion.end = parseTime(this.date_promotion[1], '{d}-{m}-{y} {h}:{i}:{s}');
      }
      this.$emit('handleProcessActive', active);
      this.$emit('handleProcessTemp', this.temp);
    },
  },
};
</script>
