<template>
  <permission-detail :is-edit="false" :data-temp="temp"/>
</template>

<script>

import PermissionDetail from './components/PermissionDetail';

export default {
  name: 'PermissionCreate',
  components: { PermissionDetail },
  data() {
    return {
      temp: {
        id: null,
        name: '',
        slug: '',
        http_uri:[],
      },
    };
  },
  created() {

  },
  methods: {
    
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
