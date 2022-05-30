<template>
  <div class="json-editor">
    <textarea ref="textarea" />
  </div>
</template>

<script>
require('script-loader!jsonlint');
import CodeMirror from 'codemirror';
import 'codemirror/addon/lint/lint.css';
import 'codemirror/lib/codemirror.css';
import 'codemirror/theme/monokai.css';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/addon/lint/lint';
import 'codemirror/addon/lint/json-lint';

export default {
  name: 'JsonEditor',
  /* eslint-disable vue/require-prop-types */
  props: ['value','type'],
  data() {
    return {
      jsonEditor: false,
    };
  },
  watch: {
    value(value) {
      const editorValue = this.jsonEditor.getValue();
      if (value !== editorValue) {
        this.jsonEditor.setValue(this.value);
      }
    },
  },
  mounted() {
    let conf = {
      lineNumbers: true,
      gutters: ['CodeMirror-lint-markers'],
      theme: 'monokai',
      lint: true,
    };
    if (this.type == 'html') {
      conf['mode'] = 'htmlmixed';
    }else{
      conf['mode'] = 'application/json';
    }
    this.jsonEditor = CodeMirror.fromTextArea(this.$refs.textarea, conf);
    this.jsonEditor.setValue(this.value);
    this.jsonEditor.on('change', cm => {
      this.$emit('changed', cm.getValue());
      this.$emit('input', cm.getValue());
    });
  },
  methods: {
    getValue() {
      return this.jsonEditor.getValue();
    },
  },
};
</script>

<style scoped>
.json-editor{
  height: 100%;
  position: relative;
}
.json-editor >>> .CodeMirror {
  height: auto;
  min-height: 300px;
}
.json-editor >>> .CodeMirror-scroll{
  min-height: 300px;
}
.json-editor >>> .cm-s-rubyblue span.cm-string {
  color: #F08047;
}
</style>
