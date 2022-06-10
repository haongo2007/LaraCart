<template>
    <div class="view-container">
        <div class="files-header">
            <span class="file-item-name" v-on:click="sortBy('name')">
                {{ lang.manager.table.name }}
                <template v-if="sortSettings.field === 'name'">
                  <span class="caret-wrapper">
                    <i class="el-icon-arrow-down" v-show="sortSettings.direction === 'down'"></i>
                    <i class="el-icon-arrow-up" v-show="sortSettings.direction === 'up'"></i>
                  </span>
                </template>
            </span>
            <div>
              <span class="file-item-size">
                {{ lang.manager.table.size }}
                <template v-if="sortSettings.field === 'size'">
                    <span class="caret-wrapper">
                      <i class="el-icon-arrow-down" v-show="sortSettings.direction === 'down'"></i>
                      <i class="el-icon-arrow-up" v-show="sortSettings.direction === 'up'"></i>
                    </span>
                </template>
              </span>
              <span class="file-last-modified">
                {{ lang.manager.table.type }}
                <template v-if="sortSettings.field === 'type'">
                    <span class="caret-wrapper">
                      <i class="el-icon-arrow-down" v-show="sortSettings.direction === 'down'"></i>
                      <i class="el-icon-arrow-up" v-show="sortSettings.direction === 'up'"></i>
                    </span>
                </template>
              </span>
              <span class="file-itemaction">
                {{ lang.manager.table.lastModified }}
                <template v-if="sortSettings.field === 'date'">
                    <span class="caret-wrapper">
                      <i class="el-icon-arrow-down" v-show="sortSettings.direction === 'down'"></i>
                      <i class="el-icon-arrow-up" v-show="sortSettings.direction === 'up'"></i>
                    </span>
                </template>
              </span>
            </div>
        </div>
        <div class="file-content-level-up" v-if="!isRootPath" v-on:click="levelUp">
          <div class="file-content-icon">
            <i class="el-icon-top"></i>
          </div>
          <div class="file-content">
            <div class="content-wrapper">
              <span class="file-name">...</span>
            </div>
          </div>
        </div>

        <div class="file-content-folder" 
          v-for="(directory, index) in directories" 
          v-bind:key="`d-${index}`" 
          v-bind:class="{'table-info': checkSelect('directories', directory.path)}" 
          v-on:click="selectItem('directories', directory.path, $event)" 
          v-on:contextmenu.prevent="contextMenu(directory, $event)">
          <div class="file-folder" v-on:dblclick="selectDirectory(directory.path)">
            <div class="file-folder-name" v-bind:class="(acl && directory.acl === 0) ? 'text-hidden' : ''" ><i class="el-icon-folder"></i> {{ directory.basename }}</div>
            <div class="file-folder-left">
              <span class="file-folder-size"></span>
              <span class="file-folder-type">{{ lang.manager.table.folder }}</span>
              <span class="file-folder-date">{{ timestampToDate(directory.timestamp) }}</span>
            </div>
          </div>
        </div>

        <div class="file-content-file" 
          v-for="(file, index) in files"
          v-bind:key="`f-${index}`"
          v-bind:class="{'table-info': checkSelect('files', file.path)}"
          v-on:click="selectItem('files', file.path, $event)"   
          v-on:dblclick="selectAction(file.path, file.extension)"
          v-on:contextmenu.prevent="contextMenu(file, $event)">
          <div class="file-file" >
            <div class="file-file-name" v-bind:class="(acl && file.acl === 0) ? 'text-hidden' : ''" ><i class="fas" v-bind:class="extensionToIcon(file.extension)" ></i> 
              <div>{{ file.filename ? file.filename : file.basename }}</div></div>
            <div class="file-file-left">
              <span class="file-file-size">{{ bytesToHuman(file.size) }}</span>
              <span class="file-file-type">{{ file.extension }}</span>
              <span class="file-file-date">{{ timestampToDate(file.timestamp) }}</span>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
import translate from './../../mixins/translate';
import helper from './../../mixins/helper';
import managerHelper from './mixins/manager';

export default {
  name: 'table-view',
  mixins: [translate, helper, managerHelper],
  props: {
    manager: { type: String, required: true },
  },
  computed: {
    /**
     * Sort settings
     * @returns {*}
     */
    sortSettings() {
      return this.$store.state.fm[this.manager].sort;
    },
  },
  methods: {
    /**
     * Sort by field
     * @param field
     */
    sortBy(field) {
      console.log(field);
      this.$store.dispatch(`fm/${this.manager}/sortBy`, { field, direction: null });
    },
  },
};
</script>

<style lang="scss">
    .view-container{
      overflow-y: scroll;
      height: calc(100vh - 400px);
      padding:0px 20px;
        .table-info{
          box-shadow: 0 0 3px 1px #409EFF;
        }
        .files-header{
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            margin-left: 7.2rem;
            color: #5e5873;
            margin-top:20px;
            cursor: pointer;
            .file-item-size{
                margin-right: 3rem;
            }
            .file-last-modified{
                margin-right: 3rem;
            }
            .file-itemaction{
                margin-right: 8rem;
            }
        }
        .file-content-level-up{
          cursor: pointer;
          border: 1px solid #ebe9f1;
          border-radius: 0.428rem;
          font-size:25px;
          display: flex;
          align-items: center;
          padding-left: 3.5rem;
          .file-content{
            padding: 5px 10px 15px 30px;
            .content-wrapper{
                .file-name{
                  margin: 0;
                }
            }
          }
        }
        .file-content-folder,.file-content-file{
          cursor: pointer;
          margin: 10px 0;
          border: 1px solid #ebe9f1;
          border-radius: 0.428rem;
          font-size:17px;
          font-weight:600;
          padding-left: 3.5rem;
          .file-folder{
            display: flex;
            justify-content: space-between;
            align-items:center;
            padding: 10px 10px 10px 5px;
            .file-folder-name{
              i{
                font-size: 22px;
                padding-right: 25px;
              }
              width: 200px;
            }
            .file-folder-left{
              min-width: 360px;
              .file-folder-size{
                  margin-right: 3rem;
              }
              .file-folder-type{
                  margin-right: 3rem;
              }
              .file-folder-date{
                  margin-right: 1rem;
              }
            }
          }
          .file-file{
            display: flex;
            justify-content: space-between;
            align-items:center;
            padding: 10px 10px 10px 5px;
            .file-file-name{
              display: flex;
              align-items: center;
              i{
                font-size: 22px;
                padding-right:25px;
              }
              div{
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 400px;
              }
            }
            .file-file-left{
              min-width: 360px;
              .file-file-size{
                  margin-right: 3rem;
              }
              .file-file-type{
                  margin-right: 3rem;
              }
              .file-file-date{
                  margin-right: 1rem;
              }
            }
          }
        }
    }   
</style>
