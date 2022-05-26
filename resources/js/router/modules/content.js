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
  ]
}

export default contentRoutes;
