<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import * as echarts from 'echarts';
require('echarts/theme/macarons'); // echarts theme
import { debounce } from '@/utils';

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
    setOptions({ data, name, label, companies } = data) {
      const datasetWithFilters = [];
      const seriesList = [];
      echarts.util.each(companies, function (id,company) {
        var datasetId = 'dataset_' + company;
        datasetWithFilters.push({
          id: datasetId,
          fromDatasetId: 'dataset_raw',
          transform: {
            type: 'filter',
            config: {
              and: [
                { 
                  dimension: 'Date' ,
                  '>=': '2000-01',
                  '<': '2100-01',
                  parser: 'time'
                },
                { dimension: 'Company', '=': id }
              ]
            }
          }
        });
        seriesList.push({
          type: 'line',
          datasetId: datasetId,
          showSymbol: false,
          name: company,
          smooth: true,
          animationEasing: 'cubicInOut',
          endLabel: {
            show: true,
            formatter: function (params) {
              return company + ': ' + params.value[0];
            }
          },
          labelLayout: {
            moveOverlap: 'shiftY'
          },
          emphasis: {
            focus: 'series'
          },
          encode: {
            x: 'Date',
            y: 'value',
            label: ['Company', 'value'],
            itemName: 'Date',
            tooltip: ['value']
          }
        });
      });
      let option = {
            animationDuration: 2800,
            dataset: [
              {
                id: 'dataset_raw',
                source: data
              },
              ...datasetWithFilters
            ],
            title: {
              text: name
            },
            tooltip: {
              trigger: 'axis',
              axisPointer: {
                type: 'cross'
              },
              backgroundColor: 'rgba(255, 255, 255, 0.8)',
            },
            toolbox: {
              show: true,
              feature: {
                dataZoom: {
                  yAxisIndex: 'none'
                },
                dataView: { readOnly: false },
                magicType: { type: ['line', 'bar'] },
                restore: {},
                saveAsImage: {}
              }
            },
            xAxis: {
              type: 'category',
              nameLocation: 'middle'
            },
            yAxis: {
            },
            grid: {
              left: 10,
              right: 10,
              bottom: 20,
              top: 30,
              containLabel: true,
            },
            series: seriesList
          };

      this.chart.setOption(option);
    },
    initChart() {
      this.chart = echarts.init(this.$el,'macarons')
      this.setOptions(this.chartData);
    },
  },
};
</script>
