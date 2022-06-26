<template>
  <div style="height: 100%;">
    <h3 class="title_jvector">Order Country In Year</h3>
    <el-row v-loading="loading" class="world-map" style="height: calc(100% - 40px);"/>
  </div>
</template>

<script>

require('@/utils/jsvectormap.min');
require('@/utils/jvectormap-world-mill');
import { ordersAmountCountry } from '@/api/dashboard';


export default {
  props: {
    dataLabel:{
      type: Object,
      required: true,
    }
  },
  data() {
    return {
      loading:true,
    };
  },
  mounted(){
    ordersAmountCountry().then(({data}) => {
      this.loading = false;
      this.drawMap(data.items,this.dataLabel);
    });
  },
  methods: {
    drawMap(data,label){
      let storeList = label;
      let mapOrder = {};
      let mapAmount = {};
      let mapStore_id = {};
      let value_region = {};
      data.forEach(function (key, i) {
        if (!mapOrder.hasOwnProperty(key.name)) {
          mapOrder[key.name] = {};
          mapAmount[key.name] = {};
          mapStore_id[key.name] = {};
          value_region[key.name] = 0;
        }
        value_region[key.name] += key.order;
        for(let props in storeList) {
          if (!mapOrder[key.name].hasOwnProperty(props)) {
            mapOrder[key.name][props] = {};
            mapAmount[key.name][props] = {};
            mapStore_id[key.name][props] = {};
          }
          if (storeList[props] == key.store_id) {
            mapOrder[key.name][props] = key.order;
            mapAmount[key.name][props] = key.amount;
            mapStore_id[key.name][props] = key.store_id;
          }
        };
      })
      var jvm = new jsVectorMap({
        map: 'world_mill_en',
        selector: ".world-map",
        zoomButtons : false,
        backgroundColor: "transparent",
        zoomOnScroll: true,
        regionStyle: {
          initial: {
            fill: '#e4e4e4',
            "fill-opacity": 0.9,
            stroke: 'none',
            "stroke-width": 0,
            "stroke-opacity": 0
          },
          hover: { fill: 'rgb(47 179 133)' }
        },
        visualizeData: {
          scale: ['#eeeeee', '#42b983'],
          values: value_region
        },
        onRegionTooltipShow: function(el, code){
          if(mapOrder[code]){
            let result = '<h4 class="title_country">'+el._tooltip.innerHTML+' ('+value_region[code]+')</h4>';
            for(let props in storeList) {
                result += '<div class="block_store">'+props+'</br> Amount: '+mapAmount[code][props]+'</br> Order: '+mapOrder[code][props]+'</div>';
            }
            el._tooltip.innerHTML = result;
          }
        }
      });
    }
  },
};
</script>
<style type="text/css">
  .jvm-tooltip{
    position:absolute;
    display: none;
    border:1px solid #cdcdcd;
    border-radius:3px;
    background:rgb(240, 242, 245);
    font-family:sans-serif;
    font-size:smaller;
    padding:5px
  }
  .title_country{
    margin: 0px 0px 10px 0px;
  }
  .block_store{
    border: 0.2px solid #fff;
    border-radius: 3px;
    padding: 5px;
    margin-bottom: 5px;
  }
  .title_jvector{
    margin: 0px;
    padding: 10px 20px;
  }
  svg {
  -ms-touch-action: none;
  touch-action: none;
}
image,
text,
.jvm-zoomin,
.jvm-zoomout {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.jvm-container {
  -ms-touch-action: none;
  touch-action: none;
  position: relative;
  overflow: hidden;
  height: 100%;
  width: 100%;
}
.jvm-tooltip {
  border-radius: 3px;
  background-color: #42b983;
  font-family: sans-serif, Verdana;
  font-size: smaller;
  box-shadow: 1px 2px 12px rgba(0, 0, 0, 0.2);
  padding: 3px 5px;
  white-space: nowrap;
  position: absolute;
  display: none;
  color: #fff;
}
.jvm-tooltip.active {
  display: block;
}
.jvm-zoom-btn {
  border-radius: 3px;
  background-color: #292929;
  padding: 3px;
  box-sizing: border-box;
  position: absolute;
  line-height: 10px;
  cursor: pointer;
  color: #fff;
  height: 15px;
  width: 15px;
  left: 10px;
}
.jvm-zoom-btn.jvm-zoomout {
  top: 30px;
}
.jvm-zoom-btn.jvm-zoomin {
  top: 10px;
}
.jvm-series-container {
  right: 15px;
  position: absolute;
}
.jvm-series-container.jvm-series-h {
  bottom: 15px;
}
.jvm-series-container.jvm-series-v {
  top: 15px;
}
.jvm-series-container .jvm-legend {
  background-color: #fff;
  border: 1px solid #e5e7eb;
  margin-left: 0.75rem;
  border-radius: 0.25rem;
  border-color: #e5e7eb;
  padding: 0.6rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  float: left;
}
.jvm-series-container .jvm-legend .jvm-legend-title {
  line-height: 1;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 0.5rem;
  margin-bottom: 0.575rem;
  text-align: left;
}
.jvm-series-container .jvm-legend .jvm-legend-inner {
  overflow: hidden;
}
.jvm-series-container .jvm-legend .jvm-legend-inner .jvm-legend-tick {
  overflow: hidden;
  min-width: 40px;
}
.jvm-series-container
  .jvm-legend
  .jvm-legend-inner
  .jvm-legend-tick:not(:first-child) {
  margin-top: 0.575rem;
}
.jvm-series-container
  .jvm-legend
  .jvm-legend-inner
  .jvm-legend-tick
  .jvm-legend-tick-sample {
  border-radius: 4px;
  margin-right: 0.65rem;
  height: 16px;
  width: 16px;
  float: left;
}
.jvm-series-container
  .jvm-legend
  .jvm-legend-inner
  .jvm-legend-tick
  .jvm-legend-tick-text {
  font-size: 12px;
  text-align: center;
  float: left;
}
.jvm-line[animation="true"] {
  -webkit-animation: jvm-line-animation 10s linear forwards infinite;
  animation: jvm-line-animation 10s linear forwards infinite;
}
@-webkit-keyframes jvm-line-animation {
  from {
    stroke-dashoffset: 250;
  }
}
@keyframes jvm-line-animation {
  from {
    stroke-dashoffset: 250;
  }
}

</style>