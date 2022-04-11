import Layout from '@/layout';

const storeManagerRoutes = {
  path: '/store-manager',
  component: Layout,
  redirect: 'noredirect',
  name: 'storeManager',
  alwaysShow: true,
  meta: {
    title: 'storeManager',
    icon: 'theme',
  },
  children: [
    {
      path: 'orders',
      component: () => import('@/views/store-manager/orders/List'),
      name: 'OrdersList',
      meta: {
        title: 'Orders',
        icon: 'form',
        permissions: ['Orders manager'],
        parent: 'root',
      },
    },
    {
      path: 'orders/create',
      component: () => import('@/views/store-manager/orders/Create'),
      name: 'OrderCreate',
      meta: {
        title: 'orderCreate',
        permissions: ['Orders manager'],
        parent: 'OrdersList',
      },
      hidden: true,
    },
    {
      path: 'orders/edit/:id(\\d+)',
      component: () => import('@/views/store-manager/orders/Edit'),
      name: 'OrderEdit',
      meta: {
        title: 'orderEdit',
        permissions: ['Orders manager'],
        parent: 'OrdersList',
      },
      hidden: true,
    },
    // //////////////end orders
    {
      path: 'product',
      component: () => import('@/views/store-manager/product/List'),
      name: 'ProductList',
      meta: {
        title: 'Product',
        icon: 'shopping',
        permissions: ['Product manager'],
        parent: 'root',
      },
    },
    {
      path: 'product/createsingle',
      component: () => import('@/views/store-manager/product/CreateSingle'),
      name: 'ProductCreateSingle',
      meta: {
        title: 'productCreateSingle',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    {
      path: 'product/createGroup',
      component: () => import('@/views/store-manager/product/CreateGroup'),
      name: 'ProductCreateGroup',
      meta: {
        title: 'productCreateGroup',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    {
      path: 'product/createBundle',
      component: () => import('@/views/store-manager/product/CreateBundle'),
      name: 'ProductCreateBundle',
      meta: {
        title: 'productCreateBundle',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    {
      path: 'product/editSingle/:id(\\d+)',
      component: () => import('@/views/store-manager/product/EditSingle'),
      name: 'ProductEditSingle',
      meta: {
        title: 'productEditSingle',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    {
      path: 'product/editGroup/:id(\\d+)',
      component: () => import('@/views/store-manager/product/EditGroup'),
      name: 'ProductEditGroup',
      meta: {
        title: 'productEditGroup',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    {
      path: 'product/editBundle/:id(\\d+)',
      component: () => import('@/views/store-manager/product/EditBundle'),
      name: 'ProductEditBundle',
      meta: {
        title: 'productEditBundle',
        permissions: ['Product manager'],
        parent: 'ProductList',
      },
      hidden: true,
    },
    // //////////////end product
    {
      path: 'category',
      component: () => import('@/views/store-manager/category/List'),
      name: 'CategoryList',
      meta: {
        title: 'Category',
        icon: 'list',
        permissions: ['Category manager'],
        parent: 'root',
      },
    },
    {
      path: 'category/create',
      component: () => import('@/views/store-manager/category/Create'),
      name: 'CategoryCreate',
      meta: {
        title: 'categoryCreate',
        permissions: ['Category manager'],
        parent: 'CategoryList',
      },
      hidden: true,
    },
    {
      path: 'category/edit/:id(\\d+)',
      component: () => import('@/views/store-manager/category/Edit'),
      name: 'CategoryEdit',
      meta: {
        title: 'categoryEdit',
        permissions: ['Category manager'],
        parent: 'CategoryList',
      },
      hidden: true,
    }, // //////////////end category
  ],
};

export default storeManagerRoutes;
