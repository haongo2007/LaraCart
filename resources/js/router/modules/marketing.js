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
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'emailTemplate',parent:'root',},
    },
    //coupon & discount
    {
      path: '/coupon-discount',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'couponDiscount',parent:'root',},
    },
    //product flashsale
    {
      path: '/product-flashsale',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'productFlashsale',parent:'root',},
    },
    //customer manager
    {
      path: '/customer-manager/list',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
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
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'productReport',parent:'root',},
    },
  ],
};
export default marketingRoutes;
