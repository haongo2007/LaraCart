import Layout from '@/layout';

const contentRoutes = {
  path: '/content',
  component: Layout,
  redirect: 'noredirect',
  name: 'Content',
  meta: {
    title: 'content',
    icon: 'table',
    roles: ['Manager'],
  },
  children:[
    {
      path: '/banner',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'banner',parent:'root',},
    },
    {
      path: '/page',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'page',parent:'root',},
    },
    {
      path: '/blog-news',
      component: Layout,
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: { title: 'blogNews',parent:'root',},
    },
  ]
}

export default contentRoutes;
