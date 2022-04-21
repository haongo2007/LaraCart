import Layout from '@/layout';

const configStoreRoutes = {
  path: '/store',
  component: Layout,
  redirect: '/store/list',
  name: 'configStore',
  alwaysShow: true,
  meta: {
    title: 'configStore',
    icon: 'store-setting',
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/store/List'),
      name: 'StoreList',
      meta: {
        title: 'store',
        icon: 'book-shop',
        permissions: ['Store manager'],
        parent: 'root',
      },
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/store/Edit'),
      name: 'StoreEdit',
      meta: {
        title: 'infomation',
        permissions: ['Store manager'],
        parent: 'StoreList',
      },
      hidden: true,
    },
    {
      path: 'create',
      component: () => import('@/views/store/Create'),
      name: 'StoreCreate',
      meta: {
        title: 'storeCreate',
        permissions: ['Store manager'],
        parent: 'StoreList',
      },
      hidden: true,
    },
    {
      path: 'config/:id(\\d+)',
      component: () => import('@/views/store/Config'),
      name: 'StoreConfig',
      meta: {
        title: 'configStore',
        permissions: ['Store manager'],
        parent: 'StoreList',
      },
      hidden: true,
    },
  ],
};

export default configStoreRoutes;
