<template>
  <div class="app-container">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :xs="24" :sm="10" :md="8" :lg="6">
          <user-card :user="user" />
          <user-bio />
        </el-col>
        <el-col :xs="24" :sm="14" :md="16" :lg="18">
          <user-activity :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import UserBio from './components/UserBio';
import UserCard from './components/UserCard';
import UserActivity from './components/UserActivity';

export default {
  name: 'SelfProfile',
  components: { UserBio, UserCard, UserActivity },
  data() {
    return {
      user: {},
    };
  },
  watch: {
    '$store.state.user.avatar'(value, oldValue) {
      this.user.avatar = value;
    },
  },
  created() {
    this.getUser();
  },
  methods: {
    async getUser() {
      const data = await this.$store.dispatch('user/getInfo');
      this.user = data;
    },
  },
};
</script>
