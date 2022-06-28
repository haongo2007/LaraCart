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
      meta: { 
        title: 'users', 
        parent: 'root', 
        permissions: ['view.user'] 
      },
    },
    {
      path: '/users-permissions/users/create',
      component: () => import('@/views/system/users-permissions/users/Create'),
      name: 'UserCreate',
      meta: { 
        title: 
        'userCreate', 
        parent: 'UsersList', 
        permissions: ['create.user'] 
      },
    },
    {
      path: 'users-permissions/users/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/users/Edit'),
      name: 'UserEdit',
      meta: { 
        title: 'userEdit',
        parent: 'UsersList',
        permissions: ['edit.user']
      },
    },
    {
      path: '/profile',
      component: () => import('@/views/users/SelfProfile'),
      name: 'SelfProfile',
      meta: { 
        title: 'selfProfile', 
        icon: 'user',
        parent: 'root' ,
        permissions: ['view.profile']
       },
    },
    // roles manager
    {
      path: '/users-permissions/roles',
      component: Layout,
      component: () => import('@/views/system/users-permissions/roles/List'),
      name: 'RolesList',
      meta: { 
        title: 'roles', 
        parent: 'root' , 
        permissions: ['view.roles']
      },
    },
    {
      path: '/users-permissions/roles/create',
      component: () => import('@/views/system/users-permissions/roles/Create'),
      name: 'RoleCreate',
      meta: { 
        title: 'roleCreate', 
        parent: 'RolesList',
        permissions: ['create.roles'] 
      },
    },
    {
      path: 'users-permissions/roles/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/roles/Edit'),
      name: 'RoleEdit',
      meta: { 
        title: 'roleEdit',
        parent: 'RolesList',
        permissions: ['edit.roles']
      },
    },
    // permissions manager
    {
      path: '/users-permissions/permissions',
      component: Layout,
      component: () => import('@/views/system/users-permissions/permissions/List'),
      name: 'PermissionsList',
      meta: { 
        title: 'permissions', 
        parent: 'root', 
        permissions: ['view.permissions'] 
      },
    },
    {
      path: '/users-permissions/permissions/create',
      component: () => import('@/views/system/users-permissions/permissions/Create'),
      name: 'PermissionCreate',
      meta: { 
        title: 'permissionCreate', 
        parent: 'PermissionsList', 
        permissions: ['create.permissions'] 
      },
    },
    {
      path: 'users-permissions/permissions/edit/:id(\\d+)',
      component: () => import('@/views/system/users-permissions/permissions/Edit'),
      name: 'PermissionEdit',
      meta: { 
        title: 'permissionEdit',
        parent: 'PermissionsList',
        permissions: ['edit.permissions']
      },
    },
    // setting
    {
      path: '/setting/order-status',
      component: Layout,
      component: () => import('@/views/system/setting/order-status/List'),
      name: 'OrderStatusList',
      meta: { 
        title: 'orderStatus', 
        parent: 'root',
        permissions: ['view.order.status']
      },
    },
    {
      path: '/setting/shipping-status',
      component: Layout,
      component: () => import('@/views/system/setting/shipping-status/List'),
      name: 'ShippingStatusList',
      meta: { 
        title: 'shippingStatus', 
        parent: 'root',
        permissions: ['view.shipping.status']
      },
    },
    {
      path: '/setting/payment-status',
      component: Layout,
      component: () => import('@/views/system/setting/payment-status/List'),
      name: 'PaymentStatusList',
      meta: { 
        title: 'paymentStatus',
        parent: 'root',
        permissions: ['view.payment.status']
      },
    },
    {
      path: '/setting/suppliers',
      component: Layout,
      component: () => import('@/views/system/setting/suppliers/List'),
      name: 'SuppliersList',
      meta: { 
        title: 'suppliers', 
        parent: 'root',
        permissions: ['view.supplier']
      },
    },
    {
      path: '/setting/brand',
      component: Layout,
      component: () => import('@/views/system/setting/brand/List'),
      name: 'BrandList',
      meta: { 
        title: 'brand', 
        parent: 'root',
        permissions: ['view.brand']
      },
    },
    {
      path: '/setting/custom-field',
      component: Layout,
      component: () => import('@/views/system/setting/custom-field/List'),
      name: 'CustomFieldList',
      meta: { 
        title: 'customField', 
        parent: 'root',
        permissions: ['view.custom.field']
      },
    },
    {
      path: '/setting/weight-unit',
      component: Layout,
      component: () => import('@/views/system/setting/weight-unit/List'),
      name: 'WeightUnitList',
      meta: { 
        title: 'weightUnit', 
        parent: 'root',
        permissions: ['view.weight.unit']
      },
    },
    {
      path: '/setting/length-unit',
      component: Layout,
      component: () => import('@/views/system/setting/length-unit/List'),
      name: 'LengthUnitList',
      meta: { 
        title: 'lengthUnit', 
        parent: 'root',
        permissions: ['view.length.unit']
      },
    },
    {
      path: '/setting/attribute-group',
      component: Layout,
      component: () => import('@/views/system/setting/attribute-group/List'),
      name: 'AttributeGroupList',
      meta: { 
        title: 'attributeGroup', 
        parent: 'root',
        permissions: ['view.attribute.group']
      },
    },
    {
      path: '/setting/tax-manager',
      component: Layout,
      component: () => import('@/views/system/setting/tax-manager/List'),
      name: 'TaxManagetList',
      meta: { 
        title: 'taxManager', 
        parent: 'root',
        permissions: ['view.tax']
      },
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
    // {
    //   path: '/admin-global/backup-db',
    //   component: Layout,
    //   component: () => import('@/views/store-manager/orders/List'),
    //   name: 'OrdersList',
    //   meta: { title: 'backupDatabase', parent: 'root' },
    // },
    // errors logs
    {
      path: '/errors-logs/operationLogs',
      component: Layout,
      component: () => import('@/views/system/errors-logs/operation-logs/List'),
      name: 'OperationLogsList',
      meta: { 
        title: 'operationLogs', 
        parent: 'root',
        permissions: ['view.logs']
      },
    },
    // {
    //   path: '/errors-logs/webhook',
    //   component: Layout,
    //   component: () => import('@/views/store-manager/orders/List'),
    //   name: 'OrdersList',
    //   meta: { title: 'webHook', parent: 'root' },
    // },
    // localization
    {
      path: '/localization/languages',
      component: Layout,
      component: () => import('@/views/system/localization/languages/List'),
      name: 'LanguagesList',
      meta: { 
        title: 'languages', 
        parent: 'root',
        permissions: ['view.languages']
      },
    },
    {
      path: '/localization/currency',
      component: Layout,
      component: () => import('@/views/system/localization/currency/List'),
      name: 'CurrencyList',
      meta: { 
        title: 'currency', 
        parent: 'root',
        permissions: ['view.currency']
      },
    },
  ],
};
export default systemRoutes;
