import Layout from '@/layout';

const contentRoutes = {
  path: '/content',
  component: Layout,
  redirect: 'noredirect',
  name: 'Content',
  meta: {
    title: 'content',
    icon: 'table',
    permissions: ['view,banner','view.page','view.blog'],
  },
  children:[
    {
      path: '/banner',
      component: Layout,
      component: () => import('@/views/content/banner/List'),
      name: 'BannerList',
      meta: { 
        title: 'banner',
        parent:'root',
        permissions: ['view.banner'],
      },
    },
    {
      path: '/page',
      component: Layout,
      component: () => import('@/views/content/page/List'),
      name: 'PageList',
      meta: { 
        title: 'page',
        parent:'root',
        permissions: ['view.page'],
      },
    },
    {
      path: 'page/create',
      component: () => import('@/views/content/page/Create'),
      name: 'PageCreate',
      meta: {
        title: 'pageCreate',
        permissions: ['create.page'],
        parent: 'PageList',
      },
      hidden: true,
    },
    {
      path: 'page/edit/:id(\\d+)',
      component: () => import('@/views/content/page/Edit'),
      name: 'PageEdit',
      meta: {
        title: 'pageEdit',
        permissions: ['edit.page'],
        parent: 'PageList',
      },
      hidden: true,
    },
    {
      path: '/blog-news',
      component: Layout,
      component: () => import('@/views/content/blog/List'),
      name: 'BlogList',
      meta: { 
        title: 'blogNews',
        parent:'root',
        permissions: ['view.blog'],
      },
    },
    {
      path: 'blog-news/create',
      component: () => import('@/views/content/blog/Create'),
      name: 'BlogCreate',
      meta: {
        title: 'blogCreate',
        permissions: ['create.blog'],
        parent: 'BlogList',
      },
      hidden: true,
    },
    {
      path: 'blog-news/edit/:id(\\d+)',
      component: () => import('@/views/content/blog/Edit'),
      name: 'BlogEdit',
      meta: {
        title: 'blogEdit',
        permissions: ['edit.blog'],
        parent: 'BlogList',
      },
      hidden: true,
    },
  ]
}

export default contentRoutes;
