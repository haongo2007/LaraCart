<template>
  <el-table
    v-loading="loading"
    :data="list"
    max-height="530"
    style="width: 100%"
  >
    <el-table-column :label="$t('table.order_new')">
      <el-table-column fixed :label="$t('table.id')" width="70">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id | orderNoFilter }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.customer')" width="120">
        <template slot-scope="scope">
          {{ scope.row && scope.row.first_name }} {{ scope.row && scope.row.last_name }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.money')" width="195" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.total | toThousandFilter }} {{ scope.row && scope.row.currency }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.country')" width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.country }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.address')" width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.address1 }} {{ scope.row && scope.row.address2 }} {{ scope.row && scope.row.address3 }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.phone')" width="195" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.phone | formatPhone }}
        </template>
      </el-table-column>
      <el-table-column :label="$t('table.email')" width="195" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.email }}
        </template>
      </el-table-column>
      <el-table-column fixed="right" :label="$t('table.status')" width="100" align="center">
        <template slot-scope="scope">
          <el-tag>
            New
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column fixed="right" :label="$t('table.actions')" width="120" v-permission="['edit.order']">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="$router.push({ name: 'OrderEdit',params:{id:scope.row.id} })">Detail</el-button>
        </template>
      </el-table-column>
    </el-table-column>
  </el-table>
</template>

<script>
import { orders } from '@/api/dashboard';
import permission from '@/directive/permission'; // Permission directive (v-permission)

export default {
  directives:{ permission },
  filters: {
    orderNoFilter(str) {
      return '#' + str;
    },
  },
  data() {
    return {
      list: [],
      loading: true,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      const { data } = await orders();
      this.list = data.items.slice(0, 8);
      this.loading = false;
    },
  },
};
</script>
