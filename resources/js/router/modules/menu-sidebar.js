export const menuSidebar = [
  {
    path: '',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        meta: { 
          title: 'dashboard', 
          icon: 'dashboard' 
        },
      },
    ],
  },
  {
    path: '/store-manager',
    alwaysShow: true,
    meta: {
      title: 'storeManager',
      icon: 'theme',
      roles: ['Manager'],
      permissions: ['Store Manager'],
    },
    children: [
      {
        path: 'orders',
        meta: {
          title: 'orders',
          icon: 'form',
          roles: ['Manager'],
          permissions: ['Orders Manager','View All'],
        },
      },
      // //////////////end orders
      {
        path: 'product',
        meta: {
          title: 'Product',
          icon: 'shopping',
          roles: ['Manager'],
          permissions: ['Product Manager','View All'],
        },
      },
      // //////////////end product
      {
        path: 'category',
        name: 'CategoryList',
        meta: {
          title: 'Category',
          icon: 'list',
          roles: ['Manager'],
          permissions: ['Category Manager','View All'],
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
        meta: { 
          title: 'banner',
          icon: 'el-icon-collection-tag'
        },
      },
      {
        path: '/page',
        meta: { 
          title: 'page',
          icon:'el-icon-tickets'
        },
      },
      {
        path: '/blog-news',
        meta: { 
          title: 'blogNews',
          icon:'el-icon-document-copy'
        },
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
        meta: { 
          title: 'emailTemplate',
          icon:'email-template'
        },
      },
      {
        path: '/coupon-discount',
        meta: { 
          title: 'couponDiscount',
          icon:'el-icon-discount'
        },
      },
      {
        path: '/product-flashsale',
        meta: { 
          title: 'productFlashsale',
          icon:'flash-sale'
        },
      },
      {
        path: '/customer-manager',
        alwaysShow: true,
        meta: { 
          title: 'customerManager',
          icon:'customer-manager'
        },
        children:[
          {
            path: '/customer-manager/list',
            meta: { 
              title: 'customerManager',
              icon:'customer-manager'
            },
          },
          {
            path: '/customer-manager/subcribe',
            meta: { 
              title: 'subcribe',
              icon:'subcribe'
            },
          },
        ]
      },
      {
        path: '/seo-manager',
        alwaysShow: true,
        meta: { 
          title: 'seoManager',
          icon:'seo'
        },
        children:[
          {
            path: '/seo-manager/config',
            meta: { 
              title: 'seoConfig',
              icon:'admin'
            },
          },
        ]
      },
      {
        path: '/report-analytics',
        alwaysShow: true,
        meta: { 
          title: 'reportAnalytics',
          icon:'el-icon-data-analysis'
        },
        children:[
          {
            path: '/report-analytics/product-report',
            meta: { 
              title: 'productReport',
              icon:'el-icon-pie-chart'
            },
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
        },
      },
    ],
  },
  {
    path: '/library',
    redirect : '/library/index',
    meta: {
      title: 'library',
      icon: 'storage',
      permissions: ['File Manager'],
    },
    children: [
      {
        path: 'index',
        meta: {
          title: 'library',
          icon: 'storage',
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
            meta: { 
              permissions: ['Users manager'],
              title: 'users',
              icon:'user'
            },
          },
          {
            path: '/users-permissions/roles',
            meta: { 
              title: 'roles',
              icon:'role'
            },
          },
          {
            path: '/users-permissions/permissions',
            meta: { 
              title: 'permissions',
              icon:'permissions'
            },
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
            meta: { 
              title: 'orderStatus',
              icon:'el-icon-s-order'
            },
          },
          {
            path: '/setting/shipping-status',
            meta: { 
              title: 'shippingStatus',
              icon:'el-icon-truck'
            },
          },
          {
            path: '/setting/payment-status',
            meta: { 
              title: 'paymentStatus',
              icon:'el-icon-money'
            },
          },
          {
            path: '/setting/suppliers',
            meta: { 
              title: 'suppliers',
              icon:'el-icon-s-shop'
            },
          },
          {
            path: '/setting/brand',
            meta: { 
              title: 'brand',
              icon:'el-icon-medal'
            },
          },
          {
            path: '/setting/custom-field',
            meta: { 
              title: 'customField',
              icon:'el-icon-postcard'
            },
          },
          {
            path: '/setting/weight-unit',
            meta: { 
              title: 'weightUnit',
              icon:'el-icon-sold-out'
            },
          },
          {
            path: '/setting/length-unit',
            meta: { 
              title: 'lengthUnit',
              icon:'el-icon-sell'
            },
          },
          {
            path: '/setting/attribute-group',
            meta: { 
              title: 'attributeGroup',
              icon:'el-icon-news'
            },
          },
          {
            path: '/setting/tax-manager',
            meta: { 
              title: 'taxManager',
              icon:'el-icon-s-check'
            },
          },
        ]
      },
      {
        path: '/admin-global',
        alwaysShow: true,
        meta: { title: 'adminGlobal',icon:'el-icon-s-claim'},
        children:[
          // {
          //   path: '/admin-global/menu',
          //   meta: { 
          //     title: 'menu',
          //     icon:'el-icon-menu'
          //   },
          // },
          // {
          //   path: '/admin-global/env-config',
          //   meta: { 
          //     title: 'enviromentConfig',
          //     icon:'el-icon-s-tools'},
          // },
          {
            path: '/admin-global/backup-db',
            meta: { 
              title: 'backupDatabase',
              icon:'el-icon-refresh'
            },
          },
          {
            path: '/admin-global/cache-manager',
            meta: { 
              title: 'cacheManager',
              icon:'el-icon-refresh-left'
            },
          },
        ]
      },
      {
        path: '/errors-logs',
        alwaysShow: true,
        meta: { 
          title: 'errorsLogs',
          icon:'404'
        },
        children:[
          {
            path: '/errors-logs/operationLogs',
            meta: { 
              title: 'operationLogs',
              icon:'el-icon-document'
            },
          },
          // {
          //   path: '/errors-logs/webhook',
          //   meta: { 
          //     title: 'webHook',
          //     icon:'el-icon-connection'
          //   },
          // },
        ]
      },
      {
        path: '/localization',
        alwaysShow: true,
        meta: { 
          title: 'localization',
          icon:'language'
        },
        children:[
          {
            path: '/localization/languages',
            meta: { 
              title: 'languages',
              icon:'language'
            },
          },
          {
            path: '/localization/currency',
            meta: { 
              title: 'currency',
              icon:'dollar'
            },
          },
        ]
      },
    ],
  }
];
