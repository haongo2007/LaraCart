<template>
  <components v-if="!loading" :is="component" :is-edit="true" :data-temp="temp"/>
</template>

<script>

import PermissionDetail from './components/PermissionDetail';
import PermissionsResource from '@/api/permissions';

const permissionsResource = new PermissionsResource();

export default {
  name: 'PermissionEdit',
  components: { PermissionDetail },
  data() {
    return {
      temp: {
        id: null,
        name: '',
        slug: '',
        http_uri:[],
      },
      loading: true,
      component:'',
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchPermission(id);
  },
  methods: {
    fetchPermission(id){
      const loading = this.$loading({
        target: '.app-main',
      });
      permissionsResource.get(id).then(({ data } = response) => {
        this.temp.id  = data.id;
        this.temp.name = data.name;
        this.temp.slug = data.slug;
        this.temp.http_uri = data.http_uri.split(',');
        this.component = 'PermissionDetail';
        loading.close();
        this.loading = false;
      });
    }
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
