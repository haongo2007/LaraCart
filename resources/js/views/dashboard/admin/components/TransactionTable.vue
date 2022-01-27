<template>
  <el-table
    v-loading="loading"
    :data="list"
    style="width: 100%"
  >
    <el-table-column fixed label="Order #" width="70">
      <template slot-scope="scope">
        {{ scope.row && scope.row.id | orderNoFilter }}
      </template>
    </el-table-column>
    <el-table-column label="Customer" width="100">
      <template slot-scope="scope">
        {{ scope.row && scope.row.first_name }} {{ scope.row && scope.row.last_name }}
      </template>
    </el-table-column>
    <el-table-column label="Country" width="100" align="center">
      <template slot-scope="scope">
        {{ scope.row && scope.row.country }}
      </template>
    </el-table-column>
    <el-table-column label="Address" width="250">
      <template slot-scope="scope">
        {{ scope.row && scope.row.address1 }} {{ scope.row && scope.row.address2 }} {{ scope.row && scope.row.address3 }}
      </template>
    </el-table-column>
    <el-table-column label="Phone" width="195" align="center">
      <template slot-scope="scope">
        {{ scope.row && scope.row.phone | formatPhone }}
      </template>
    </el-table-column>
    <el-table-column label="Email" width="195" align="center">
      <template slot-scope="scope">
        {{ scope.row && scope.row.email }}
      </template>
    </el-table-column>
    <el-table-column label="Price" width="195" align="center">
      <template slot-scope="scope">
        {{ scope.row && scope.row.total | toThousandFilter }} {{ scope.row && scope.row.currency }}
      </template>
    </el-table-column>
    <el-table-column fixed="right" label="Status" width="100" align="center">
      <template slot-scope="scope">
        <el-tag>
          New
        </el-tag>
      </template>
    </el-table-column>
    <el-table-column fixed="right" label="Action" width="120">
      <template slot-scope="scope">
        <el-button type="text" size="small" @click="$router.push({ name: 'OrderEdit',params:{id:scope.row.id} })">Detail</el-button>
      </template>
    </el-table-column>
  </el-table>
</template>

<script>
import { orders } from '@/api/dashboard';

export default {
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
