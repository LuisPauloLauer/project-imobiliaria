/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const masterRoutes = {
  path: '/master-register',
  component: Layout,
  redirect: '/master-register/tpaffiliates',
  name: 'Cadastros',
  alwaysShow: true,
  meta: {
    title: 'cadastros',
    icon: 'form',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: 'tpaffiliates/edit/:id(\\d+)',
      component: () => import('@/views/users/UserProfile'),
      name: 'UserProfile',
      meta: { title: 'userProfile', noCache: true, permissions: ['manage user'] },
      hidden: true,
    },
    {
      path: 'tpaffiliates',
      component: () => import('@/views/master/tpAffiliates/List'),
      name: 'UserList',
      meta: { title: 'Tipo Affiliados', icon: 'peoples', permissions: ['manage user'] },
    },
  ],
};

export default masterRoutes;
