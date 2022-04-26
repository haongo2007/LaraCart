<template>
  <div class="block-tables">
    <div class="el-descriptions__header">
      <div class="el-descriptions__title">History Order #{{ this.$route.params.id }}</div>
    </div>
    <el-table
      :data="dataHistory.filter(data => !search || data.content.toLowerCase().includes(search.toLowerCase()) || data.staff.fullname.toLowerCase().includes(search.toLowerCase()) )"
      height="295"
      style="width: 100%"
    >
      <el-table-column
        label="Staff"
        min-width="180"
      >
        <template slot-scope="scope">
          <span v-html="scope.row.staff.fullname" />
        </template>
      </el-table-column>
      <el-table-column
        label="Content"
        min-width="450"
      >
        <template slot-scope="scope">
          <span v-html="scope.row.content" />
        </template>
      </el-table-column>
      <el-table-column
        label="Created at"
        min-width="150"
      >
        <template slot="header" slot-scope="scope">
          <el-input
            v-model="search"
            size="mini"
            placeholder="Type to search"
          />
        </template>
        <template slot-scope="scope">
          <span>{{ scope.row.add_date | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
export default {
  name: 'OrdersHistory',
  props: ['dataHistory'],
  data() {
    return {
      search: '',
    };
  },
};
</script>
