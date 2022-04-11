<template>
  <el-form ref="dataForm" class="form-config-container" style="width: 100%;">
    <el-row :gutter="20" style="margin:0px">
      <el-col :span="12" style="padding: 0;">
        <el-descriptions class="margin-top" title="Config Mode" :column="1" border>
          <el-descriptions-item v-for="(item,index) in dataConfig.emailConfig.email_action" :key="index">
            <template slot="label">
              <span v-html="item.detail"></span>
            </template>
            <el-switch
              @change="handleValue(item)"
              v-model="item.value"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0">
            </el-switch>
          </el-descriptions-item>
        </el-descriptions>
      </el-col>
      <el-col :span="12"  style="padding: 0;">
        <el-descriptions class="margin-top" title="Config SMTP" :column="1" border>
          <el-descriptions-item v-for="(item,index) in dataConfig.emailConfig.smtp_config" :key="index" :label="item.detail">

            <el-popover
              v-model="visible[index]"
              placement="top"
              :title="item.detail"
              width="200">

              <el-form-item prop="admin_title" v-if="item.key == 'smtp_security'">
                <el-select v-model="item.value" placeholder="Select" filterable style="width: 100%;">
                  <el-option
                    v-for="(scure,key) in dataConfig.smtp_method"
                    :key="key"
                    :label="scure"
                    :value="key"
                  />
                </el-select>
              </el-form-item>

              <el-form-item prop="admin_title" v-else>
                <el-input v-model="item.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(item,index)" />
              </el-form-item>

              <div style="text-align: right; margin: 12px 0px 0px 0px">
                <el-button-group>
                  <el-button type="danger" size="mini" @click="handleCancel(index)">cancel</el-button>
                  <el-button type="primary" size="mini" @click="handleConfirm(item,index)">Confirm</el-button>
                </el-button-group>
              </div>
              <span slot="reference" class="border-edit">{{ item.value ? item.value : 'Empty' }}</span>
            </el-popover>

          </el-descriptions-item>
        </el-descriptions>
      </el-col>
    </el-row>
  </el-form>
</template>

<script>

export default {
  name: 'ConfigEmail',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      visible:{}
  	};
  },
  created(){
    for(let vsb in this.dataConfig.emailConfig.smtp_config){
      this.$set(this.visible,vsb, false);
    }
  },
  methods: {
    handleValue(item){
      this.$emit('handleUpdate', item);
    },
    handleCancel(i){
      this.visible[i] = false;
    },
    handleConfirm(item,i){
      this.visible[i] = false;
      this.$emit('handleUpdate', item);
    }
  },
};
</script>
