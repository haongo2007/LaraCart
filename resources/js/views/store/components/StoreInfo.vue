<template>
  <div class="el-main-form">
    <el-row :gutter="20" style="margin:0px;">
      <div style="padding: 24px;display: flex;justify-content: space-between;align-items: center;">
        <el-page-header :content="$t('route.'+this.$route.meta.title) + (this.$route.params.id ? ' - ' + this.$route.params.id : '' ) " @back="goBackList" />
      </div>
    </el-row>
    <el-row :gutter="20" style="margin:0px 0px 20px 0px;">
      <el-form ref="dataForm" :model="temp" class="form-container">
        <el-col :span="12">
          <el-skeleton :rows="6" animated :loading="dataLoading" />
          <div class="block-tables">
            <el-descriptions class="margin-top" :column="1" border>
              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-s-check" />
                  Logo
                </template>
                <div @click="handleVisibleStorage()">
                  <el-image 
                    style="width: 100px;cursor: pointer;"
                    :src="temp.logo ? temp.logo :'api/system/getFile?disk=store&path=logo.png&w=260'"
                    fit="contain">
                  </el-image>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-phone" />
                  Number Phone
                </template>
                <el-popover v-if="isEdit"
                  @hide="handleCancel(0,'phone')"
                  v-model="visible[0]"
                  placement="top"
                  title="Number phone"
                  width="200">
                  <el-form-item
                    prop="phone">
                    <el-input v-model="temp.phone" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(0,'phone')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(0)">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(0,'phone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.phone ? temp.phone : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="phone"
                  :rules="[
                    { required: true, message: 'Number Phone is required'},
                  ]">
                  <el-input v-model="temp.phone" placeholder="Please input" @keyup.enter.native="handleConfirm(0,'phone')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-phone-outline" />
                  Number Phone (Other)
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(1,'long_phone')"
                  v-model="visible[1]"
                  placement="top"
                  title="Number phone (other)"
                  width="200">
                  <el-form-item
                    prop="long_phone">
                    <el-input v-model="temp.long_phone" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(1,'long_phone')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(1,'long_phone')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'long_phone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.long_phone ? temp.long_phone : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="long_phone">
                  <el-input v-model="temp.long_phone" placeholder="Please input" @keyup.enter.native="handleConfirm(1,'long_phone')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="email" />
                  Email address
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(2,'email')"
                  v-model="visible[2]"
                  placement="top"
                  title="Email address"
                  width="200">
                  <el-form-item
                    prop="email">
                    <el-input v-model="temp.email" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'email')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(2,'email')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(2,'email')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.email ? temp.email : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="email"
                  :rules="[
                    { required: true, message: 'Email is required'},
                  ]">
                  <el-input v-model="temp.email" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'email')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-location" />
                  Address
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(3,'address')"
                  v-model="visible[3]"
                  placement="top"
                  title="Address"
                  width="200">
                  <el-form-item
                    prop="address">
                    <el-input v-model="temp.address" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'address')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(3,'address')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(3,'address')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.address ? temp.address : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="address">
                  <el-input v-model="temp.address" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'address')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="international" />
                  Domain
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(4,'domain')"
                  v-model="visible[4]"
                  placement="top"
                  title="Domain"
                  width="200">
                  <el-form-item
                    prop="domain">
                    <el-input v-model="temp.domain" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'domain')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(4,'domain')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(4,'domain')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.domain ? temp.domain : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="domain"
                  :rules="[
                    { required: true, message: 'Domain is required'},
                  ]">
                  <el-input v-model="temp.domain" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'domain')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="language" />
                  Default language
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(5,'language')"
                  v-model="visible[5]"
                  placement="top"
                  title="Default language"
                  width="200">
                  <el-form-item
                    prop="language">

                    <el-select v-model="temp.language" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.languages"
                        :key="index"
                        :label="item.name"
                        :value="index"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(5,'language')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(5,'language')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.language ? temp.languages[temp.language].name : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="language"
                  :rules="[
                    { required: true, message: 'Domain is required'},
                  ]">

                  <el-select v-model="temp.language" placeholder="Select" filterable style="width: 100%;">
                    <el-option
                      v-for="(item,index) in samp.languages"
                      :key="index"
                      :label="item.name"
                      :value="index"
                    />
                  </el-select>
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <svg-icon icon-class="dollar" />
                  Default currency
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(6,'currency')"
                  v-model="visible[6]"
                  placement="top"
                  title="Default currency"
                  width="200">
                  <el-form-item
                    prop="currency">
                    <el-select v-model="temp.currency" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.currencies"
                        :key="index"
                        :label="item"
                        :value="index"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(6,'currency')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(6,'currency')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.currency ? temp.currencies[temp.currency] : 'Empty' }}</span>
                </el-popover>
                  <el-form-item
                    v-else
                    prop="currency"
                    :rules="[
                      { required: true, message: 'Currency is required'}
                    ]">
                    <el-select v-model="temp.currency" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in samp.currencies"
                        :key="index"
                        :label="item"
                        :value="index"
                      />
                    </el-select>
                  </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-s-home" />
                  Office
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(7,'office')"
                  v-model="visible[7]"
                  placement="top"
                  title="Office"
                  width="200">
                  <el-form-item
                    prop="office">
                    <el-input v-model="temp.office" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'office')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(7,'office')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(7,'office')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.office ? temp.office : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                    v-else
                    prop="office">
                  <el-input v-model="temp.office" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'office')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-box" />
                  Warehouse
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(8,'warehouse')"
                  v-model="visible[8]"
                  placement="top"
                  title="Warehouse"
                  width="200">
                  <el-form-item
                    prop="warehouse">
                    <el-input v-model="temp.warehouse" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(8,'warehouse')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(8,'warehouse')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(1,'warehouse')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.warehouse ? temp.warehouse : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                    v-else
                    prop="warehouse">
                  <el-input v-model="temp.warehouse" placeholder="Please input" @keyup.enter.native="handleConfirm(8,'warehouse')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-time" />
                  Timezone
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(9,'timezone')"
                  v-model="visible[9]"
                  placement="top"
                  title="Timezone"
                  width="200">
                  <el-form-item
                    prop="timezone">
                    <el-select v-model="temp.timezone" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in temp.timezones"
                        :key="index"
                        :label="item"
                        :value="item"
                      />
                    </el-select>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(9,'timezone')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(9,'timezone')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.timezone ? temp.timezone : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                    v-else
                    prop="timezone"
                    :rules="[
                      { required: true, message: 'Timezone is required'}
                    ]">
                    <el-select v-model="temp.timezone" placeholder="Select" filterable style="width: 100%;">
                      <el-option
                        v-for="(item,index) in samp.timezones"
                        :key="index"
                        :label="item"
                        :value="item"
                      />
                    </el-select>
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-menu" />
                  Template
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(10,'template')"
                  v-model="visible[10]"
                  placement="top"
                  title="Template"
                  width="200">
                  <el-form-item
                    prop="template">
                    <el-input v-model="temp.template" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'template')" />
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(10,'template')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(10,'template')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.template ? temp.template : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                    v-else
                    prop="template"
                    :rules="[
                      { required: true, message: 'Template is required'}
                    ]">
                    <el-input v-model="temp.template" placeholder="Please input" @keyup.enter.native="handleConfirm(2,'template')" />
                </el-form-item>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'right'}">
                <template slot="label">
                  <i class="el-icon-time" />
                  Time Working
                </template>
                <el-popover
                  v-if="isEdit"
                  @hide="handleCancel(11,'time_active')"
                  v-model="visible[11]"
                  placement="top"
                  title="Time Working"
                  width="376">
                  <el-form-item
                    prop="time_active">
                    <el-time-picker
                      is-range
                      v-model="time_active"
                      range-separator="To"
                      start-placeholder="Start time"
                      end-placeholder="End time">
                    </el-time-picker>
                  </el-form-item>
                  <div style="text-align: right; margin: 12px 0px 0px 0px">
                    <el-button-group>
                      <el-button type="danger" size="mini" @click="handleCancel(11,'time_active')">cancel</el-button>
                      <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(11,'time_active')">Confirm</el-button>
                    </el-button-group>
                  </div>
                  <span slot="reference" class="border-edit">{{ temp.time_active ? temp.time_active[0]+" -> "+temp.time_active[1] : 'Empty' }}</span>
                </el-popover>
                <el-form-item
                  v-else
                  prop="time_active">
                  <el-time-picker
                    style="width: 100%"
                    is-range
                    v-model="temp.time_active"
                    range-separator="To"
                    start-placeholder="Start time"
                    end-placeholder="End time">
                  </el-time-picker>
                </el-form-item>
              </el-descriptions-item>
            </el-descriptions>
          </div>
        </el-col>

        <el-col :span="12">
          <el-skeleton :rows="6" animated :loading="dataLoading" />
          <el-descriptions class="margin-top" :column="1" border>
              
              <el-descriptions-item :content-style="{'text-align': 'left'}" :column="2">
                <template slot="label">
                  <i class="el-icon-s-marketing" />
                  App Name
                </template>
                <div v-for="(item,index) in temp.descriptions">
                  <div v-if="isEdit">
                    <svg-icon :icon-class="'flag-'+item.lang" style="width:2em"/>
                    <el-popover
                      @hide="handleCancel(index+'.'+'title.visible',index+'.'+'title.value')"
                      v-model="item.title.visible"
                      placement="top"
                      title="App Name"
                      width="200"
                    >
                      <el-form-item
                        :prop="'descriptions.'+index+'.title.value'">
                        <el-input v-model="item.title.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(index+'.title.visible',index+'.title.value',item.lang)" />
                      </el-form-item>
                      <div style="text-align: right; margin: 12px 0px 0px 0px">
                        <el-button-group>
                          <el-button type="danger" size="mini" @click="handleCancel(index+'.'+'title.visible',index+'.'+'title.value')">cancel</el-button>
                          <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(index+'.title.visible',index+'.title.value',item.lang)">Confirm</el-button>
                        </el-button-group>
                      </div>
                      <span slot="reference" class="border-edit">{{ item.title.value ? item.title.value : 'Empty' }}</span>
                    </el-popover>
                  </div>
                  <el-form-item
                    v-else
                    :prop="'descriptions.'+index+'.title.value'">
                    <el-input v-model="item.title.value" placeholder="Please input" >
                      <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" slot="suffix" />
                    </el-input>
                  </el-form-item>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'left'}">
                <template slot="label">
                  <i class="el-icon-key" />
                  Keyword
                </template>
                <div v-for="(item,index) in temp.descriptions">
                  <div v-if="isEdit">
                    <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" />
                    <el-popover
                      @hide="handleCancel(index+'.'+'keyword.visible',index+'.'+'keyword.value')"
                      v-model="item.keyword.visible"
                      placement="top"
                      title="Keyword"
                      width="200"
                    >
                      <el-form-item
                        :prop="'descriptions.'+index+'.keyword.value'">
                        <el-input v-model="item.keyword.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(index+'.keyword.visible',index+'.keyword.value',item.lang)" />
                      </el-form-item>
                      <div style="text-align: right; margin: 12px 0px 0px 0px">
                        <el-button-group>
                          <el-button type="danger" size="mini" @click="handleCancel(index+'.'+'keyword.visible',index+'.'+'keyword.value')">cancel</el-button>
                          <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(index+'.keyword.visible',index+'.keyword.value',item.lang)">Confirm</el-button>
                        </el-button-group>
                      </div>
                      <span slot="reference" class="border-edit">{{ item.keyword.value ? item.keyword.value  : 'Empty'}}</span>
                    </el-popover>
                  </div>
                  <el-form-item
                    v-else
                    :prop="'descriptions.'+index+'.keyword.value'">
                    <el-input v-model="item.keyword.value" placeholder="Please input">                                      
                      <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" slot="suffix" />
                    </el-input>
                  </el-form-item>
                </div>
              </el-descriptions-item>

              <el-descriptions-item :content-style="{'text-align': 'left'}">
                <template slot="label">
                  <i class="el-icon-document-copy" />
                  Description
                </template>
                <div v-for="(item,index) in temp.descriptions">
                  <div v-if="isEdit">
                    <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" />
                    <el-popover
                      @hide="handleCancel(index+'.'+'description.visible',index+'.'+'description.value')"
                      v-model="item.description.visible"
                      placement="top"
                      title="Description"
                      width="200"
                    >
                      <el-form-item
                        :prop="'descriptions.'+index+'.description.value'">
                        <el-input v-model="item.description.value" size="mini" placeholder="Please input" @keyup.enter.native="handleConfirm(index+'.description.visible',index+'.description.value',item.lang)" />
                      </el-form-item>
                      <div style="text-align: right; margin: 12px 0px 0px 0px">
                        <el-button-group>
                          <el-button type="danger" size="mini" @click="handleCancel(index+'.'+'description.visible',index+'.'+'description.value')">cancel</el-button>
                          <el-button type="primary" size="mini" :loading="btnLoading" @click="handleConfirm(index+'.description.visible',index+'.description.value',item.lang)">Confirm</el-button>
                        </el-button-group>
                      </div>
                      <span slot="reference" class="border-edit">{{ item.description.value ? item.description.value : 'Empty' }}</span>
                    </el-popover>
                  </div>
                  <el-form-item
                    v-else
                    :prop="'descriptions.'+index+'.description.value'">
                    <el-input v-model="item.description.value" placeholder="Please input" >
                      <svg-icon :icon-class="'flag-'+item.lang" style="width:2em" slot="suffix" />
                    </el-input>
                  </el-form-item>
                </div>
              </el-descriptions-item>
              <template slot="extra" extra="bottom">
                <el-button v-if="!isEdit" :loading="btnLoading" type="primary" @click="createData()" size="small">Save</el-button>
              </template>
          </el-descriptions>
        </el-col>
      </el-form>
    </el-row>
    <el-dialog :visible.sync="dialogStorageVisible" width="80%" @close="dialogStorageClose()">
      <component :is="componentStorage" :get-file="true" />
    </el-dialog>
  </div>
