import Layout from '../../views/Banner/Layout'
import AllBanner from '../../views/Banner/All'
import CreateBanner from '../../views/Banner/Create'
import EditBanner from '../../views/Banner/Edit'
import DetailsBanner from '../../views/Banner/Details'
import ImportBanner from '../../views/category/Import'

export default {
    path: 'banner',
    component: Layout,
    props: {
        role_permissions: ['super_admin','admin'],
        layout_title: 'Banner Management',
    },
    children: [{
            path: '',
            name: 'AllBanner',
            component: AllBanner,
        },
        {
            path: 'import',
            name: 'ImportBanner',
            component: ImportBanner,
        },
        {
            path: 'create',
            name: 'CreateBanner',
            component: CreateBanner,
        },
        {
            path: 'edit/:id',
            name: 'EditBanner',
            component: EditBanner,
        },
        {
            path: 'details/:id',
            name: 'DetailsBanner',
            component: DetailsBanner,
        },
    ],

};
