<template>
  <el-card>
    <div class="user-profile">
      <div class="user-avatar box-center">
        <el-button type="text" @click="imagecropperShow">
          <pan-thumb v-if="user.avatar" :image="user.avatar+'&w=100&h=100'" :height="'100px'" :width="'100px'" :hoverable="false" :camera="true" />
        </el-button>
        <Cropper :cropper="imagecropper" @dadUpdatedCropper="imagecropper = $event" />
      </div>
      <div class="box-center">
        <div class="user-name text-center">
          {{ user.name }}
        </div>
        <div class="user-role text-center text-muted">
          {{ getRole() }}
        </div>
      </div>
      <div class="box-social">
        <el-table :data="social" :show-header="false">
          <el-table-column prop="name" label="Name" />
          <el-table-column label="Count" align="left" width="100">
            <template slot-scope="scope">
              {{ scope.row.count | toThousandFilter }}
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="user-follow">
        <el-button type="primary" style="width: 100%;">
          Follow
        </el-button>
      </div>
    </div>
  </el-card>
</template>

<script>
import PanThumb from '@/components/PanThumb';
import Cropper from './Cropper';
import '../sass/user.scss';

export default {
  components: { PanThumb, Cropper },
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          avatar: '',
          roles: [],
        };
      },
    },
  },
  data() {
    return {
      social: [
        {
          'name': 'Followers',
          'count': 1235,
        },
        {
          'name': 'Following',
          'count': 23512,
        },
        {
          'name': 'Friends',
          'count': 7242,
        },
      ],
      imagecropper: false,
    };
  },
  methods: {
    getRole() {
      if (this.user.roles) {
        const roles = this.user.roles.map(value => this.$options.filters.uppercaseFirst(value));
        return roles.join(' | ');
      }
    },
    imagecropperShow() {
      this.imagecropper = true;
    },
  },
};
</script>
