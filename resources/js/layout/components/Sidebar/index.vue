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
        <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
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
      route:[
        {
          path: '',
          children: [
            {
              path: 'dashboard',
              name: 'Dashboard',
              meta: { title: 'dashboard', icon: 'dashboard', parent: 'root' },
            },
          ],
        },
        {
          path: '/store-manager',
          redirect: 'noredirect',
          name: 'storeManager',
          alwaysShow: true,
          meta: {
            title: 'storeManager',
            icon: 'theme',
            roles: ['Manager'],
          },
          children: [
            {
              path: 'orders',
              name: 'OrdersList',
              meta: {
                title: 'Orders',
                icon: 'form',
                permissions: ['Orders manager'],
                roles: ['Manager'],
                parent: 'root',
              },
            },
            {
              path: 'orders/create',
              component: () => import('@/views/orders/Create'),
              name: 'OrderCreate',
              meta: {
                title: 'orderCreate',
                permissions: ['Orders manager'],
                parent: 'OrdersList',
              },
              hidden: true,
            },
            {
              path: 'orders/edit/:id(\\d+)',
              component: () => import('@/views/orders/Edit'),
              name: 'OrderEdit',
              meta: {
                title: 'orderEdit',
                permissions: ['Orders manager'],
                parent: 'OrdersList',
              },
              hidden: true,
            },
            // //////////////end orders
            {
              path: 'product',
              component: () => import('@/views/product/List'),
              name: 'ProductList',
              meta: {
                title: 'Product',
                icon: 'shopping',
                permissions: ['Product manager'],
                roles: ['Manager'],
                parent: 'root',
              },
            },
            {
              path: 'product/createsingle',
              component: () => import('@/views/product/CreateSingle'),
              name: 'ProductCreateSingle',
              meta: {
                title: 'productCreateSingle',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            {
              path: 'product/createGroup',
              component: () => import('@/views/product/CreateGroup'),
              name: 'ProductCreateGroup',
              meta: {
                title: 'productCreateGroup',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            {
              path: 'product/createBundle',
              component: () => import('@/views/product/CreateBundle'),
              name: 'ProductCreateBundle',
              meta: {
                title: 'productCreateBundle',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            {
              path: 'product/editSingle/:id(\\d+)',
              component: () => import('@/views/product/EditSingle'),
              name: 'ProductEditSingle',
              meta: {
                title: 'productEditSingle',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            {
              path: 'product/editGroup/:id(\\d+)',
              component: () => import('@/views/product/EditGroup'),
              name: 'ProductEditGroup',
              meta: {
                title: 'productEditGroup',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            {
              path: 'product/editBundle/:id(\\d+)',
              component: () => import('@/views/product/EditBundle'),
              name: 'ProductEditBundle',
              meta: {
                title: 'productEditBundle',
                permissions: ['Product manager'],
                parent: 'ProductList',
              },
              hidden: true,
            },
            // //////////////end product
            {
              path: 'category',
              component: () => import('@/views/category/List'),
              name: 'CategoryList',
              meta: {
                title: 'Category',
                icon: 'list',
                permissions: ['Category manager'],
                roles: ['Manager'],
                parent: 'root',
              },
            },
            {
              path: 'category/create',
              component: () => import('@/views/category/Create'),
              name: 'CategoryCreate',
              meta: {
                title: 'categoryCreate',
                permissions: ['Category manager'],
                parent: 'CategoryList',
              },
              hidden: true,
            },
            {
              path: 'category/edit/:id(\\d+)',
              component: () => import('@/views/category/Edit'),
              name: 'CategoryEdit',
              meta: {
                title: 'categoryEdit',
                permissions: ['Category manager'],
                parent: 'CategoryList',
              },
              hidden: true,
            }, // //////////////end category
          ],
        },
        {
          path: '/store',
          name: 'configStore',
          meta: {
            title: 'configStore',
            icon: 'store-setting',
            roles: ['Manager'],
          },
          children: [
            {
              path: 'list',
              name: 'StoreList',
              meta: {
                title: 'Store',
                icon: 'book-shop',
                permissions: ['shop list manager'],
                roles: ['Manager'],
                parent: 'root',
              },
            },
            {
              path: 'edit/:id(\\d+)',
              name: 'StoreEdit',
              meta: {
                title: 'infomation',
                permissions: ['Store manager'],
                parent: 'StoreList',
              },
              hidden: true,
            },
            {
              path: 'config/:id(\\d+)',
              name: 'StoreConfig',
              meta: {
                title: 'configStore',
                permissions: ['Store manager'],
                parent: 'StoreList',
              },
              hidden: true,
            },
          ],
        }
      ]
    }
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'permission_routers',
    ]),
    routes() {
      // return this.$store.state.permission.routes;
      return this.route;
    },
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
};
</script>
