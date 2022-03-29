<template>
  <el-form ref="dataForm" :model="temp" class="form-config-container" style="width: 100%;">
    <el-row :gutter="20" style="margin:0px">
      <el-col :span="12" style="padding: 0;">
        <el-descriptions class="margin-top" title="Config Product" :column="1" border>
          <el-descriptions-item v-for="(item,index) in dataConfig.emailConfig.email_action" :key="index">
            <template slot="label" v-html="item.detail">
              {{ item.detail }}
            </template>
            <el-popover
              v-if="item.key == 'product_tax'"
              v-model="visible"
              placement="top"
              :title="item.detail"
              width="200"
            >
              <el-form-item
                prop="admin_title"
                :rules="[
                  { required: true, message: item.detail+' is required'},
                ]"
              >
                <el-select v-model="temp.tax" placeholder="Select" filterable style="width: 100%;">
                  <el-option
                    v-for="(item,index) in dataConfig.taxs"
                    :key="index"
                    :label="item"
                    :value="index"
                  />
                </el-select>
              </el-form-item>
              <div style="text-align: right; margin: 12px 0px 0px 0px">
                <el-button-group>
                  <el-button type="danger" size="mini">cancel</el-button>
                  <el-button type="primary" size="mini" :loading="btnLoading" >Confirm</el-button>
                </el-button-group>
              </div>
              <span slot="reference" class="border-edit">{{ dataConfig.taxs[item.value] }}</span>
            </el-popover>
            <el-switch v-else
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
        <el-descriptions class="margin-top" title="Config Product" :column="1" border>
          <el-descriptions-item v-for="(item,index) in dataConfig.emailConfig.smtp_config" :key="index" :label="item.detail">
            <el-popover
              v-if="item.key == 'product_tax'"
              v-model="visible"
              placement="top"
              :title="item.detail"
              width="200"
            >
              <el-form-item
                prop="admin_title"
                :rules="[
                  { required: true, message: item.detail+' is required'},
                ]"
              >
                <el-select v-model="temp.tax" placeholder="Select" filterable style="width: 100%;">
                  <el-option
                    v-for="(item,index) in dataConfig.taxs"
                    :key="index"
                    :label="item"
                    :value="index"
                  />
                </el-select>
              </el-form-item>
              <div style="text-align: right; margin: 12px 0px 0px 0px">
                <el-button-group>
                  <el-button type="danger" size="mini">cancel</el-button>
                  <el-button type="primary" size="mini" :loading="btnLoading" >Confirm</el-button>
                </el-button-group>
              </div>
              <span slot="reference" class="border-edit">{{ dataConfig.taxs[item.value] }}</span>
            </el-popover>
            <el-switch v-else
              v-model="item.value"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-value="1"
              inactive-value="0">
            </el-switch>
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
      temp:{}
  	};
  },
  created(){
    console.log(this.dataConfig);
  },
  methods: {
    
  },
};
</script>
