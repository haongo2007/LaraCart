import Layout from '@/layout';

const marketingRoutes = {
  path: '/marketing',
  component: Layout,
  redirect: 'noredirect',
  name: 'Marketing',
  meta: {
    title: 'marketing',
    icon: 'table',
    roles: ['Manager'],
  },
  children: [
    //email template
    {
      path: '/email-template',
      component: Layout,
      component: () => import('@/views/marketing/email-template/List'),
      name: 'EmailTemplateList',
      meta: { title: 'emailTemplate',parent:'root',},
    },
    //coupon & discount
    {
      path: '/coupon-discount',
      component: Layout,
      component: () => import('@/views/marketing/coupon-discount/List'),
      name: 'CouponList',
      meta: { title: 'couponDiscount',parent:'root',},
    },
    //product flashsale
    {
      path: '/product-flashsale',
      component: Layout,
      component: () => import('@/views/marketing/product-flashsale/List'),
      name: 'ProductFlashSale',
      meta: { title: 'productFlashsale',parent:'root',},
    },
    //customer manager
    {
      path: '/customer-manager/list',
      component: Layout,
      component: () => import('@/views/marketing/customer-manager/List'),
      name: 'CustomerList',
      meta: { title: 'customerManager',parent:'root',},
    },
    //customer subcribe
    {
      path: '/customer-manager/subcribe',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'subcribe',parent:'root',},
    },
    //seo manager
    {
      path: '/seo-manager/config',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'config',parent:'root',},
    },
    //report analytics
    {
      path: '/report-analytics/product-report',
      component: Layout,
      component: () => import('@/views/marketing/report-analytics/product-report/List'),
      name: 'ProductReportList',
      meta: { title: 'productReport',parent:'root',},
    },
  ],
};
export default marketingRoutes;
