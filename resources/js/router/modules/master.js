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
    /** Master managements */
    {
      path: 'tpaffiliates',
      component: () => import('@/views/master/tpAffiliates/List'),
      name: 'TpAffiliateList',
      meta: { title: 'Tipo Affiliados', icon: 'theme', permissions: ['manage user'] },
    },
    {
      path: 'affiliates',
      component: () => import('@/views/master/affiliates/List'),
      name: 'AffiliateList',
      meta: { title: 'Affiliados', icon: 'peoples', permissions: ['manage user'] },
    },
  ],
};

export default masterRoutes;
