import Layout from '@/layout';

const systemRoutes = {
  path: '/system-config',
  component: Layout,
  redirect: 'noredirect',
  name: 'System',
  meta: {
    title: 'System',
    icon: 'table',
    roles: ['Manager'],
  },
  children: [
    {
      path: 'setting',
      component: Layout,
      component: () => import('@/views/category/List'),
      name: 'Setting',
      meta: { title: 'Setting',parent:'root', level:'1'},
    },
    {
      path: 'order-status',
      component: Layout,
      component: () => import('@/views/orders/List'),
      name: 'OrderStatus',
      meta: { title: 'OrderStatus',parent:'root', level:'1-1' },
    },
  ],
};
export default systemRoutes;
