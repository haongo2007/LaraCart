<template>
  <user-detail :is-edit="true" :data-temp="temp"/>
</template>

<script>

import UserDetail from './components/UserDetail';
import UserResource from '@/api/user';

const userResource = new UserResource();

const defaultForm = {
  id: '',
  fullname: '',
  email: '',
  password: '',
  permissions: [],
  roles: [],
  stores:[],
  phone:'',
};

export default {
  name: 'UserEdit',
  components: { UserDetail },
  data() {
    return {
      temp: Object.assign({}, defaultForm),
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchUser(id);
  },
  methods: {
    fetchUser(id){
      userResource.get(id).then(({ data } = response) => {
        this.temp.id = data.id;
        this.temp.fullname = data.fullname;
        this.temp.email = data.email;
        this.temp.phone = data.phone;
        this.temp.stores = this.filterData(data.store);
        this.temp.roles = this.filterData(data.roles);
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
