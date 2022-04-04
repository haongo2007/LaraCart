import Layout from '@/layout';

const contentRoutes = {
  path: '/content',
  component: Layout,
  redirect: 'noredirect',
  name: 'Content',
  meta: {
    title: 'content',
    icon: 'table',
    roles: ['Manager'],
  },
  children:[
    {
      path: '/banner',
      component: Layout,
      component: () => import('@/views/content/banner/List'),
      name: 'BannerList',
      meta: { title: 'banner',parent:'root',},
    },
    {
      path: '/page',
      component: Layout,
      component: () => import('@/views/content/page/List'),
      name: 'PageList',
      meta: { title: 'page',parent:'root',},
    },
    {
      path: '/blog-news',
      component: Layout,
      component: () => import('@/views/content/blog/List'),
      name: 'BlogList',
      meta: { title: 'blogNews',parent:'root',},
    },
  ]
}

export default contentRoutes;
