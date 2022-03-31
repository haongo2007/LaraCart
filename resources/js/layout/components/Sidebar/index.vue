<template>
  <div :class="{'has-logo':showLogo}">
    <logo v-if="showLogo" :collapse="isCollapse" />
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :show-timeout="200"
        :default-active="$route.path"
        :collapse="isCollapse"
        mode="vertical"
        background-color="#304156"
        text-color="#bfcbd9"
        active-text-color="#409EFF"
      >
        <sidebar-item v-for="route in route" :key="route.path" :item="route" :base-path="route.path" />
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import SidebarItem from './SidebarItem';
import Logo from './Logo';
import variables from '@/styles/variables.scss';

export default {
  components: { SidebarItem, Logo },
  data(){
    return {
      route:[]
    }
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'permission_routers',
    ]),
    showLogo() {
      return this.$store.state.settings.sidebarLogo;
    },
    variables() {
      return variables;
    },
    isCollapse() {
      return !this.sidebar.opened;
    },
  },
  created(){
    this.reMenu();
    // console.log(this.route);
  },
  methods:{
    reMenu(route,level){
      let that = this;
      var newRoute;
      if (!route) {
        let cloneRoute = [...this.$store.state.permission.routes];
        let i = 1;
        cloneRoute.forEach(function (val,ind) {
          val = JSON.parse(JSON.stringify(val));
          if(val.hasOwnProperty('children') && val.children.length > 1){
            that.reMenu(val,i);
          }
        }) 
      }else{
        route.children.forEach(function (val,ind) {    
          if (val.hasOwnProperty('meta') && val.meta.hasOwnProperty('level') && val.meta.level.toString() == level.toString()) { 
            if(level == 1) {
              route.children[ind] = val;
              that.reMenu(route,level+'-'+level);
            }else{
              level = level.split('-')[0];
              let index = route.children.findIndex((item) => item.meta.level == level);
              route.children[index]['children'] = val;
              route.children.splice(ind,1);
            }
          }
        })
        // this.route.push(route);
      }
    }
  }
};
</script>
