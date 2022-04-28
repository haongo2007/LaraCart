import Layout from '@/layout';

const systemRoutes = {
  path: '/system-config',
  component: Layout,
  redirect: 'noredirect',
  name: 'System',
  meta: {
    title: 'system',
    icon: 'table',
  },
  children: [
    // users manager
    {
      path: '/users-permissions/users',
      component: () => import('@/views/system/users-permissions/users/List'),
      name: 'UsersList',
      meta: { title: 'users', parent: 'root', permissions: ['User manager'] },
    },
    {
      path: '/users-permissions/users/create',
      component: () => import('@/views/system/users-permissions/users/Create'),
      name: 'UserCreate',
      meta: { title: 'userCreate', parent: 'UsersList', permissions: ['User manager'] },
    },
    {
      path: 'users-permissions/users/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/users/Edit'),
      name: 'UserEdit',
      meta: { title: 'userEdit',parent: 'UsersList',permissions: ['User manager']},
    },
    {
      path: '/profile',
      component: () => import('@/views/users/SelfProfile'),
      name: 'SelfProfile',
      meta: { title: 'selfProfile', icon: 'user', parent: 'root' },
    },
    // roles manager
    {
      path: '/users-permissions/roles',
      component: Layout,
      component: () => import('@/views/system/users-permissions/roles/List'),
      name: 'RolesList',
      meta: { title: 'roles', parent: 'root' , permissions: ['Role manager']},
    },
    {
      path: '/users-permissions/roles/create',
      component: () => import('@/views/system/users-permissions/roles/Create'),
      name: 'RoleCreate',
      meta: { title: 'roleCreate', parent: 'RolesList', permissions: ['Role manager'] },
    },
    {
      path: 'users-permissions/roles/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/roles/Edit'),
      name: 'RoleEdit',
      meta: { title: 'roleEdit',parent: 'RolesList',permissions: ['Role manager']},
    },
    // permissions manager
    {
      path: '/users-permissions/permissions',
      component: Layout,
      component: () => import('@/views/system/users-permissions/permissions/List'),
      name: 'PermissionsList',
      meta: { title: 'permissions', parent: 'root', permissions: ['Permission manager'] },
    },
    {
      path: '/users-permissions/permissions/create',
      component: () => import('@/views/system/users-permissions/permissions/Create'),
      name: 'PermissionCreate',
      meta: { title: 'permissionCreate', parent: 'PermissionsList', permissions: ['Permission manager'] },
    },
    {
      path: 'users-permissions/permissions/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/permissions/Edit'),
      name: 'PermissionEdit',
      meta: { title: 'permissionEdit',parent: 'PermissionsList',permissions: ['Permission manager']},
    },
    // setting
    {
      path: '/setting/order-status',
      component: Layout,
      component: () => import('@/views/store-manager/category/List'),
      name: 'CategoryList',
      meta: { title: 'orderStatus', parent: 'root' },
    },
    {
      path: '/setting/shipping-status',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'shippingStatus', parent: 'root' },
    },
    {
      path: '/setting/payment-status',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'paymentStatus', parent: 'root' },
    },
    {
      path: '/setting/suppliers',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'suppliers', parent: 'root' },
    },
    {
      path: '/setting/brand',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'brand', parent: 'root' },
    },
    {
      path: '/setting/custom-field',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'customField', parent: 'root' },
    },
    {
      path: '/setting/weight-unit',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'weightUnit', parent: 'root' },
    },
    {
      path: '/setting/length-unit',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'lengthUnit', parent: 'root' },
    },
    {
      path: '/setting/attribute-group',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'attributeGroup', parent: 'root' },
    },
    {
      path: '/setting/tax-manager',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'taxManager', parent: 'root' },
    },
    // admin global
    // {
    //   path: '/admin-global/menu',
    //   component: Layout,
    //   component: () => import('@/views/store-manager/orders/List'),
    //   name: 'OrdersList',
    //   meta: { title: 'menu', parent: 'root' },
    // },
    // {
    //   path: '/admin-global/env-config',
    //   component: Layout,
    //   component: () => import('@/views/store-manager/orders/List'),
    //   name: 'OrdersList',
    //   meta: { title: 'enviromentConfig', parent: 'root' },
    // },
    {
      path: '/admin-global/backup-db',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'backupDatabase', parent: 'root' },
    },
    {
      path: '/admin-global/cache-manager',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'cacheManager', parent: 'root' },
    },
    // errors logs
    {
      path: '/errors-logs/operationLogs',
      component: Layout,
      component: () => import('@/views/system/errors-logs/operation-logs/List'),
      name: 'OperationLogsList',
      meta: { title: 'operationLogs', parent: 'root' },
    },
    {
      path: '/errors-logs/webhook',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'webHook', parent: 'root' },
    },
    // localization
    {
      path: '/localization/languages',
      component: Layout,
      component: () => import('@/views/system/localization/languages/List'),
      name: 'LanguagesList',
      meta: { title: 'languages', parent: 'root' },
    },
    {
      path: '/localization/currency',
      component: Layout,
      component: () => import('@/views/system/localization/currency/List'),
      name: 'CurrencyList',
      meta: { title: 'currency', parent: 'root' },
    },
  ],
};
export default systemRoutes;
