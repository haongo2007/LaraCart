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
    path: '/content',
    alwaysShow: true,
    meta: {
      title: 'content',
      icon: 'el-icon-s-management',
    },
    children: [
      {
        path: '/list',
        meta: { title: 'banner'},
      },
      {
        path: '/page',
        meta: { title: 'page'},
      },
      {
        path: '/blog-news',
        meta: { title: 'blogNews'},
      }
    ],
  },
  {
    path: '/maketing',
    alwaysShow: true,
    meta: {
      title: 'maketing',
      icon: 'el-icon-key',
    },
    children: [
      {
        path: '/email-template',
        meta: { title: 'emailTemplate'},
      },
      {
        path: '/coupon-discount',
        meta: { title: 'couponDiscount'},
      },
      {
        path: '/product-flashsale',
        meta: { title: 'productFlashsale'},
      },
      {
        path: '/customer-manager',
        alwaysShow: true,
        meta: { title: 'customerManager'},
        children:[
          {
            path: '/customer-manager/list',
            name: 'List',
            meta: { title: 'customerManager',parent:'root',},
          },
          {
            path: '/customer-manager/subcribe',
            name: 'Subcribe',
            meta: { title: 'subcribe',parent:'root',},
          },
        ]
      },
      {
        path: '/seo-manager',
        alwaysShow: true,
        meta: { title: 'seoManager'},
        children:[
          {
            path: '/seo-manager/config',
            name: 'Config',
            meta: { title: 'config',parent:'root',},
          },
        ]
      },
      {
        path: '/report-analytics',
        alwaysShow: true,
        meta: { title: 'reportAnalytics'},
        children:[
          {
            path: '/report-analytics/product-report',
            name: 'ProductRepost',
            meta: { title: 'productReport',parent:'root',},
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
      icon: 'el-icon-s-tools',
    },
    children: [
      {
        path: '/users-permissions',
        alwaysShow: true,
        meta: { title: 'usersPermissions'},
        children:[
          {
            path: '/users-permissions/user',
            name: 'Users',
            meta: { title: 'users',parent:'root',},
          },
          {
            path: '/users-permissions/roles',
            name: 'Roles',
            meta: { title: 'roles',parent:'root',},
          },
          {
            path: '/users-permissions/permissions',
            name: 'Permissions',
            meta: { title: 'permissions',parent:'root',},
          },
        ]
      },
      {
        path: '/setting',
        alwaysShow: true,
        meta: { title: 'setting'},
        children:[
          {
            path: '/system-config/order-status',
            name: 'OrderStatus',
            meta: { title: 'orderStatus',parent:'root',},
          },
          {
            path: '/system-config/shipping-status',
            name: 'ShippingStatus',
            meta: { title: 'shippingStatus',parent:'root',},
          },
          {
            path: '/system-config/payment-status',
            name: 'PaymentStatus',
            meta: { title: 'paymentStatus',parent:'root',},
          },
          {
            path: '/system-config/suppliers',
            name: 'Suppliers',
            meta: { title: 'suppliers',parent:'root',},
          },
          {
            path: '/system-config/brand',
            name: 'Brand',
            meta: { title: 'brand',parent:'root',},
          },
          {
            path: '/system-config/custom-field',
            name: 'CustomField',
            meta: { title: 'customField',parent:'root',},
          },
          {
            path: '/system-config/weight-unit',
            name: 'WeightUnit',
            meta: { title: 'weightUnit',parent:'root',},
          },
          {
            path: '/system-config/length-unit',
            name: 'LengthUnit',
            meta: { title: 'lengthUnit',parent:'root',},
          },
          {
            path: '/system-config/attribute-group',
            name: 'AttributeGroup',
            meta: { title: 'attributeGroup',parent:'root',},
          },
          {
            path: '/system-config/tax-manager',
            name: 'TaxManager',
            meta: { title: 'taxManager',parent:'root',},
          },
        ]
      },
      {
        path: '/admin-global',
        alwaysShow: true,
        meta: { title: 'adminGlobal'},
        children:[
          {
            path: '/admin-global/menu',
            name: 'Menu',
            meta: { title: 'menu',parent:'root',},
          },
          {
            path: '/admin-global/env-config',
            name: 'EnviromentConfig',
            meta: { title: 'enviromentConfig',parent:'root',},
          },
          {
            path: '/admin-global/backup-db',
            name: 'BackupDatabase',
            meta: { title: 'backupDatabase',parent:'root',},
          },
          {
            path: '/admin-global/cache-manager',
            name: 'CacheManager',
            meta: { title: 'cacheManager',parent:'root',},
          },
        ]
      },
      {
        path: '/error-log',
        alwaysShow: true,
        meta: { title: 'errorLog'},
        children:[
          {
            path: '/error-log/operationLog',
            name: 'OperationLog',
            meta: { title: 'operationLog',parent:'root',},
          },
          {
            path: '/error-log/webhook',
            name: 'Webhook',
            meta: { title: 'webHook',parent:'root',},
          },
        ]
      },
      {
        path: '/localization',
        alwaysShow: true,
        meta: { title: 'localization'},
        children:[
          {
            path: '/localization/languages',
            name: 'Languages',
            meta: { title: 'languages',parent:'root',},
          },
          {
            path: '/localization/currency',
            name: 'Currency',
            meta: { title: 'currency',parent:'root',},
          },
        ]
      },
    ],
  }
];
