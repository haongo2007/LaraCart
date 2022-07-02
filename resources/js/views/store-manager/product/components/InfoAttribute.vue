<template>
  <div>
    <el-form ref="dataAttributeForm" class="form-container" label-width="150px">
      <el-row v-show="!loadAttributes" class="el-main-form">
        <el-col :span="2">
          <div v-loading="loadAttributes" width="200px">
            <draggable-tree @drop="dropAttribute" :data="temp" draggable="draggable" cross-tree="cross-tree">
              <div slot-scope="{data, store}">
                <template v-if="!data.isDragPlaceHolder">
                  <b v-if="data.children && data.children.length" @click="store.toggleOpen(data)">
                    {{ data.open ? '-' : '+' }} 
                  </b>
                  <span>{{ data.name }}</span>
                  <el-button :disabled="data.children.length == 0 && temp.length <= 1 && disabledChild" type="success" size="mini" @click="handleAddAttribute(data.id)"><i class="el-icon-plus"></i></el-button>
                </template>
              </div>
            </draggable-tree>
            <!-- <div v-for="(attribute,index) in temp" :key="attribute.id" style="margin: 5px;">
              <el-button type="warning" icon="el-icon-plus" ">{{ attribute.name }}</el-button>
            </div> -->
          </div>
        </el-col>
        <el-col :span="20" style="display: flex;justify-content: space-around;">
          <div v-for="(attribute,index) in temp" :key="attribute.id" v-loading="loadAttributes" style="padding: 0 20px;" :style="[temp.length > 1 ? {'width': '50%'} : {'width': '70%'}]" >
            <el-header align="center">{{ attribute.name }} {{ attribute.values ? '( '+ attribute.values.length + ' )': '' }}</el-header>
            <div v-for="(value,key) in attribute.values" v-if="attribute.values" :key="key" class="box-attributes" >
              <div style="display: flex;justify-content: space-between;align-items: center;" >
                <el-form-item label-width="60px" label="Name" style="width: 100%;margin: 15px 0px;">
                  <el-input size="mini" v-model="temp[index]['values'][key].name" />
                </el-form-item>
                <el-form-item label-width="60px" label="Price" style="width: 100%;margin: 15px 0px;">
                  <el-input-number size="mini" v-model="temp[index]['values'][key].add_price" style="width: 100%" :controls="false" />
                </el-form-item>
                <el-form-item label-width="30px" style="width: 100%;margin: 15px 0px;">
                  <el-button-group>
                    <el-button size="mini" v-if="attribute.picker" type="success" @click="handleVisibleStorage(index,key)">Image</el-button>
                    <el-button size="mini" type="danger" icon="el-icon-close" @click="handleClearAttribute(index,key)" />
                  </el-button-group>
                </el-form-item>
              </div>
              <div v-if="temp[index]['values'][key].images || temp[index]['values'][key].files" v-loading="loadFiles">
                <lightbox :cells="3" :items="temp[index]['values'][key].files" />
                <div v-if="temp[index]['values'][key].palette" class="color-Palette">
                  <h4 style="margin: 10px 0px">Choose Code</h4>
                  <ul class="swatch__container">
                    <li @click="handleActivePal(index,key,i)" 
                      v-for="(color,i) in temp[index]['values'][key].palette" :class="color.active==true?'active':''" class="swatch__wrapper">
                      <el-tooltip content="Top center" placement="top">
                        <div slot="content">
                          <div class="swatch__type">№ {{ color.number }}. {{ color.type }}</div>
                          <div class="swatch__name">{{ color.name }}</div>
                        </div>
                        <div :style="{ backgroundColor: color.hex }" class="swatch"></div>
                      </el-tooltip>
                    </li>
                  </ul>
                </div>
              </div>


              <div v-if="value.children.length > 0" v-for="(child,childKey) in value.children" :key="childKey" v-loading="loadAttributes">
                <div style="display: flex;justify-content: space-between;">
                  <el-form-item label-width="60px" label="Name" style="width: 100%;margin: 15px 0px;">
                    <el-input size="mini" v-model="temp[index]['values'][key]['children'][childKey].name" />
                  </el-form-item>
                  <el-form-item label-width="60px" label="Price" style="width: 100%;margin: 15px 0px;">
                    <el-input-number size="mini" v-model="temp[index]['values'][key]['children'][childKey].add_price" style="width: 100%" :controls="false" />
                  </el-form-item>
                  <el-form-item label-width="30px" style="width: 100%;margin: 15px 0px;">
                    <el-button-group>
                      <el-button size="mini" v-if="attribute.children[index] && attribute.children[index].picker" type="success" @click="handleVisibleStorage(index,key,childKey)">Image</el-button>
                      <el-button size="mini" type="danger" icon="el-icon-close" @click="handleClearAttribute(index,key,childKey)" />
                    </el-button-group>
                  </el-form-item>
                </div>
                <div v-if="temp[index]['values'][key]['children'][childKey].images != ''" v-loading="loadFiles">
                  <lightbox :cells="3" :items="temp[index]['values'][key]['children'][childKey].files" />
                  <div v-if="temp[index]['values'][key]['children'][childKey].palette" class="color-Palette">
                    <h4 style="margin: 10px 0px">Choose Code</h4>
                    <ul class="swatch__container">
                      <li @click="handleActivePal(index,key,'children',childKey,i)" 
                        v-for="(color,i) in temp[index]['values'][key]['children'][childKey].palette" class="swatch__wrapper" :class="color.active==true?'active':''">
                        <div :style="{ backgroundColor: color.hex }" class="swatch">
                          <el-tooltip content="Top center" placement="top">
                            <div slot="content">
                              <div class="swatch__type">№ {{ color.number }}. {{ color.type }}</div>
                              <div class="swatch__name">{{ color.name }}</div>
                            </div>
                            <div :style="{ backgroundColor: color.hex }" class="swatch"></div>
                          </el-tooltip>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>


            </div>
          </div>
        </el-col>
        <el-col :span="2">
          <div style="margin: 5px;">
            <el-button :disabled="disabled_clear" type="danger" icon="el-icon-close" @click="handleClearAllAttribute()">{{ $t('form.clear') }}</el-button>
          </div>
        </el-col>
      </el-row>
    </el-form>
    <el-row>
      <el-button-group class="pull-right">
        <el-button type="warning" icon="el-icon-arrow-left" @click="backStep">
          {{ $t('form.prev') }}
        </el-button>
        <el-button type="primary" icon="el-icon-arrow-right" @click="nextStep">
          {{ $t('form.next') }}
        </el-button>
      </el-button-group>
    </el-row>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>
