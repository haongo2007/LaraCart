
<template>
  <div class="drawer-container">
    <div>
      <h3 class="drawer-title">
        {{ $t('settings.title') }}
      </h3>

      <div class="drawer-item">
          <el-row :gutter="20">
            <el-col :span="3">
              <el-button-group style="width: 100%;">
                <el-button v-waves style="width: 33.3%;" type="success" :disabled="loading" @click="handleDownload"><svg-icon icon-class="excel" /></el-button>
                <el-button style="width: 33.3%;" type="danger" icon="el-icon-delete" :disabled="multiSelectRow.length == 0 ? true : false" @click="handerDeleteAll" />
                <el-dropdown trigger="click" style="width: 33.3%;" placement="top-start" split-button type="primary" @command="handleCommand" @click="$router.push({ name: 'ProductCreateSingle'})">
                  <i class="el-icon-plus" />
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item command="ProductCreateBundle">Product Bundle</el-dropdown-item>
                    <el-dropdown-item command="ProductCreateGroup">Product Group</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>
              </el-button-group>
            </el-col>
            <el-col :span="9">
              <el-date-picker
                v-model="arDateToSearch"
                style="width: 100%;display: flex;justify-content: space-between;"
                type="datetimerange"
                align="right"
                unlink-panels
                range-separator="To"
                start-placeholder="Created date"
                end-placeholder="End date"
                :picker-options="pickerOptions"
                @change="handleFilterDate()"
              />
            </el-col>
            <el-col :span="12" style="display: flex;justify-content: space-between;">
              <el-input v-model="listQuery.keyword" clearable placeholder="Typing for search name, Sku, Description or keyword" class="filter-item" style="width: 100%;margin-right: 10px;" @keyup.enter.native="handleFilter" />
              <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
                {{ $t('table.search') }}
              </el-button>
            </el-col>
          </el-row>

          <el-row :gutter="20">
            <el-col :span="2">
              <el-tooltip class="item" effect="dark" content="Choose value needed filter" placement="bottom-start">
                <el-radio-group v-model="listQuery.filter_price_by" style="width: 100%;" @change="handleFilterPriceSlide">
                  <el-radio-button label="Cost" />
                  <el-radio-button label="Price" />
                </el-radio-group>
              </el-tooltip>
            </el-col>
            <el-col :span="10">
              <el-tooltip class="item" effect="dark" content="Filter price with slider" placement="bottom-start">
                <el-slider
                  v-model="listQuery.price"
                  range
                  show-stops
                  :step="stepPrice"
                  :format-tooltip="formatTooltip"
                  :max="maxPrice"
                  @change="handleFilter"
                />
              </el-tooltip>
            </el-col>
            <el-col :span="4">
              <el-cascader
                v-model="listQuery.category"
                style="width: 100%;"
                placeholder="Category"
                :options="listRecursive"
                :props="cateRecurProps"
                collapse-tags
                clearable
                @change="handleFilter"
              />
            </el-col>
            <el-col :span="4">
              <el-select v-model="listQuery.status" style="width: 100%" :placeholder="$t('table.status')" class="filter-item" clearable multiple @change="handleFilter">
                <el-option v-for="item in fillterStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
              </el-select>
            </el-col>
            <el-col :span="4">
              <el-select v-model="listQuery.sort_order" style="width: 100%" :placeholder="$t('table.order')" class="filter-item" @change="handleFilter">
                <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" :disabled="item.active" />
              </el-select>
            </el-col>
          </el-row>
      </div>
    </div>
  </div>
</template>

<script>
import ThemePicker from '@/components/ThemePicker';

export default {
  components: { ThemePicker },
  data() {
    return {};
  },
  computed: {
    fixedHeader: {
      get() {
        return this.$store.state.settings.fixedHeader;
      },
      set(val) {
        this.$store.dispatch('settings/changeSetting', {
          key: 'fixedHeader',
          value: val,
        });
      },
    },
    tagsView: {
      get() {
        return this.$store.state.settings.tagsView;
      },
      set(val) {
        this.$store.dispatch('settings/changeSetting', {
          key: 'tagsView',
          value: val,
        });
      },
    },
    sidebarLogo: {
      get() {
        return this.$store.state.settings.sidebarLogo;
      },
      set(val) {
        this.$store.dispatch('settings/changeSetting', {
          key: 'sidebarLogo',
          value: val,
        });
      },
    },
  },
  methods: {
    themeChange(val) {
      this.$store.dispatch('settings/changeSetting', {
        key: 'theme',
        value: val,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.drawer-container {
  padding: 24px;
  font-size: 14px;
  line-height: 1.5;
  word-wrap: break-word;

  .drawer-title {
    margin-bottom: 12px;
    color: rgba(0, 0, 0, .85);
    font-size: 14px;
    line-height: 22px;
  }

  .drawer-item {
    color: rgba(0, 0, 0, .65);
    font-size: 14px;
    padding: 12px 0;
  }

  .drawer-switch {
    float: right
  }
}
</style>
