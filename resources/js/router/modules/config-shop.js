import Layout from '@/layout';

const configShopRoutes = {
  path: '/config-shop',
  component: Layout,
  redirect: 'noredirect',
  name: 'configShop',
  alwaysShow: true,
  meta: {
    title: 'configShop',
    icon: 'store-setting',
    roles: ['Manager'],
  },
  children: [
    {
      path: 'shop',
      component: () => import('@/views/shop/List'),
      name: 'ShopList',
      meta: {
        title: 'Shop',
        icon: 'book-shop',
        permissions: ['shop list manager'],
        roles: ['Manager'],
        parent: 'root',
      },
    },
    {
      path: 'shop/edit/:id(\\d+)',
      component: () => import('@/views/shop/Edit'),
      name: 'ShopEdit',
      meta: {
        title: 'shopEdit',
        permissions: ['Shop manager'],
        parent: 'ShopList',
      },
      hidden: true,
    },
  ],
};

export default configShopRoutes;
