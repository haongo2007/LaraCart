/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const adminRoutes = {
  path: '/administrator',
  component: Layout,
  redirect: '/administrator/users',
  name: 'Administrator',
  alwaysShow: true,
  meta: {
    title: 'administrator',
    icon: 'admin',
  },
  children: [
    {
      path: '/profile',
      component: Layout,
      redirect: 'noredirect',
      children: [
        {
          path: 'index',
          component: () => import('@/views/users/SelfProfile'),
          name: 'SelfProfile',
          meta: { title: 'selfProfile', icon: 'user', parent: 'root' },
        },
      ],
    },
    /** User managements */
    {
      path: 'users/edit/:id(\\d+)',
      component: () => import('@/views/users/UserProfile'),
      name: 'UserProfile',
      meta: { title: 'userProfile', noCache: true },
      hidden: true,
    },
    {
      path: 'users',
      component: () => import('@/views/users/List'),
      name: 'UserList',
      meta: { title: 'users', icon: 'user' },
    },
    /** Role and permission */
    // {
    //   path: 'roles',
    //   component: () => import('@/views/role-permission/List'),
    //   name: 'RoleList',
    //   meta: { title: 'rolePermission', icon: 'role', permissions: ['manage permission'] },
    // },
    {
      path: 'articles/create',
      component: () => import('@/views/articles/Create'),
      name: 'CreateArticle',
      meta: { title: 'createArticle', icon: 'edit', permissions: ['manage article'] },
      hidden: true,
    },
    {
      path: 'articles/edit/:id(\\d+)',
      component: () => import('@/views/articles/Edit'),
      name: 'EditArticle',
      meta: { title: 'editArticle', noCache: true, permissions: ['manage article'] },
      hidden: true,
    },
    {
      path: 'articles',
      component: () => import('@/views/articles/List'),
      name: 'ArticleList',
      meta: { title: 'articleList', icon: 'list', permissions: ['manage article'] },
    },
  ],
};

export default adminRoutes;