</template>

<script>


import StoreResource from '@/api/store';
import EventBus from '@/components/FileManager/eventBus';
import FileManager from '@/components/FileManager';
import { parseTime } from '@/filters';

const storeResource = new StoreResource();

export default {
  name: 'StoreInfo',
  components:{FileManager},
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    dataLoading: {
      type: Boolean,
      default: true,
    },
    dataInfo: {
      type: Object,
      default: false,
    },
  },
  data() {
    return {
      id:0,
      visible: {},
      btnLoading: false,
      temp: {},
      samp:{},
      componentStorage: '',
      dialogStorageVisible:false,
      cancelAction:false,
      time_active:'',
    };
  },
  created() {
    for (var i = 0; i <=11; i++) {
      this.$set(this.visible,i,false);
    }
    if (!this.isEdit) {
      this.samp = Object.assign({}, this.dataInfo);
      let i = 0;
      this.$set(this.temp,'descriptions',[]);
      this.$set(this.temp,'language','');
      this.$set(this.temp,'currency','');
      this.$set(this.temp,'domain','');
      this.$set(this.temp,'address','');
      this.$set(this.temp,'email','');
      this.$set(this.temp,'long_phone','');
      this.$set(this.temp,'phone','');
      this.$set(this.temp,'timezone','');
      this.$set(this.temp,'template','');
      this.$set(this.temp,'warehouse','');
      this.$set(this.temp,'office','');
      this.$set(this.temp,'time_active','');
      this.$set(this.temp,'logo','');

      for (var prop in this.samp.languages) {   
        this.$set(this.temp.descriptions,i,{lang:prop});
        i++;
      };
    }else{
      this.id = this.$route.params.id;  
      this.temp = Object.assign({}, this.dataInfo);
      if (this.temp.time_active) {
        this.temp.time_active = JSON.parse(this.temp.time_active);
      }
    }
    let that = this;
    this.temp.descriptions.forEach(function(v,i) {
      that.$set(that.temp.descriptions[i],'description',{value:v.description?v.description:'',visible:false});
      that.$set(that.temp.descriptions[i],'title',{value:v.title?v.title:'',visible:false});
      that.$set(that.temp.descriptions[i],'keyword',{value:v.keyword?v.keyword:'',visible:false});
    });
  },
  watch: {
    'dataQuery.limit': {
      handler(newValue, oldValue) {
        this.getList(newValue);
      },
    },
  },
  methods: {
    goBackList(){
      this.$router.push({ name: 'StoreList' });
    },
    handleVisibleStorage(index, key){
      EventBus.$on('getFileResponse', this.handlerGeturl);
      this.$store.commit('fm/setDisks', 'store');
      this.componentStorage = 'FileManager';
      this.dialogStorageVisible = true;
    },
    dialogStorageClose(){
      EventBus.$off('getFileResponse');
      this.componentStorage = '';
      this.dialogStorageVisible = false;
    },
    handlerGeturl(data) {
      this.temp.logo = data[0];
      if(this.isEdit){
        let params = {logo:data[0]};
        this.updateData('',params);
      }
      this.dialogStorageClose();
    },
    handleConfirm(i, key,lang){
      this.cancelAction = false;
      this.$refs['dataForm'].validateField(key, this._checkValidate);
      if (this.cancelAction) {
        return false;
      }
      this.btnLoading = true;
      let params = {};
      if(key == 'time_active'){
        this.temp.time_active = [parseTime(this.time_active[0], '{h}:{i}:{s}'),parseTime(this.time_active[1], '{h}:{i}:{s}')];
      }
      if (lang) {
        params['descriptions'] = this.temp.descriptions;
      }else{
        params[key] = this.temp[key];
      }      
      this.updateData(i,params);     
    },
    handleCancel(i,key){
      if(typeof i == 'string'){
        let newI = i.split('.');
        let newKey = key.split('.');
        this.temp.descriptions[newI[0]][newI[1]][newI[2]] = false;
      }else{
        this.visible[i] = false;
      }
    },
    _checkValidate(msg){
      if (msg != '' && msg != undefined) {
        this.cancelAction = true;
      }
    },
    reloadRedirectToList(cpn){
      const view = this.$router.resolve({ name: cpn }).route;
      this.$store.dispatch('tagsView/delCachedView', view).then(() => {
        const { fullPath } = view;
        this.$router.replace({
          path: '/redirect' + fullPath,
        });
      });
    },
    updateData(i,params){
      storeResource.update(this.id,params).then((res) => {
        if (res) {
          this.$message({
            type: 'success',
            message: 'Update successfully',
          });
          if (i) {
            if (typeof i == 'string') {
              i = i.split('.');
              this.temp.descriptions[i[0]][i[1]][i[2]] = false;
            }else{
              this.visible[i] = false;
            }
            this.btnLoading = false;
          }
          const view = this.$router.resolve({ name: 'StoreList' }).route;
          this.$store.dispatch('tagsView/delCachedView', view);
        } else {
          this.$message({
            type: 'error',
            message: 'Update failed',
          });
          this.btnLoading = false;
        }
      }).catch(err => {
          console.log(err);
          this.btnLoading = false;
      });
    },
    createData(){
      let that = this;
      for (var key in this.temp) {
        this.$refs['dataForm'].validateField(key, this._checkValidate);
      }
      if (this.cancelAction) {
        return false;
      }
      this.btnLoading = true;
      if (this.temp.time_active.length) {
        this.temp.time_active = [parseTime(this.temp.time_active[0], '{h}:{i}:{s}'),parseTime(this.temp.time_active[1], '{h}:{i}:{s}')];
      }
      storeResource.store(this.temp).then((res) => {
        if (res) {
          this.$message({
            type: 'success',
            message: 'Create successfully',
          });
          this.btnLoading = false;
          const view = this.$router.resolve({ name: 'StoreCreate' }).route;
          this.$store.dispatch('tagsView/delCachedView', view);
          this.reloadRedirectToList('StoreList');
          this.$store.dispatch('user/getInfo');
        } else {
          this.$message({
            type: 'error',
            message: 'Create failed',
          });
          this.btnLoading = false;
        }
      }).catch(err => {
          console.log(err);
          this.btnLoading = false;
      });
    }
  }
};
</script>
<style type="text/css">

  .block-tables{
    padding: 24px;
    border: 1px solid #ebebeb;
    border-radius: 3px;
    transition: .2s;
  }
  .el-main-form{
    height: calc(100vh - 130px);
    overflow-y: scroll;
  }
  .border-edit {
    border-bottom: 1px dotted #606266;
      color: #1890ff;
      cursor: pointer;
  }
</style>