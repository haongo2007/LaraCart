<template>
  <el-form ref="form" :model="form" label-width="120px">
    <div v-if="!isNew">
      <el-descriptions title="Attribute" direction="vertical" :column="3" border>
        <el-descriptions-item v-for="(item,index) in dataAtributeGroup" :key="index" :label="item">
          <div v-if="typeof dataAttribute == 'string'">
            <span v-for="(dataAttributed,key) in dataAttributeds" :key="key" v-if="dataAttributed[index]">
              {{ dataAttributed[index].split('__')[0] }}
              <span class="el-badge__content el-badge__content--warning">
                {{ dataAttributed[index].split('__')[1] | changeCurrency(dataExchangeRate,dataCurrency) }}
              </span>
            </span>
          </div>
          <span v-else>
            <span class="el-badge__content el-badge__content--warning">
              {{ dataAttributed[index].split('__')[0] }} {{ ( dataAttributed[index].split('__')[1] > 0 ? ' + '+dataAttributed[index].split('__')[1] : '') | changeCurrency(dataExchangeRate,dataCurrency) }}
            </span>
          </span>
        </el-descriptions-item>
      </el-descriptions>
    </div>
    <div v-else>
      <el-descriptions title="Attribute" direction="vertical" :column="3" border>
        <el-descriptions-item v-for="(detail,ind) in dataAttributeRemade" :key="ind" :label="ind">
          <el-radio v-for="(attb,key) in detail" :key="attb.id" v-model="form[ind]" :label="attb.id" @change="handleUpdateModel(attb)">
            {{ attb.name }} <span class="el-badge__content el-badge__content--warning"> {{ attb.price | changeCurrency(dataExchangeRate,dataCurrency) }} </span>
          </el-radio>
        </el-descriptions-item>
      </el-descriptions>
    </div>
  </el-form>
</template>
<script>
export default {
  name: 'AttributesProduct',
  props: ['dataAttribute', 'dataAtributeGroup', 'dataCurrency', 'dataExchangeRate', 'isNew', 'dataProduct'],
  data() {
    return {
      form: {},
      dataAttributeRemade: {},
    };
  },
  computed: {
    dataAttributeds(){
      if (this.dataAttribute) {
        let data;
        if (typeof this.dataAttribute === 'string') {
          data = this.dataAttribute.replace(/&quot;/g, '"');
        } else {
          data = this.dataAttribute;
        }
        try {
          JSON.parse(data);
        } catch (e) {
          return this.dataAttribute;
        }
        return JSON.parse(data);
      }
    },
  },
  watch: {
    'dataAttribute': {
      handler(newValue, oldValue) {
        this.dataAttributeRemade = newValue;
      },
    },
  },
  created(){
    this.dataAttributeRemade = this.dataAttribute;
  },
  methods: {
    handleUpdateModel(item){
      if (item.hasOwnProperty('children')) {
        this.dataAttributeRemade = { ...this.dataAttributeRemade, ...item.children };
      }
      var text = {};
      text[item.group_id] = item.name + '__' + item.price;
    	this.$emit('handleAttributeProduct', { group: item.group_id, attr: item.id, prd: this.dataProduct, text: text });
    },
  },
};
</script>
