import Layout from '@/layout';

const libraryRoutes = {
      path: '/library',
      component: Layout,
      redirect: 'noredirect',
      meta: { title: 'library', icon: 'storage', permissions: ['File Manager'], },
      children: [
        {
          path: 'index',
          component: () => import('@/views/library/index'),
          meta: { title: 'library', icon: 'storage',parent:'root' },
          name: 'Storage',
        },
      ],
    }

export default libraryRoutes;
