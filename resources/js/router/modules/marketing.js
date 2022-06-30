import Layout from '@/layout';

const marketingRoutes = {
  path: '/marketing',
  component: Layout,
  redirect: 'noredirect',
  name: 'Marketing',
  meta: {
    title: 'marketing',
    icon: 'table',
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
    //email template
    {
      path: '/email-template',
      component: Layout,
      component: () => import('@/views/marketing/email-template/List'),
      name: 'EmailTemplateList',
      meta: { 
        title: 'emailTemplate',
        parent:'root',
        permissions: ['view.email_template'],
      },
    },
    {
      path: 'email-template/create',
      component: () => import('@/views/marketing/email-template/Create'),
      name: 'EmailTemplateCreate',
      meta: {
        title: 'createEmailTemplate',
        permissions: ['create.email_template'],
        parent: 'EmailTemplateList',
        noCache: true
      },
      hidden: true,
    },
    {
      path: 'email-template/edit/:id(\\d+)',
      component: () => import('@/views/marketing/email-template/Edit'),
      name: 'EmailTemplateEdit',
      meta: {
        title: 'editEmailTemplate',
        permissions: ['edit.email_template'],
        parent: 'EmailTemplateList',
        noCache: true
      },
      hidden: true,
    },
    //coupon & discount
    {
      path: '/coupon-discount',
      component: Layout,
      component: () => import('@/views/marketing/coupon-discount/List'),
      name: 'CouponList',
      meta: { 
        title: 'couponDiscount',
        parent:'root',
        permissions: ['view.coupon'],
      },
    },
    //product flashsale
    {
      path: '/product-flashsale',
      component: Layout,
      component: () => import('@/views/marketing/product-flashsale/List'),
      name: 'ProductFlashSale',
      meta: { 
        title: 'productFlashsale',
        parent:'root',
        permissions: ['view.flashSale'],
      },
    },
    //customer manager
    {
      path: '/customer-manager/list',
      component: Layout,
      component: () => import('@/views/marketing/customer-manager/List'),
      name: 'CustomerList',
      meta: { 
        title: 'customerManager',
        parent:'root',
        permissions: ['view.customer'],
      },
    },
    {
      path: 'customer-manager/create',
      component: () => import('@/views/marketing/customer-manager/Create'),
      name: 'CustomerCreate',
      meta: {
        title: 'createCustomer',
        permissions: ['create.customer'],
        parent: 'CustomerList',
      },
      hidden: true,
    },
    {
      path: 'customer-manager/edit/:id(\\d+)',
      component: () => import('@/views/marketing/customer-manager/Edit'),
      name: 'CustomerEdit',
      meta: {
        title: 'editCustomer',
        permissions: ['edit.customer'],
        parent: 'CustomerList',
      },
      hidden: true,
    },
    //customer subcribe
    {
      path: '/customer-manager/subcribe',
      component: Layout,
      component: () => import('@/views/marketing/customer-manager/subcribe/List'),
      name: 'SubscribeList',
      meta: { title: 'subcribe',parent:'root',},
    },
    //seo manager
    {
      path: '/seo-manager/config',
      component: Layout,
      component: () => import('@/views/marketing/seo-manager/Config'),
      name: 'SeoConfig',
      meta: { 
        title: 'seoConfig',
        parent:'root',
        permissions: ['view.seo'],
      },
    },
    //report analytics
    {
      path: '/report-analytics/product-report',
      component: Layout,
      component: () => import('@/views/marketing/report-analytics/product-report/List'),
      name: 'ProductReportList',
      meta: { 
        title: 'productReport',
        parent:'root',
        permissions: ['view.reportanalytics'],
      },
    },
  ],
};
export default marketingRoutes;
