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
    path: '/content',
    alwaysShow: true,
    meta: {
      title: 'content',
      icon: 'content',
    },
    children: [
      {
        path: '/banner',
        meta: { title: 'banner',icon: 'el-icon-collection-tag'},
      },
      {
        path: '/page',
        meta: { title: 'page',icon:'el-icon-tickets'},
      },
      {
        path: '/blog-news',
        meta: { title: 'blogNews',icon:'el-icon-document-copy'},
      }
    ],
  },
  {
    path: '/marketing',
    alwaysShow: true,
    meta: {
      title: 'marketing',
      icon: 'marketing',
    },
    children: [
      {
        path: '/email-template',
        meta: { title: 'emailTemplate',icon:'email-template'},
      },
      {
        path: '/coupon-discount',
        meta: { title: 'couponDiscount',icon:'el-icon-discount'},
      },
      {
        path: '/product-flashsale',
        meta: { title: 'productFlashsale',icon:'flash-sale'},
      },
      {
        path: '/customer-manager',
        alwaysShow: true,
        meta: { title: 'customerManager',icon:'customer-manager'},
        children:[
          {
            path: '/customer-manager/list',
            name: 'List',
            meta: { title: 'customerManager',parent:'root',icon:'customer-manager'},
          },
          {
            path: '/customer-manager/subcribe',
            name: 'Subcribe',
            meta: { title: 'subcribe',parent:'root',icon:'subcribe'},
          },
        ]
      },
      {
        path: '/seo-manager',
        alwaysShow: true,
        meta: { title: 'seoManager',icon:'seo'},
        children:[
          {
            path: '/seo-manager/config',
            name: 'Config',
            meta: { title: 'config',parent:'root',icon:'admin'},
          },
        ]
      },
      {
        path: '/report-analytics',
        alwaysShow: true,
        meta: { title: 'reportAnalytics',icon:'el-icon-data-analysis'},
        children:[
          {
            path: '/report-analytics/product-report',
            name: 'ProductRepost',
            meta: { title: 'productReport',parent:'root',icon:'el-icon-pie-chart'},
          },
        ]
      },
    ],
  },
  {
    path: '/store',
    redirect : '/store/list',
    meta: {
      title: 'configStore',
      icon: 'store-setting',
      permissions: ['Store manager'],
    },
    children: [
      {
        path: 'list',
        meta: {
          title: 'store',
          icon: 'book-shop',
          parent: 'root',
        },
      },
    ],
  },
  {
    path: '/system-config',
    alwaysShow: true,
    meta: {
      title: 'system',
      icon: 'system',
    },
    children: [
      {
        path: '/users-permissions',
        alwaysShow: true,
        meta: { title: 'usersPermissions',icon:'peoples'},
        children:[
          {
            path: '/users-permissions/users',
            name: 'Users',
            meta: { title: 'users',parent:'root',icon:'user'},
          },
          {
            path: '/users-permissions/roles',
            name: 'Roles',
            meta: { title: 'roles',parent:'root',icon:'role'},
          },
          {
            path: '/users-permissions/permissions',
            name: 'Permissions',
            meta: { title: 'permissions',parent:'root',icon:'permissions'},
          },
        ]
      },
      {
        path: '/setting',
        alwaysShow: true,
        meta: { title: 'setting',icon:'settings'},
        children:[
          {
            path: '/setting/order-status',
            name: 'OrderStatus',
            meta: { title: 'orderStatus',parent:'root',icon:'el-icon-s-order'},
          },
          {
            path: '/setting/shipping-status',
            name: 'ShippingStatus',
            meta: { title: 'shippingStatus',parent:'root',icon:'el-icon-truck'},
          },
          {
            path: '/setting/payment-status',
            name: 'PaymentStatus',
            meta: { title: 'paymentStatus',parent:'root',icon:'el-icon-money'},
          },
          {
            path: '/setting/suppliers',
            name: 'Suppliers',
            meta: { title: 'suppliers',parent:'root',icon:'el-icon-s-shop'},
          },
          {
            path: '/setting/brand',
            name: 'Brand',
            meta: { title: 'brand',parent:'root',icon:'el-icon-medal'},
          },
          {
            path: '/setting/custom-field',
            name: 'CustomField',
            meta: { title: 'customField',parent:'root',icon:'el-icon-postcard'},
          },
          {
            path: '/setting/weight-unit',
            name: 'WeightUnit',
            meta: { title: 'weightUnit',parent:'root',icon:'el-icon-sold-out'},
          },
          {
            path: '/setting/length-unit',
            name: 'LengthUnit',
            meta: { title: 'lengthUnit',parent:'root',icon:'el-icon-sell'},
          },
          {
            path: '/setting/attribute-group',
            name: 'AttributeGroup',
            meta: { title: 'attributeGroup',parent:'root',icon:'el-icon-news'},
          },
          {
            path: '/setting/tax-manager',
            name: 'TaxManager',
            meta: { title: 'taxManager',parent:'root',icon:'el-icon-s-check'},
          },
        ]
      },
      {
        path: '/admin-global',
        alwaysShow: true,
        meta: { title: 'adminGlobal',icon:'el-icon-s-claim'},
        children:[
          {
            path: '/admin-global/menu',
            name: 'Menu',
            meta: { title: 'menu',parent:'root',icon:'el-icon-menu'},
          },
          {
            path: '/admin-global/env-config',
            name: 'EnviromentConfig',
            meta: { title: 'enviromentConfig',parent:'root',icon:'el-icon-s-tools'},
          },
          {
            path: '/admin-global/backup-db',
            name: 'BackupDatabase',
            meta: { title: 'backupDatabase',parent:'root',icon:'el-icon-refresh'},
          },
          {
            path: '/admin-global/cache-manager',
            name: 'CacheManager',
            meta: { title: 'cacheManager',parent:'root',icon:'el-icon-refresh-left'},
          },
        ]
      },
      {
        path: '/errors-logs',
        alwaysShow: true,
        meta: { title: 'errorsLogs',icon:'404'},
        children:[
          {
            path: '/errors-logs/operationLogs',
            name: 'OperationLogs',
            meta: { title: 'operationLogs',parent:'root',icon:'el-icon-document'},
          },
          {
            path: '/errors-logs/webhook',
            name: 'Webhook',
            meta: { title: 'webHook',parent:'root',icon:'el-icon-connection'},
          },
        ]
      },
      {
        path: '/localization',
        alwaysShow: true,
        meta: { title: 'localization',icon:'language'},
        children:[
          {
            path: '/localization/languages',
            name: 'Languages',
            meta: { title: 'languages',parent:'root',icon:'language'},
          },
          {
            path: '/localization/currency',
            name: 'Currency',
            meta: { title: 'currency',parent:'root',icon:'dollar'},
          },
        ]
      },
    ],
  }
];
