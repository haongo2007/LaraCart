<template>
  <role-detail :is-edit="false" :data-temp="temp"/>
</template>

<script>

import RoleDetail from './components/RoleDetail';
import RoleResource from '@/api/role';

const roleResource = new RoleResource();

const defaultForm = {
  name: '',
  slug:'',
  permissions:[],
};

export default {
  name: 'RoleEdit',
  components: { RoleDetail },
  data() {
    return {
      temp: Object.assign({}, defaultForm),
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchRole(id);
  },
  methods: {
    fetchRole(id){
      roleResource.get(id).then(({ data } = response) => {
        this.temp.name = data.name;
        this.temp.slug = data.slug;
        this.temp.permissions = this.filterData(data.permissions);
      });
    },
    filterData(data){
      let res = [];
      data.forEach(function (e,i) {
        res.push(e.id);
      });
      return res;
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
  .image-uploading{
    position: relative;
    .el-icon-close{
        cursor: pointer;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 18px;
        opacity: 0;
        transition:all .5s;
    }
    &:hover {
      .el-icon-close{
        opacity: 1;
      }
    }
  }
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    vertical-align: bottom;
  }
  .el-main-form{
    height: calc(100vh - 142px);
    overflow-y: scroll;
  }
</style>
