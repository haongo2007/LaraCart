<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import * as echarts from 'echarts';
require('echarts/theme/macarons'); // echarts theme
import { debounce } from '@/utils';
import _rawData from '../life-expectancy-table.json'

export default {
  props: {
    className: {
      type: String,
      default: 'chart',
    },
    width: {
      type: String,
      default: '100%',
    },
    height: {
      type: String,
      default: '350px',
    },
    autoResize: {
      type: Boolean,
      default: true,
    },
    chartData: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      chart: null,
      sidebarElm: null,
      rawData:_rawData
    };
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val);
      },
    },
  },
  mounted() {
    this.initChart();
    if (this.autoResize) {
      this.__resizeHandler = debounce(() => {
        if (this.chart) {
          this.chart.resize();
        }
      }, 100);
      window.addEventListener('resize', this.__resizeHandler);
    }

    // Monitor the sidebar changes
    this.sidebarElm = document.getElementsByClassName('sidebar-container')[0];
    this.sidebarElm && this.sidebarElm.addEventListener('transitionend', this.sidebarResizeHandler);
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    if (this.autoResize) {
      window.removeEventListener('resize', this.__resizeHandler);
    }

    this.sidebarElm && this.sidebarElm.removeEventListener('transitionend', this.sidebarResizeHandler);

    this.chart.dispose();
    this.chart = null;
  },
  methods: {
    sidebarResizeHandler(e) {
      if (e.propertyName === 'width') {
        this.__resizeHandler();
      }
    },
    setOptions({ data, name, label } = data) {
      let _rawData = this.rawData;
      const countries = [
        'Finland',
        'France',
        'Germany',
        'Iceland',
        'Norway',
        'Poland',
        'Russia',
        'United Kingdom'
      ];
      const datasetWithFilters = [];
      const seriesList = [];
      echarts.util.each(countries, function (country) {
        var datasetId = 'dataset_' + country;
        datasetWithFilters.push({
          id: datasetId,
          fromDatasetId: 'dataset_raw',
          transform: {
            type: 'filter',
            config: {
              and: [
                { dimension: 'Year', gte: 1990 },
                { dimension: 'Country', '=': country }
              ]
            }
          }
        });
        seriesList.push({
          type: 'line',
          datasetId: datasetId,
          showSymbol: false,
          name: country,
          endLabel: {
            show: true,
            formatter: function (params) {
              return params.value[3] + ': ' + params.value[0];
            }
          },
          labelLayout: {
            moveOverlap: 'shiftY'
          },
          emphasis: {
            focus: 'series'
          },
          encode: {
            x: 'Year',
            y: 'Income',
            label: ['Country', 'Income'],
            itemName: 'Year',
            tooltip: ['Income']
          }
        });
      });
      let option = {
            animationDuration: 10000,
            dataset: [
              {
                id: 'dataset_raw',
                source: _rawData
              },
              ...datasetWithFilters
            ],
            tooltip: {
              order: 'valueDesc',
              trigger: 'axis'
            },
            xAxis: {
              type: 'category',
              nameLocation: 'middle'
            },
            yAxis: {
              name: 'Income'
            },
            grid: {
              right: 140
            },
            series: seriesList
          };

      this.chart.setOption(option);
      // this.chart.setOption({
      //   xAxis: {
      //     data: label,
      //     boundaryGap: false,
      //     axisTick: {
      //       show: false,
      //     },
      //   },
      //   grid: {
      //     left: 10,
      //     right: 10,
      //     bottom: 20,
      //     top: 30,
      //     containLabel: true,
      //   },
      //   tooltip: {
      //     trigger: 'axis',
      //     axisPointer: {
      //       type: 'cross',
      //     },
      //     padding: [5, 10],
      //   },
      //   yAxis: {
      //     axisTick: {
      //       show: false,
      //     },
      //   },
      //   legend: {
      //     data: name,
      //   },
      //   series: {
      //     name: name,
      //     itemStyle: {
      //       normal: {
      //         color: '#FF005A',
      //         lineStyle: {
      //           color: '#FF005A',
      //           width: 2,
      //         },
      //       },
      //     },
      //     smooth: true,
      //     type: 'line',
      //     data: data,
      //     animationDuration: 2800,
      //     animationEasing: 'cubicInOut',
      //   },
      // });
    },
    initChart() {
      this.chart = echarts.init(this.$el,'macarons')
      this.setOptions(this.chartData);
    },
  },
};
</script>
