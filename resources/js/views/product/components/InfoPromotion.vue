<template>
  <div>
    <el-row>
      <el-col :span="16" :offset="4">
        <el-form-item :label="$t('table.promotion_price')" prop="cost">
          <el-input-number v-model="temp.priceToPromotion" style="width: 100%" :controls="false" />
        </el-form-item>
        <el-form-item :label="$t('table.sale_date')" prop="sale_date">
          <el-date-picker
            v-model="temp.arDateToPromotion"
            style="width: 100%"
            type="datetimerange"
            align="right"
            unlink-panels
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            :picker-options="pickerOptions"
            @change="handleFilterDate()"
          />
        </el-form-item>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  name: 'InfoPromotion',
  data() {
    return {
      temp: {
        priceToPromotion: 0,
        arDateToPromotion: [],
      },
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
  },
  methods: {
    handleFilterDate(){

    },
  },
};
</script>
