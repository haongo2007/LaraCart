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
      path: 'shop-list',
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
  ],
};

export default configShopRoutes;
