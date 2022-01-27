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
    roles: ['Manager'],
  },
  children: [
    {
      path: 'orders',
      component: () => import('@/views/orders/List'),
      name: 'OrdersList',
      meta: {
        title: 'Orders',
        icon: 'form',
        permissions: ['Orders manager'],
        roles: ['Manager'],
        parent: 'root',
      },
    },
    {
      path: 'orders/create',
      component: () => import('@/views/orders/Create'),
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
      component: () => import('@/views/orders/Edit'),
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
      component: () => import('@/views/product/List'),
      name: 'ProductList',
      meta: {
        title: 'Product',
        icon: 'shopping',
        permissions: ['Product manager'],
        roles: ['Manager'],
        parent: 'root',
      },
    },
    {
      path: 'product/createsingle',
      component: () => import('@/views/product/CreateSingle'),
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
      component: () => import('@/views/product/CreateGroup'),
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
      component: () => import('@/views/product/CreateBundle'),
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
      component: () => import('@/views/product/EditSingle'),
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
      component: () => import('@/views/product/EditGroup'),
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
      component: () => import('@/views/product/EditBundle'),
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
      component: () => import('@/views/category/List'),
      name: 'CategoryList',
      meta: {
        title: 'Category',
        icon: 'list',
        permissions: ['Category manager'],
        roles: ['Manager'],
        parent: 'root',
      },
    },
    {
      path: 'category/create',
      component: () => import('@/views/category/Create'),
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
      component: () => import('@/views/category/Edit'),
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
