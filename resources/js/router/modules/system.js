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
        path: 'setting/order-status',
      component: Layout,
        component: () => import('@/views/category/List'),
        name: 'CategoryList',
        meta: { title: 'dragTable' },
      },
  ],
};
export default systemRoutes;
