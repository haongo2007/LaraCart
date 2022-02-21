<template>
  <el-form ref="form" :model="form" label-width="120px">
    <el-descriptions title="Attribute" direction="vertical" :column="3" border>
      <el-descriptions-item v-for="(item,index) in dataAtributeGroup" :key="index" :label="item">
        <span v-if="!isNew">
          {{ dataAttributed[index].split('__')[0] }} {{ ( dataAttributed[index].split('__')[1] > 0 ? ' + '+dataAttributed[index].split('__')[1] : '') }}
          <el-badge v-if="dataAttributed[index].split('__')[1] > 0" :value="dataCurrency" class="item" type="warning" />
        </span>
        <div v-else>
          <el-radio v-for="(attb,ind) in dataAttribute" v-if="attb.attribute_group_id == index" :key="attb.id" v-model="form[item]" :label="attb.id" @change="handleUpdateModel(index,attb.id,attb.product_id,attb.add_price,attb.name)">
            {{ attb.name }} {{ (attb.add_price ? '(+ '+attb.add_price+' '+dataCurrency +')' : '') }}
          </el-radio>
        </div>

      </el-descriptions-item>
    </el-descriptions>
  </el-form>
</template>
<script>
export default {
  name: 'AttributesProduct',
  props: ['dataAttribute', 'dataAtributeGroup', 'dataCurrency', 'isNew'],
  data() {
    return {
      form: {},
    };
  },
  computed: {
    dataAttributed(){
      if (this.dataAttribute) {
        try {
          JSON.parse(this.dataAttribute);
        } catch (e) {
            return this.dataAttribute;
        }
        return JSON.parse(this.dataAttribute);
      }
    },
  },
  created(){
  },
  methods: {
    handleUpdateModel(group, attr_id, prd_id, price, name){
      var text = {};
      text[group] = name + '__' + price;
    	this.$emit('handleAttributeProduct', { group: group, attr: attr_id, prd: prd_id, text: text });
    },
  },
};
</script>
