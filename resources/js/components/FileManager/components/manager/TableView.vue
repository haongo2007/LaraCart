<template>
    <div class="el-table el-table--fit el-table--enable-row-hover el-table--enable-row-transition">
        <div class="el-table__header-wrapper">
            <table class="el-table__header" style="width:100%;">
                <thead class="has-gutter">
                    <tr class="">
                        <th class="el-table_12_column_52 ascending is-leaf is-sortable" v-on:click="sortBy('name')">
                            <div class="cell sort-name-file">
                                {{ lang.manager.table.name }}
                                <template v-if="sortSettings.field === 'name'">
                                    <span class="caret-wrapper">
                                        <i class="sort-caret ascending"
                                           v-show="sortSettings.direction === 'down'"></i>
                                        <i class="sort-caret descending"
                                           v-show="sortSettings.direction === 'up'"></i>
                                    </span>
                                </template>
                            </div>
                        </th>
                        <th class="el-table_12_column_52 ascending is-leaf is-sortable">
                            <div class="cell">
                                {{ lang.manager.table.size }}
                                <template v-if="sortSettings.field === 'size'">
                                    <span class="caret-wrapper">
                                        <i class="sort-caret ascending"
                                           v-show="sortSettings.direction === 'down'"></i>
                                        <i class="sort-caret descending"
                                           v-show="sortSettings.direction === 'up'"></i>
                                    </span>
                                </template>
                            </div>
                        </th>
                        <th class="el-table_12_column_52 ascending is-leaf is-sortable">
                            <div class="cell">
                                {{ lang.manager.table.type }}
                                <template v-if="sortSettings.field === 'type'">
                                    <span class="caret-wrapper">
                                        <i class="sort-caret ascending"
                                           v-show="sortSettings.direction === 'down'"></i>
                                        <i class="sort-caret descending"
                                           v-show="sortSettings.direction === 'up'"></i>
                                    </span>
                                </template>
                            </div>
                        </th>
                        <th class="el-table_12_column_52 ascending is-leaf is-sortable">
                            <div class="cell">
                                {{ lang.manager.table.date }}
                                <template v-if="sortSettings.field === 'date'">
                                    <span class="caret-wrapper">
                                        <i class="sort-caret ascending"
                                           v-show="sortSettings.direction === 'down'"></i>
                                        <i class="sort-caret descending"
                                           v-show="sortSettings.direction === 'up'"></i>
                                    </span>
                                </template>
                            </div>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="el-table__body-wrapper is-scrolling-none">
            <table class="el-table__body" style="width:100%;">
                <tbody>
                     <tr v-if="!isRootPath" class="el-table__row" >
                        <td class="el-table_12_column_52" v-on:click="levelUp">
                            <div class="cell">
                                <i class="fas fa-level-up-alt"></i>
                            </div>
                        </td>
                    </tr>
                    <tr class="el-table__row" 
                        v-for="(directory, index) in directories" 
                        v-bind:key="`d-${index}`"
                        v-bind:class="{'table-info': checkSelect('directories', directory.path)}"
                        v-on:click="selectItem('directories', directory.path, $event)"
                        v-on:contextmenu.prevent="contextMenu(directory, $event)">
                        <td class="el-table_12_column_52"
                            v-bind:class="(acl && directory.acl === 0) ? 'text-hidden' : ''"
                            v-on:dblclick="selectDirectory(directory.path)">
                            <div class="cell sort-name-file">
                                <i class="far fa-folder"></i>
                                <span>{{ directory.basename }}</span>
                            </div>
                        </td>
                        <td></td>
                        <td class="el-table_12_column_52">
                            <div class="cell">
                                {{ lang.manager.table.folder }}
                            </div>
                        </td>
                        <td class="el-table_12_column_52">
                            <div class="cell">
                                {{ timestampToDate(directory.timestamp) }}
                            </div>
                        </td>
                    </tr>
                    <tr class="el-table__row" 
                        v-for="(file, index) in files"
                        v-bind:key="`f-${index}`"
                        v-bind:class="{'table-info': checkSelect('files', file.path)}"
                        v-on:click="selectItem('files', file.path, $event)"
                        v-on:dblclick="selectAction(file.path, file.extension)"
                        v-on:contextmenu.prevent="contextMenu(file, $event)">
                        <td class="fm-content-item unselectable"
                            v-bind:class="(acl && file.acl === 0) ? 'text-hidden' : ''">
                            <div class="cell sort-name-file">
                                <i class="far" v-bind:class="extensionToIcon(file.extension)"></i>
                                <span>{{ file.filename ? file.filename : file.basename }}</span>
                            </div>
                        </td>
                        <td>{{ bytesToHuman(file.size) }}</td>
                        <td class="el-table_12_column_52">
                            <div class="cell">
                                {{ file.extension }}
                            </div>
                        </td>
                        <td class="el-table_12_column_52">
                            <div class="cell">
                                {{ timestampToDate(file.timestamp) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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
      this.$store.dispatch(`fm/${this.manager}/sortBy`, { field, direction: null });
    },
  },
};
</script>