import {
    createRouter,
    createWebHistory
} from 'vue-router';
import Login from './components/Login.vue';
import Dashboard from './components/Dashboard.vue';
import UploadForm from './components/UploadForm.vue'; // Import the UploadForm component
import NumberPool from './components/NumberPool.vue'; // Number pool page
import MainDashboard from './components/MainDashboard.vue'; // Number pool page
import NumberList from './components/NumberList.vue'; // Number pool page
import UploadPool from './components/NumberPool.vue'; // Number pool page
import DncrPool from './components/DncrPool.vue'; // Number pool page
import UploadDncr from './components/UploadDncr.vue'; // Number pool page
import NumberPoolForAgent from './components/NumberPoolForAgent.vue'; // Number pool page


// Define routes
const routes = [{
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/DncrPool',
        name: 'DncrPool',
        component: DncrPool
    },
    {
        path: '/MainDashboard',
        name: 'MainDashboard',
        component: MainDashboard
    },
    {
        path: '/UploadPool/:customerId',
        name: 'UploadPool',
        component: UploadPool
    },
    {
        path: '/UploadDncr/:customerId',
        name: 'UploadDncr',
        component: UploadDncr
    },
    {
        path: '/NumberList',
        name: 'NumberList',
        component: NumberList
    },
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/upload-numbers',
        name: 'UploadForm',
        component: UploadForm // Route to the upload form
    }, {
        path: '/',
        redirect: '/upload-numbers' // Redirect to upload form by default
    },
    {
        path: '/customer-data/:customerId',
        name: 'CustomerDataPage',
        component: Dashboard, // The customer data page that shows customer details
        props: true // Pass customerId as a prop to the component
    },
{
    path: '/number-pool',
    name: 'NumberPool',
    component: NumberPool // The new number pool page
},
{
    path: '/NumberPoolForAgent',
    name: 'NumberPoolForAgent',
    component: NumberPoolForAgent // The new number pool page
},

];

// Create the router instance
const router = createRouter({
    history: createWebHistory(),
    routes
});



// Route guard to prevent unauthenticated access to dashboard
// router.beforeEach((to, from, next) => {
//     const token = localStorage.getItem('auth_token');

//     if (to.name === 'dashboard' && !token) {
//         next({
//             name: 'login'
//         });
//     } else if (to.name === 'login' && token) {
//         next({
//             name: 'MainDashboard'
//         });
//     } else {
//         next();
//     }
// });
// Route guard to check role before navigating
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const userRole = localStorage.getItem('user_role'); // Assuming role is stored in localStorage

    if (to.matched.some(record => record.meta.role)) {
        const requiredRole = to.meta.role;
        if (token && userRole === requiredRole) {
            next(); // Proceed if the user has the required role
        } else {
            next({
                name: 'Login'
            }); // Redirect to login if unauthorized
        }
    } else {
        next(); // No role restriction, proceed normally
    }
});


export default router;
