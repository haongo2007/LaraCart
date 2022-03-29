import Layout from '@/layout';

const systemRoutes = {
  path: '/system',
  component: Layout,
  redirect: 'noredirect',
  name: 'System',
  meta: {
    title: 'System',
    icon: 'table',
    roles: ['Manager'],
  },
  children: [
    {
      path: 'drag-table',
      component: () => import('@/views/table/DragTable'),
      name: 'DragTable',
      meta: { title: 'dragTable' },
    },
    {
      path: 'inline-edit-table',
      component: () => import('@/views/table/InlineEditTable'),
      name: 'InlineEditTable',
      meta: { title: 'inlineEditTable' },
    },
    {
      path: 'tree-table',
      component: () => import('@/views/table/TreeTable/TreeTable'),
      name: 'TreeTableDemo',
      meta: { title: 'treeTable' },
    },
    {
      path: 'custom-tree-table',
      component: () => import('@/views/table/TreeTable/CustomTreeTable'),
      name: 'CustomTreeTableDemo',
      meta: { title: 'customTreeTable' },
    },
    {
      path: 'complex-table',
      component: () => import('@/views/table/ComplexTable'),
      name: 'ComplexTable',
      meta: { title: 'complexTable' },
    },
  ],
};
export default systemRoutes;
