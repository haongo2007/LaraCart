export const menuSidebar = [
  {
    path: '',
    meta:{      
      permissions: ['view.dashboard'],
    },
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        meta: { 
          title: 'dashboard', 
          icon: 'dashboard',
          permissions: ['view.dashboard'],
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
      permissions: ['view.order','view.product','view.category'],
    },
    children: [
      {
        path: 'orders',
        meta: {
          title: 'orders',
          icon: 'form',
          permissions: ['view.order'],
        },
      },
      // //////////////end orders
      {
        path: 'product',
        meta: {
          title: 'product',
          icon: 'shopping',
          permissions: ['view.product'],
        },
      },
      // //////////////end product
      {
        path: 'category',
        name: 'CategoryList',
        meta: {
          title: 'category',
          icon: 'list',
          permissions: ['view.category'],
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
      permissions: ['view.banner','view.page','view.blog'],
    },
    children: [
      {
        path: '/banner',
        meta: { 
          title: 'banner',
          icon: 'el-icon-collection-tag',
          permissions: ['view.banner'],
        },
      },
      {
        path: '/page',
        meta: { 
          title: 'page',
          icon:'el-icon-tickets',
          permissions: ['view.page'],
        },
      },
      {
        path: '/blog-news',
        meta: { 
          title: 'blogNews',
          icon:'el-icon-document-copy',
          permissions: ['view.blog'],
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
      permissions: [
        'view.email.template',
        'view.coupon',
        'view.flashsale',
        'view.customer',
        'view.seo',
        'view.reportanalytics',
      ],
    },
    children: [
      {
        path: '/email-template',
        meta: { 
          title: 'emailTemplate',
          icon:'email-template',
          permissions: ['view.email.template'],
        },
      },
      {
        path: '/coupon-discount',
        meta: { 
          title: 'couponDiscount',
          icon:'el-icon-discount',
          permissions: ['view.coupon'],
        },
      },
      {
        path: '/product-flashsale',
        meta: { 
          title: 'productFlashsale',
          icon:'flash-sale',
          permissions: ['view.flashsale'],
        },
      },
      {
        path: '/customer-manager',
        alwaysShow: true,
        meta: { 
          title: 'customerManager',
          icon:'customer-manager',
          permissions: ['view.customer'],
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
          icon:'seo',
          permissions: ['view.seo'],
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
          icon:'el-icon-data-analysis',
          permissions: ['view.reportanalytics'],
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
      permissions: ['view.store'],
    },
    children: [
      {
        path: 'list',
        meta: {
          title: 'store',
          icon: 'book-shop',
          permissions: ['view.store'],
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
      permissions: ['view.library'],
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
      permissions: [
        'view.user',
        'view.roles',
        'view.permissions',
        'view.order.status',
        'view.shipping.status',
        'view.payment.status',
        'view.supplier',
        'view.brand',
        'view.custom.field',
        'view.weight.unit',
        'view.length.unit',
        'view.attribute.group',
        'view.tax',
        'view.logs',
        'view.languages',
        'view.currency',
      ],
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
              permissions: ['view.user'],
              title: 'users',
              icon:'user'
            },
          },
          {
            path: '/users-permissions/roles',
            meta: { 
              permissions: ['view.roles'],
              title: 'roles',
              icon:'role'
            },
          },
          {
            path: '/users-permissions/permissions',
            meta: { 
              permissions: ['view.permissions'],
              title: 'permissions',
              icon:'permissions'
            },
          },
        ]
      },
      {
        path: '/setting',
        alwaysShow: true,
        meta: { 
          title: 'setting',
          icon:'settings',
          permissions: [
            'view.order.status',
            'view.shipping.status',
            'view.payment.status',
            'view.supplier',
            'view.brand',
            'view.custom.field',
            'view.weight.unit',
            'view.length.unit',
            'view.attribute.group',
            'view.tax',
          ],
        },
        children:[
          {
            path: '/setting/order-status',
            meta: { 
              permissions: ['view.order.status'],
              title: 'orderStatus',
              icon:'el-icon-s-order'
            },
          },
          {
            path: '/setting/shipping-status',
            meta: { 
              permissions: ['view.shipping.status'],
              title: 'shippingStatus',
              icon:'el-icon-truck'
            },
          },
          {
            path: '/setting/payment-status',
            meta: { 
              permissions: ['view.payment.status'],
              title: 'paymentStatus',
              icon:'el-icon-money'
            },
          },
          {
            path: '/setting/suppliers',
            meta: { 
              permissions: ['view.supplier'],
              title: 'suppliers',
              icon:'el-icon-s-shop'
            },
          },
          {
            path: '/setting/brand',
            meta: { 
              permissions: ['view.brand'],
              title: 'brand',
              icon:'el-icon-medal'
            },
          },
          {
            path: '/setting/custom-field',
            meta: { 
              permissions: ['view.custom.field'],
              title: 'customField',
              icon:'el-icon-postcard'
            },
          },
          {
            path: '/setting/weight-unit',
            meta: { 
              permissions: ['view.weight.unit'],
              title: 'weightUnit',
              icon:'el-icon-sold-out'
            },
          },
          {
            path: '/setting/length-unit',
            meta: { 
              permissions: ['view.length.unit'],
              title: 'lengthUnit',
              icon:'el-icon-sell'
            },
          },
          {
            path: '/setting/attribute-group',
            meta: { 
              permissions: ['view.attribute.group'],
              title: 'attributeGroup',
              icon:'el-icon-news'
            },
          },
          {
            path: '/setting/tax-manager',
            meta: { 
              permissions: ['view.tax'],
              title: 'taxManager',
              icon:'el-icon-s-check'
            },
          },
        ]
      },
      {
        path: '/errors-logs',
        alwaysShow: true,
        meta: { 
          title: 'errorsLogs',
          icon:'404',
          permissions: ['view.logs'],
        },
        children:[
          {
            path: '/errors-logs/operationLogs',
            meta: { 
              title: 'operationLogs',
              icon:'el-icon-document',
              permissions: ['view.logs'],
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
          icon:'language',
          permissions: [
            'view.languages',
            'view.currency',
          ],
        },
        children:[
          {
            path: '/localization/languages',
            meta: { 
              permissions: ['view.languages'],
              title: 'languages',
              icon:'language'
            },
          },
          {
            path: '/localization/currency',
            meta: { 
              permissions: ['view.currency'],
              title: 'currency',
              icon:'dollar'
            },
          },
        ]
      },
    ],
  }
];
