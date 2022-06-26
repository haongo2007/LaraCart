<template>
  <el-table
    v-loading="loading"
    :data="list"
    style="width: 100%"
  >
  <el-page-header content="detail"></el-page-header>

    <el-table-column :label="$t('table.product_utstock')">
      <el-table-column :label="$t('table.id')" min-width="50" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.store')" min-width="150" v-if="checkOnlyStore">
        <template slot-scope="scope">
          <el-tag type="success">
            <i class="el-icon-s-shop"></i>
            {{ scope.row.store.descriptions_current_lang[0].title && scope.row.store.descriptions_current_lang[0].title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.sku')" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.sku }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.name')" min-width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.name }}
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-view" class="filter-item" @click="renderRouterEdit(row.kind,row.id)" 
            v-permission="['edit'+renderKind(row.kind)+'product']"/>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table-column>
  </el-table>
</template>

<script>
import { productOutStock } from '@/api/dashboard';
import { checkOnlyStore } from '@/utils';
import permission from '@/directive/permission'; // Permission directive (v-permission)

export default {
  directives:{ permission },
  data() {
    return {
      list: [],
      loading: true,
    };
  },
  created() {
    this.fetchData();
  },
  computed:{
    checkOnlyStore
  },
  methods: {
    renderRouterEdit(kind, prid){
      var rou = 'ProductEditSingle';
      if (kind == 1) {
        rou = 'ProductEditBundle';
      } else if (kind == 2) {
        rou = 'ProductEditGroup';
      }
      this.$router.push({ name: rou, params: { id: prid }});
    },
    renderKind(kind){
      let kinds = {
            0: '.single.',
            1: '.bundle.',
            2: '.group.',
          };
      return kinds[kind];
    },
    async fetchData() {
      const { data } = await productOutStock();
      this.list = data.items.slice(0, 8);
      this.loading = false;
    },
  },
};
</script>
