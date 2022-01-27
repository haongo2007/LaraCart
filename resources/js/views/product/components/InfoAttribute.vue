<template>
  <el-container>
    <el-aside v-loading="loadAttributes" width="200px">
      <div v-for="(attribute,index) in temp" :key="attribute.id" style="margin: 5px;">
        <el-button type="warning" icon="el-icon-plus" @click="handleAddAttribute(index)">{{ attribute.name }}</el-button>
      </div>
      <div style="margin: 5px;">
        <el-button :disabled="disabled_clear" type="danger" icon="el-icon-close" @click="handleClearAllAttribute()">Clear</el-button>
      </div>
    </el-aside>
    <el-container v-for="(attribute,index) in temp" :key="attribute.id" v-loading="loadAttributes">
      <el-header align="center">{{ attribute.name }} ({{ attribute.values.length }})</el-header>
      <el-main v-for="(value,key) in attribute.values" v-if="attribute.values" :key="key">
        <div style="display: flex;justify-content: space-between;">
          <el-form-item label="Name">
            <el-input v-model="temp[index]['values'][key].name" />
          </el-form-item>
          <el-form-item label="Price">
            <el-input-number v-model="temp[index]['values'][key].price" style="width: 100%" :controls="false" />
          </el-form-item>
          <el-form-item label-width="30px">
            <el-button v-if="attribute.picker" type="success" @click="handleVisibleStorage(index,key)">Pick Image</el-button>
          </el-form-item>
          <el-form-item label-width="30px">
            <el-button type="danger" icon="el-icon-close" @click="handleClearAttribute(index,key)" />
          </el-form-item>
        </div>
        <div v-if="temp[index]['values'][key].files" v-loading="loadFiles">
          <lightbox :cells="3" :items="temp[index]['values'][key].files" />
          <div v-if="temp[index]['values'][key].palette" class="color-Palette">
            <h1>COLORS</h1>
            <ul class="swatch__container">
              <li v-for="color in temp[index]['values'][key].palette" class="swatch__wrapper">
                <div :style="{ backgroundColor: color.hex }" class="swatch">
                  <div :style="{ color: color.typeTextColor }" class="swatch__type">№ {{ color.number }}. {{ color.type }}</div>
                  <div :style="{ color: color.hexTextColor }" class="swatch__hex">{{ color.hex }}</div>
                  <div :style="{ color: color.nameTextColor }" class="swatch__name">{{ color.name }}</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </el-main>
    </el-container>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </el-container>
</template>

<script>
import AttributeGroupResource from '@/api/attribute_group';
import FileManager from '@/components/FileManager';
import EventBus from '@/components/FileManager/eventBus';
import * as Vibrant from 'node-vibrant';
import Lightbox from '@/components/LightBox';
import nearestColor from 'nearest-color';
import colorNameList from 'color-name-list';

const attributeGroupResource = new AttributeGroupResource();
const getColorName = new GetColorName();

function GetColorName() {
  const colors = colorNameList.reduce((o, { name, hex }) => Object.assign(o, { [name]: hex }), {});
  const nearest = nearestColor.from(colors);
  return hexColor => nearest(hexColor).name;
}
export default {
  name: 'InfoAttribute',
  components: {
    Lightbox,
    FileManager,
  },
  data() {
    return {
      loadAttributes: true,
      loadFiles: false,
      disabled_clear: true,
      temp: [],
      attNum: 0,
      dialogStorageVisible: false,
      componentStorage: '',
      currentSelectFile: [],
      palette: [],
    };
  },
  created() {
    this.fetchAttributeGroup();
    EventBus.$on('getFileResponse', this.handlerGeturl);
  },
  methods: {
    async fetchAttributeGroup(){
      const { data } = await attributeGroupResource.list();
      const that = this;
      data.forEach(function(v, i) {
        that.$set(that.temp, i, v);
        that.$set(that.temp[i], 'values', []);
      });
      this.loadAttributes = false;
    },
    handleAddAttribute(key){
      this.disabled_clear = false;
      this.attNum++;
      this.$set(this.temp[key]['values'], this.temp[key]['values'].length, { name: '', price: '' });
    },
    handleClearAllAttribute(){
      this.disabled_clear = true;
      this.attNum = 0;
      const that = this;
      this.temp.forEach(function(i, v) {
        that.temp[v].values.length = 0;
      });
    },
    handleClearAttribute(index, key){
      this.temp[index].values.splice(key, 1);
      if (this.attNum-- == 0) {
        this.disabled_clear = true;
      }
    },
    dialogStorageClose(){
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handleVisibleStorage(index, key){
      this.$store.commit('fm/setDisks', 'product');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
      this.currentSelectFile = [index, key];
    },
    handlerGeturl(data) {
      this.loadFiles = true;
      this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]], 'files', []);
      this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]], 'palette', []);
      const that = this;
      data.forEach(function(v, i) {
        that.temp[that.currentSelectFile[0]]['values'][that.currentSelectFile[1]].files.push(v);
      });
      this.getPalette(data[0]);
      this.dialogStorageClose();
    },
    getPalette(imageSrc) {
      Vibrant.from(imageSrc).maxColorCount(200).getPalette().then((palette) => {
        const colors = [];
        var number = 0;
        for (const color in palette) {
          number = number + 1;
          const type = color;
          const typeTextColor = palette[color].getTitleTextColor();
          const hex = palette[color].getHex();
          const hexTextColor = palette[color].getBodyTextColor();
          const name = getColorName(hex);
          const nameTextColor = palette[color].getBodyTextColor();
          colors.push({ number, type, typeTextColor, hex, hexTextColor, name, nameTextColor });
        }
        this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]].palette = colors;
        this.loadFiles = false;
      });
    },
  },
};
</script>
<style type="text/css">
.swatch__container {
  margin: 0;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.swatch {
  display: inline-block;
  width: 100%;
  height: 100px;
  border-radius: 4px;
  margin-bottom: 1em;
  transition: background .3s ease;
}
.swatch__wrapper {
  display: inline-block;
  width: 30%;
  list-style: none;
  margin-bottom: 1.4em;
}
.swatch__hex {
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 1.5px;
  padding: .3em 0 .2em .3em;
}
.swatch__name {
  font-size: 11px;
  color: #aaa;
  padding-left: .5em;
}
.swatch__type {
  font-size: 13px;
  color: #aaa;
  padding: 1.0em 0 .5em .5em;
}
</style>
