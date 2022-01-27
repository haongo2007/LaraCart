<template>
  <div class="dashboard-editor-container">

    <el-row v-loading="loading" style="background:#fff;padding:16px;margin-bottom:32px;" :style="{height:height_filter}">
      <components :is="RunFilterData" :filter-data="filterData" @handleInitLineChartData="handleInitLineChartData" />
    </el-row>

    <panel-group :panel-data="panelData" @handleSetLineChartData="handleSetLineChartData" />

    <el-row v-loading="loading" style="background:#fff;padding:16px 16px 0;margin-bottom:32px;" :style="{height:height_chart}">
      <components :is="RunLineChart" :chart-data="lineChartData" />
    </el-row>

    <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <raddar-chart />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <pie-chart />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <bar-chart />
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="8">
      <el-col :xs="{span: 24}" :sm="{span: 24}" :md="{span: 24}" :lg="{span: 12}" :xl="{span: 12}" style="padding-right:8px;margin-bottom:30px;">
        <transaction-table />
      </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6}" :xl="{span: 6}" style="margin-bottom:30px;">
        <todo-list />
      </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6}" :xl="{span: 6}" style="margin-bottom:30px;">
        <box-card />
      </el-col>
    </el-row>
  </div>
</template>

<script>
import GithubCorner from '@/components/GithubCorner';
import FilterData from './components/FilterData';
import PanelGroup from './components/PanelGroup';
import LineChart from './components/LineChart';
import RaddarChart from './components/RaddarChart';
import PieChart from './components/PieChart';
import BarChart from './components/BarChart';
import TransactionTable from './components/TransactionTable';
import TodoList from './components/TodoList';
import BoxCard from './components/BoxCard';
import { overView } from '@/api/dashboard';

export default {
  name: 'DashboardAdmin',
  components: {
    GithubCorner,
    PanelGroup,
    LineChart,
    RaddarChart,
    PieChart,
    BarChart,
    TransactionTable,
    TodoList,
    BoxCard,
    FilterData,
  },
  data() {
    return {
      lineChartDataList: {},
      lineChartData: {},
      panelData: {},
      filterData: {},
      RunLineChart: '',
      RunFilterData: '',
      loading: true,
      height_chart: '300px',
      height_filter: '70px',
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    handleSetLineChartData(type) {
      this.lineChartData = this.lineChartDataList[type];
      this.lineChartData.label = this.lineChartDataList.label;
    },
    handleInitLineChartData(query) {
      this.fetchData(query);
    },
    async fetchData(query) {
      const { data } = await overView(query);
      var panelData = {};
      Object.keys(data).forEach(function(key) {
        panelData[key] = data[key].total;
      });
      this.panelData = panelData;
      this.lineChartDataList = data;
      this.lineChartData = data.newCustomers;
      this.lineChartData.label = data.label;
      this.lineChartLabel = data.label;
      this.filterData = data.rangeDate;
      this.loading = false;
      this.height_chart = 'auto';
      this.height_filter = 'auto';
      this.RunLineChart = 'LineChart';
      this.RunFilterData = 'FilterData';
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}
</style>
