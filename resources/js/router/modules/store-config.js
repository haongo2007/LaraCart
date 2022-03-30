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
    roles: ['Manager'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/store/List'),
      name: 'StoreList',
      meta: {
        title: 'Store',
        icon: 'book-shop',
        permissions: ['shop list manager'],
        roles: ['Manager'],
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
