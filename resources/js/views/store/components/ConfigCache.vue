<template>
  <el-form ref="dataForm" class="form-config-container">
    <div class="margin-top el-descriptions">
      <div class="el-descriptions__header">
        <div class="el-descriptions__title">{{$t('store.cache_config')}}</div>
        <div class="el-descriptions__extra"></div>
      </div>
      <div class="el-descriptions__body">
        <table class="el-descriptions__table is-bordered el-descriptions--medium">
           <tbody>
              <tr class="el-descriptions-row">
                 <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('table.fieldConfig') }}</th>
                 <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('table.status') }}</th>
                 <th colspan="1" class="el-descriptions-item__cell el-descriptions-item__label is-bordered-label ">{{ $t('store.clear') }}</th>
              </tr>
              <tr class="el-descriptions-row" v-for="(item,index) in dataConfig.cacheConfig" :key="index">
                 <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content">
                   {{ item.detail }}
                 </td>
                 <td :colspan="index == 'cache_time' ? 2 : 1" v-if="index == 'cache_time'" class="el-descriptions-item__cell el-descriptions-item__content">
                    <el-popover
                      v-model="visible"
                      placement="top"
                      :title="item.detail"
                      width="200">

                      <el-form-item prop="admin_title">
                        <el-input v-model="item.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(item,index)" />
                      </el-form-item>

                      <div style="text-align: right; margin: 12px 0px 0px 0px">
                        <el-button-group>
                          <el-button type="danger" size="mini" @click="handleCancel(index)">cancel</el-button>
                          <el-button :loading="btnLoading" type="primary" size="mini" @click="handleConfirm(item,index)">Confirm</el-button>
                        </el-button-group>
                      </div>
                      <span slot="reference" class="border-edit">{{ item.value ? item.value : 'Empty' }}</span>
                    </el-popover>
                 </td>
                 <td colspan="1" v-else class="el-descriptions-item__cell el-descriptions-item__content">
                    <el-switch
                      @change="handleValue(item)"
                      v-model="item.value"
                      active-color="#13ce66"
                      inactive-color="#ff4949"
                      active-value="1"
                      inactive-value="0">
                    </el-switch>
                  </td>
                 <td colspan="1" class="el-descriptions-item__cell el-descriptions-item__content" v-if="index != 'cache_time' && index != 'cache_status'">
                    <el-button type="danger" :loading="btnLoading" icon="el-icon-refresh" size="mini" v-if="item.value" 
                    :disabled="item.hasOwnProperty('disabled')" 
                    @click="handleClearCache(index)"/>
                 </td>
              </tr>
           </tbody>
        </table>
      </div>
    </div>
  </el-form>
</template>

<script>
import { clearCache } from '@/api/cache-config';

export default {
  name: 'ConfigCache',
  props: {
    dataConfig: {
      type: Object,
      default: false,
    },
  },
  data(){
    return {
      visible:false,
      btnLoading:false,
  	};
  },
  created(){
  },
  methods: {
    async handleClearCache(i){
      this.btnLoading = true;
      const {data} = await clearCache({action:i,store_id:this.dataConfig.store_id});
      this.btnLoading = false;
      if (data.error == 0) {
        this.$set(this.dataConfig.cacheConfig[i],'disabled', true);
        this.$message({
          type: 'success',
          message: data.msg,
        });
      }else{
        this.$message({
          type: 'error',
          message: data.msg,
        });
      }
    },
    handleCancel(i){
      this.visible = false;
    },
    handleConfirm(i,key){
      this.btnLoading = true;
      let data = this.dataConfig.cacheConfig[key];
      this.$emit('handleUpdate', data);
      this.btnLoading = false;
      this.visible = false;
    },
    handleValue(item){
      this.$emit('handleUpdate', item);
    }
  },
};
</script>
