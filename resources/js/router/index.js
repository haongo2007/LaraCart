import Vue from 'vue';
import Router from 'vue-router';

/**
 * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
 * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
 * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
 */

Vue.use(Router);

/* Layout */
import Layout from '@/layout';

/* Router for modules */
import elementUiRoutes from './modules/element-ui';
import componentRoutes from './modules/components';
import chartsRoutes from './modules/charts';
import tableRoutes from './modules/table';
import adminRoutes from './modules/admin';
import nestedRoutes from './modules/nested';
import errorRoutes from './modules/error';
import excelRoutes from './modules/excel';
import permissionRoutes from './modules/permission';
import storeManagerRoutes from './modules/store-manager';
import configStoreRoutes from './modules/store-config';
import systemRoutes from './modules/system';
import dashboardRoutes from './modules/dashboard';

/**
 * Sub-menu only appear when children.length>=1
 * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
 **/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin', 'editor']   Visible for these roles only
    permissions: ['view menu zip', 'manage user'] Visible for these permissions only
    title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    noCache: true                if true, the page will no be cached(default is false)
    breadcrumb: false            if false, the item will hidden in breadcrumb (default is true)
    affix: true                  if true, the tag will affix in the tags-view
  }
**/

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index'),
      },
    ],
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/AuthRedirect'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
  },
];

// routes has children routes
export const asyncRoutes = [
  dashboardRoutes,
  storeManagerRoutes,
  configStoreRoutes,
  systemRoutes,
  {
    path: '/profile',
    component: Layout,
    redirect: 'noredirect',
    children: [
      {
        path: 'index',
        component: () => import('@/views/users/SelfProfile'),
        name: 'SelfProfile',
        meta: { title: 'selfProfile', icon: 'user', parent: 'root' },
      },
    ],
  },
  {
    path: '/storage',
    component: Layout,
    redirect: 'noredirect',
    children: [
      {
        path: 'index',
        component: () => import('@/views/storage/index'),
        name: 'Storage',
        meta: { title: 'Storage', icon: 'storage', parent: 'root' },
      },
    ],
  },
  {
    path: '/guide',
    component: Layout,
    redirect: '/guide/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/guide/index'),
        name: 'Guide',
        meta: { title: 'guide', icon: 'guide', noCache: true, parent: 'root' },
      },
    ],
  },
  {
    path: '/documentation',
    component: Layout,
    redirect: '/documentation/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/documentation/index'),
        name: 'Documentation',
        meta: { title: 'documentation', icon: 'documentation', noCache: true, parent: 'root' },
      },
    ],
  },
  elementUiRoutes,
  permissionRoutes,
  componentRoutes,
  chartsRoutes,
  nestedRoutes,
  tableRoutes,
  adminRoutes,
  {
    path: '/clipboard',
    component: Layout,
    redirect: 'noredirect',
    meta: { permissions: ['view menu clipboard'], parent: 'root' },
    children: [
      {
        path: 'index',
        component: () => import('@/views/clipboard/index'),
        name: 'ClipboardDemo',
        meta: { title: 'clipboardDemo', icon: 'clipboard', roles: ['admin', 'manager', 'editor', 'user'] },
      },
    ],
  },
  errorRoutes,
  excelRoutes,
  {
    path: '/zip',
    component: Layout,
    redirect: '/zip/download',
    alwaysShow: true,
    meta: { title: 'zip', icon: 'zip', permissions: ['view menu zip'], parent: 'root' },
    children: [
      {
        path: 'download',
        component: () => import('@/views/zip'),
        name: 'ExportZip',
        meta: { title: 'exportZip' },
      },
    ],
  },
  {
    path: '/pdf',
    component: Layout,
    redirect: '/pdf/index',
    meta: { title: 'pdf', icon: 'pdf', permissions: ['view menu pdf'], parent: 'root' },
    children: [
      {
        path: 'index',
        component: () => import('@/views/pdf'),
        name: 'Pdf',
        meta: { title: 'pdf' },
      },
    ],
  },
  {
    path: '/pdf/download',
    component: () => import('@/views/pdf/Download'),
    hidden: true,
  },
  {
    path: '/i18n',
    component: Layout,
    meta: { permissions: ['view menu i18n'], parent: 'root' },
    children: [
      {
        path: 'index',
        component: () => import('@/views/i18n'),
        name: 'I18n',
        meta: { title: 'i18n', icon: 'international' },
      },
    ],
  },
  {
    path: '/external-link',
    component: Layout,
    children: [
      {
        path: 'https://github.com/haongo2007/larviu',
        meta: { title: 'externalLink', icon: 'link', parent: 'root' },
      },
    ],
  },
  { path: '*', redirect: '/404', hidden: true },
];

export const menuSidebar = [
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
    name: 'storeManager',
    alwaysShow: true,
    meta: {
      title: 'storeManager',
      icon: 'theme',
    },
    children: [
      {
        path: 'orders',
        name: 'OrdersList',
        meta: {
          title: 'Orders',
          icon: 'form',
          permissions: ['Order manager'],
          parent: 'root',
        },
      },
      // //////////////end orders
      {
        path: 'product',
        name: 'ProductList',
        meta: {
          title: 'Product',
          icon: 'shopping',
          permissions: ['Product manager'],
          parent: 'root',
        },
      },
      // //////////////end product
      {
        path: 'category',
        name: 'CategoryList',
        meta: {
          title: 'Category',
          icon: 'list',
          permissions: ['Category manager'],
          parent: 'root',
        },
      },
      // //////////////end category
    ],
  },
  {
    path: '/store',
    name: 'configStore',
    redirect : '/store/list',
    meta: {
      title: 'configStore',
      icon: 'store-setting',
      permissions: ['Store manager'],
    },
    children: [
      {
        path: 'list',
        name: 'StoreList',
        meta: {
          title: 'Store',
          icon: 'book-shop',
          parent: 'root',
        },
      },
    ],
  },
  {
    path: '/system-config',
    name: 'System',
    meta: {
      title: 'System',
      icon: 'table',
    },
    children: [
      {
        path: '/setting',
        name: 'Setting',
        meta: { title: 'Setting'},
        children:[
          {
            path: '/system-config/order-status',
            name: 'OrderStatus',
            meta: { title: 'OrderStatus',parent:'root',},
          },
          {
            path: '/system-config/shipping-status',
            name: 'ShippingStatus',
            meta: { title: 'ShippingStatus',parent:'root',},
          },
        ]
      },
    ],
  }
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  base: process.env.MIX_LARACART_PATH,
  routes: constantRoutes,
});

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