import {DraggableTree} from 'vue-draggable-nested-tree'
import AttributeGroupResource from '@/api/attribute-group';
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
    DraggableTree
  },
  props: ['dataActive', 'dataProduct'],
  data() {
    return {
      disabledChild:true,
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
  },
  watch: {
    'attNum': {
      handler(newValue, oldValue) {
        if (newValue == 0) {
          this.disabled_clear = true;
          this.disabledChild = true;
        }else{
          this.disabled_clear = false;
        }
      },
    },
  },
  methods: {
    backStep() {
      const active = this.dataActive - 1;
      this.$emit('handleProcessActive', active);
    },
    nextStep() {
      const active = this.dataActive + 1;
      this.$emit('handleProcessActive', active);
      let lastTemp = [];
      this.temp.forEach(function (v,i) {
        let cur = {
          values : v.values,
          picker : v.picker,
          id : v.id,
          name : v.name
        }
        if (v.children.length > 0) {
          v.children.forEach(function (r,j) {
            cur['child_name'] = r.name;
            cur['child_id'] = r.id;
          })
        }
        lastTemp.push(cur)
      })
      this.$emit('handleProcessTemp', { attribute: lastTemp });
    },
    handleActivePal(index,key,i,childKey,k){
      if (i == 'children') {
        this.temp[index]['values'][key][i][childKey].palette.forEach((item,key,arr) => {
          arr[key].active = 0;
        });
        this.temp[index]['values'][key][i][childKey].palette[k].active = 1;
      }else{
        this.temp[index]['values'][key].palette.forEach((item,key,arr) => {
          arr[key].active = 0;
        });
        this.temp[index]['values'][key].palette[i].active = 1;
      }
    },
    async fetchAttributeGroup(){
      const { data } = await attributeGroupResource.list();
      const that = this;
      const values = [];
      
      if (Object.keys(this.dataProduct).length > 0 && (this.dataProduct.hasOwnProperty('attributes_parent') && this.dataProduct.attributes_parent.length > 0)) {// edit
        let attributes_parent = {};
        console.log(this.dataProduct);
        data.forEach(function(v, i) {
          if (!attributes_parent.hasOwnProperty(v.id)) {
            attributes_parent[v.id] = [];
          }
          let foundGroup = that.dataProduct.attributes_parent.filter((item) => item.attribute_group_id == v.id);
          if (foundGroup.length > 0) {
            attributes_parent[v.id].values = foundGroup;
            attributes_parent[v.id].picker = v.picker;
            attributes_parent[v.id].name = v.name;
            attributes_parent[v.id].id = v.id;
          }
          if (Object.keys(attributes_parent[v.id]).length == 0) {
            delete attributes_parent[v.id];
          }
        });
        let y = 0;
        for(let attribute in attributes_parent){
          let id = attributes_parent[attribute].id;
          let name = attributes_parent[attribute].name;
          let picker = attributes_parent[attribute].picker;
          let value = attributes_parent[attribute].values;

          let index_group = data.findIndex((item) => item.id == id);
          this.$set(this.temp,y,data[index_group]);
          this.$set(this.temp[y], 'values', []);
          // filter value

          value.forEach(function (v,i) {

            if (v.images) {
                let files = v.images.split(',');
                v['files'] = files;
            }

            if (v.children.length > 0) {
              v.children.forEach(function(vchild, ichild) {

                let index_group_child = data.findIndex((item) => item.id == vchild.attribute_group_id);
                if (index_group_child >= 0) {
                  that.$set(that.temp[y],'children', []);
                  that.temp[y]['children'].push(data[index_group_child]);
                  that.disabledChild = false;
                }

                if (vchild.images) {
                  let files = vchild.images.split(',');
                  v['children'][ichild]['files'] = files;
                }
              })

            }
            that.attNum++;
          });
          this.temp[y]['values'] = value;
          y++;
        }
      }else{
        data.forEach(function(v, i) {
          that.$set(that.temp, i, v);
          that.$set(that.temp[i], 'values', []);
        });
      }
      this.loadAttributes = false;
    },
    handleAddAttribute(key){
      let find = this.temp.findIndex((item) => item.id == key);
      if (find < 0) {
        key = this.temp.length - 1;
        if (!this.temp[key]['values'][this.temp[key]['values'].length - 1]['children']) {
          this.$set(this.temp[key]['values'][this.temp[key]['values'].length - 1], 'children', []);
        }
        this.temp[key]['values'][this.temp[key]['values'].length - 1]['children'].push({name: '', add_price: ''});
      } else {
        this.attNum++;
        if (!this.temp[find].hasOwnProperty('values')) {
          this.$set(this.temp[find], 'values' , []);
        }
        this.$set(this.temp[find]['values'], this.temp[find]['values'].length, { name: '', add_price: '' });
      }
      this.disabledChild = false;
    },
    handleClearAllAttribute(){
      this.attNum = 0;
      const that = this;
      this.temp.forEach(function(i, v) {
        that.temp[v].values.length = 0;
      });
    },
    handleClearAttribute(index, key, childKey){
      if (childKey || childKey == 0) {
        this.temp[index].values[key].children.splice(childKey, 1);
      }else{
        this.temp[index].values.splice(key, 1);
        this.attNum--;
      }
    },
    dialogStorageClose(){
      EventBus.$off('getFileResponse');
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handleVisibleStorage(index, key, childKey){
      if (childKey || childKey == 0) {
        this.currentSelectFile = [index, key, childKey];
      } else {
        this.currentSelectFile = [index, key];
      }
      EventBus.$on('getFileResponse', this.handlerGeturl);
      this.$store.commit('fm/setDisks', 'product');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
    },
    handlerGeturl(data) {
      this.loadFiles = true;
      const that = this;
      if (this.currentSelectFile.length > 2) {
        this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]]['children'][this.currentSelectFile[2]], 'files', []);
        this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]]['children'][this.currentSelectFile[2]], 'palette', []);
        data.forEach(function(v, i) {
          that.temp[that.currentSelectFile[0]]['values'][that.currentSelectFile[1]]['children'][that.currentSelectFile[2]].files.push(v);
        });
      } else {
        this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]], 'files', []);
        this.$set(this.temp[this.currentSelectFile[0]]['values'][this.currentSelectFile[1]], 'palette', []);
        data.forEach(function(v, i) {
          that.temp[that.currentSelectFile[0]]['values'][that.currentSelectFile[1]].files.push(v);
        });
      }
      this.getPalette(data[0]);
      this.dialogStorageClose();
    },
    getPalette(imageSrc,currentSelectFile = []) {
      Vibrant.from(imageSrc).maxColorCount(200).getPalette().then((palette) => {
        const colors = [];
        var number = 0;
        for (const color in palette) {
          let active = 0;
          if (number == 0) {
            active = 1;
          }
          number = number + 1;
          const type = color;
          const typeTextColor = palette[color].getTitleTextColor();
          const hex = palette[color].getHex();
          const hexTextColor = palette[color].getBodyTextColor();
          const name = getColorName(hex);
          const nameTextColor = palette[color].getBodyTextColor();
          colors.push({ number, type, typeTextColor, hex, hexTextColor, name, nameTextColor,active, active });
        }
        if (this.currentSelectFile.length > 2) {
          this.temp[currentSelectFile.length>0 ? currentSelectFile[0] : this.currentSelectFile[0]]['values'][currentSelectFile.length>0 ? currentSelectFile[1] :this.currentSelectFile[1]]['children'][currentSelectFile.length>0 ? currentSelectFile[2] :this.currentSelectFile[2]].palette = colors;
        } else {
          this.temp[currentSelectFile.length>0 ? currentSelectFile[0] : this.currentSelectFile[0]]['values'][currentSelectFile.length>0 ? currentSelectFile[1] :this.currentSelectFile[1]].palette = colors;
        }
        this.loadFiles = false;
      });
    },
    dropAttribute(node, targetTree, oldTree){
      this.temp.forEach(function (v,i) {
        if (v['id'] != node.id) {
          if (v['values']) {v['values'].forEach((val) => delete val.children)}
        }
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
  cursor: pointer;
}
.swatch {
  display: inline-block;
  width: 100%;
  border-radius: 4px;
  height: 25px;
  transition: background .3s ease;
}
.swatch__wrapper {
  display: inline-block;
  width: 5%;
  list-style: none;
  margin-right: 10px;
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
}
.swatch__type {
  font-size: 13px;
  color: #aaa;
}
.box-attributes{
    background: #f5f7fa;
    padding: 10px;
    margin: 5px;
    border-radius: 5px;
    border: 1px solid #eee;
}
.tree-node{
  background: #f5f7fa;
  padding: 5px;
  border-radius: 5px;
  color: white;  
  border: 1px solid #eee;
}
.tree-node-inner-back{
    background: #e6a23c;
    padding: 5px;
    margin-bottom: 0px!important;
    border-radius: 3px;
}
.tree-node-inner{
  width: 100%;
}
.tree-node-inner div{
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.tree-node-children{
  margin-left: 10px;
}
.tree-node-children .tree-node{
  padding: 0px;
  margin: 5px 0px;
}
.swatch__container .active .el-tooltip{
  box-shadow: 0px 0px 3px 2px #409eff;
}
</style>
