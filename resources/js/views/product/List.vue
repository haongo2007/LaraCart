<template>
  <div class="app-container">
    <div v-loading="loading" class="filter-container">
      <right-panel :button-top="'20%'" :iCon="'fa-filter'">
      </right-panel>
    </div>

    <el-table
      v-loading="loading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
      @selection-change="handleSelectionAllChange"
    >
      <el-table-column
        type="selection"
        align="center"
        width="55"
      />
      <el-table-column fixed label="#ID" min-width="50" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.id }}
        </template>
      </el-table-column>

      <el-table-column label="Image" min-width="150" align="center">
        <template slot-scope="scope">
          <el-image :src="scope.row.image+'&w=100'">
            <div slot="error" class="image-slot">
              <i class="el-icon-picture-outline" />
            </div>
          </el-image>
        </template>
      </el-table-column>

      <el-table-column label="Sku" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.sku }}
        </template>
      </el-table-column>

      <el-table-column label="Name" min-width="250">
        <template slot-scope="scope">
          {{ scope.row && scope.row.name }}
        </template>
      </el-table-column>

      <el-table-column label="Category" min-width="200">
        <template slot-scope="{row}">
          <el-tag v-for="item in row.categories" :key="item.id" style="margin: 2px;">
            {{ item.descriptions_with_lang_default.title }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column label="Cost" min-width="200">
        <template slot-scope="scope">
          {{ scope.row && scope.row.cost | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column label="Price" min-width="150" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.price | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column label="Type" min-width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row && scope.row.property }}
        </template>
      </el-table-column>

      <el-table-column label="Kind" min-min-width="150" align="center">
        <template slot-scope="{row}">
          <el-tag :type="row.kind | kindFilter">
            {{ row.kind | kindFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.status')" class-name="status-col" width="100" prop="status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status | statusFilter(true) }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column label="Created at" min-width="195" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('table.actions')" align="center" min-width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button-group>
            <el-button type="primary" size="mini" icon="el-icon-edit" class="filter-item" @click="renderRouterEdit(row.kind,row.id)" />
            <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDeleting(row)" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import RightPanel from '@/components/RightPanel';
import { parseTime, statusFilter, kindFilter, toThousandFilter } from '@/filters';
import ProductResource from '@/api/product';
import CategoryResource from '@/api/category';
import Pagination from '@/components/Pagination';
import waves from '@/directive/waves'; // Waves directive

const productResource = new ProductResource();
const categoryResource = new CategoryResource();
var statusMap = null;
export default {
  name: 'ProductList',
  components: { Pagination,RightPanel },
  directives: { waves },
  data() {
    return {
      list: null,
      total: 0,
      loading: true,
      downloadLoading: false,
      arDateToSearch: [],
      maxPrice: null,
      stepPrice: 2,
      listQuery: {
        page: 1,
        limit: 20,
        from: '',
        to: '',
        price: null,
        filter_price_by: 'Cost',
        sort_order: 'id__desc',
        status: ['1'],
        category: null,
        keyword: '',
      },
      fillterStatusOptions: [{
        label: 'Deactive',
        key: '0',
        active: false,
      }, {
        label: 'Active',
        key: '1',
        active: true,
      }],
      sortOptions: [{
        label: 'Id DESC',
        key: 'id__desc',
        active: true,
      }, {
        label: 'Id ASC',
        key: 'id__asc',
        active: false,
      }, {
        label: 'Created DESC',
        key: 'created_at__desc',
        active: false,
      }, {
        label: 'Created ASC',
        key: 'created_at__asc',
        active: false,
      }, {
        label: 'Name Z-A',
        key: 'name__desc',
        active: false,
      }, {
        label: 'Name A-Z',
        key: 'name__asc',
        active: false,
      }, {
        label: 'Sku Z-A',
        key: 'sku__desc',
        active: false,
      }, {
        label: 'Sku A-Z',
        key: 'sku__asc',
        active: false,
      }, {
        label: 'Price DESC',
        key: 'price__desc',
        active: false,
      }, {
        label: 'Price ASC',
        key: 'price__asc',
        active: false,
      }],
      listRecursive: [],
      cateRecurProps: {
        children: 'children',
        label: 'title',
        value: 'id',
        multiple: true,
      },
      CategoryOptions: [],
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          },
        }],
      },
      multiSelectRow: [],
    };
  },
  created() {
    this.getList();
    // this.getListCategory();
    // this.getMaxPrice(this.listQuery.filter_price_by);
  },
  methods: {
    getList() {
      const data = productResource.list(this.listQuery).then((data) => {
        this.list = data.data;
        this.total = data.meta.total;
        this.loading = false;
      });
    },
    async getListCategory() {
      const { data } = await categoryResource.getRecursive();
      this.listRecursive = data;
    },
    async getMaxPrice(type){
      const { data } = await productResource.getMaxPriceProduct(type);
      this.listQuery.price[0] = 0;
      this.listQuery.price[1] = data.max;
      this.maxPrice = data.max;
      this.stepPrice = data.max / 10;
      this.getList();
    },
    handleDeleting(row, multiple = false) {
      this.$confirm('This will permanently delete the row. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.loading = true;
        if (multiple) {
          var id = [];
          row.map((item) => id.push(item.id));
        } else {
          var id = row.id;
        }
        var that = this;
        productResource.destroy(id).then((res) => {
          if (multiple) {
            row.forEach(function(v) {
              const index = that.list.indexOf(v);
              that.list.splice(index, 1);
            });
          } else {
            const index = that.list.indexOf(row);
            that.list.splice(index, 1);
          }
          this.$message({
            type: 'success',
            message: 'Delete successfully',
          });
          this.loading = false;
        }).catch(() => {
          this.loading = false;
        });
      }).catch(() => {
        this.loading = false;
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    handleFilterDate(){
      this.loading = true;
      if (this.arDateToSearch !== null && this.arDateToSearch.length > 0) {
        this.listQuery.from = parseTime(this.arDateToSearch[0], '{d}-{m}-{y} {h}:{i}:{s}');
        this.listQuery.to = parseTime(this.arDateToSearch[1], '{d}-{m}-{y} {h}:{i}:{s}');
      } else {
        this.listQuery.from = '';
        this.listQuery.to = '';
      }
      this.getList();
    },
    handleFilterPriceSlide(){
      this.getMaxPrice(this.listQuery.filter_price_by);
    },
    handleFilter(){
      this.getList();
    },
    handleSelectionAllChange(val){
      this.multiSelectRow = val;
    },
    handerDeleteAll(){
      this.handleDeleting(this.multiSelectRow, true);
    },
    handleDownload() {
      this.loading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Order#', 'Customer', 'Country', 'Address', 'Phone', 'Email', 'Subtotal', 'Ship fee', 'Discount', 'Total', 'Payment', 'Currency', 'Status', 'Created at'];
        const filterVal = ['id', 'first_name', 'country', 'address3', 'phone', 'email', 'subtotal', 'shipping', 'discount', 'total', 'payment_method', 'currency', 'status', 'created_at'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'Orders-list-' + parseTime(new Date(), '{y}-{m}-{d}'),
        });
        this.loading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      const getValue = (object, keys) => keys.split('.').reduce((o, k) => (o || {})[k], object);
      return jsonData.map(v => filterVal.map(j => {
        console.log(j);
        if (j === 'created_at') {
          return parseTime(v[j]);
        } else {
          return getValue(v, j);
        }
      }));
    },
    formatTooltip(val) {
      return toThousandFilter(val);
    },
    handleCommand(command) {
      this.$router.push({ name: command });
    },
    renderRouterEdit(kind, prid){
      var rou = 'ProductEditSingle';
      if (kind == 1) {
        rou = 'ProductEditBundle';
      } else if (kind == 2) {
        rou = 'ProductEditGroup';
      }
      this.$router.push({ name: rou, params: { id: prid }});
    },
  },
};
</script>
