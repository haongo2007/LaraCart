<template>
  <div>
    <el-row>
      <el-col :span="16" :offset="4">
        <el-form-item :label="$t('table.sort')" prop="sort">
          <el-input-number v-model.number="temp.sort" style="width: 100%;" :min="1" :controls="false" />
        </el-form-item>

        <el-form-item :label="$t('table.url_config')" prop="url_config">
          <el-input
            v-model="temp.url_config"
            placeholder="Please input"
            clearable
          />
        </el-form-item>

        <el-form-item :label="$t('table.top')" prop="top">
          <el-tooltip :content="'Switch value: ' + temp.top" placement="top">
            <el-switch
              v-model="temp.top"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0"
            />
          </el-tooltip>
        </el-form-item>

        <el-form-item :label="$t('table.status')" prop="status">
          <el-tooltip :content="'Switch value: ' + temp.status" placement="top">
            <el-switch
              v-model="temp.status"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0"
            />
          </el-tooltip>
        </el-form-item>
      </el-col>
    </el-row>
  </div>
</template>

<script>

const defaultForm = {
  sort: 1,
  top: '1',
  status: '1',
  url_config: '',
};

export default {
  name: 'ActionUpload',
  data() {
    return {
      temp: Object.assign({}, defaultForm),
    };
  },
  created() {
  },
  methods: {
  },
};
</script>
