<template>
  <el-row class="el-main-form" :gutter="20" style="margin:0px;">
    <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
      <el-page-header :content="$t('route.'+this.$route.meta.title)+(this.$route.params.id ? ' - ' + this.$route.params.id : '' )" @back="goBackList"/>
      <el-button-group>
        <el-button type="primary" icon="el-icon-check" v-on:click="saveDesign"/>
        <el-button type="success" v-on:click="exportHtml"><svg-icon icon-class="excel" /></el-button>
      </el-button-group>
    </div>
    <el-col :span="24" style="height: calc(100vh - 200px);">
      <EmailEditor
        ref="emailEditor"
        v-on:load="editorLoaded"
        v-on:ready="editorReady"
      />
    </el-col>
  </el-row>
</template>

<script>

const defaultForm = {
};

import EmailEditor from '@/components/PageEditor';
import sample from '@/components/PageEditor/sample.json';

export default {
  name: 'PageCreate',
  components: {
    EmailEditor,
  },
  data() {
    return {

    };
  },
  created() {
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'PageList' });
    },
    // called when the editor is created
    editorLoaded() {
      console.log('editorLoaded');
      // Pass the template JSON here
      this.$refs.emailEditor.editor.loadDesign(sample);
    },
    // called when the editor has finished loading
    editorReady() {
      console.log('editorReady');
    },
    saveDesign() {
      this.$refs.emailEditor.editor.saveDesign((design) => {
        console.log('saveDesign', design);
      });
    },
    exportHtml() {
      this.$refs.emailEditor.editor.exportHtml((data) => {
        console.log('exportHtml', data);
      });
    },
  }
};
</script>
<style type="text/css">
  .unlayer-editor{
    height: 100%;
  }
</style>