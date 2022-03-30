import Layout from '@/layout';

const dashboardRoutes = {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard', parent: 'root' },
      },
    ],
  };

export default dashboardRoutes;
