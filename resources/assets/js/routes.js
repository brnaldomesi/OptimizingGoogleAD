import Accounts from "./components/accounts/Accounts.vue";
import AdTest from "./components/ad-test/AdTest.vue";
import AdTestHolding from "./components/ad-test/AdTestHolding.vue";
import BudgetCommander from "./components/budget-commander/BudgetCommander.vue";
import Feed from "./components/feed/Feed.vue";
import FeedHolding from "./components/feed/FeedHolding.vue";
import Matrix from "./components/matrix/Matrix.vue";
import ComingSoon from "./components/coming-soon/ComingSoon.vue";
import NotFound from "./components/NotFound.vue";
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {
            path: '/user/feed/:id',
            component: Feed,
            name: "Feed"
        },
        {
            path: '/user/feed_holding',
            component: FeedHolding,
            name: "Feed (Coming Soon)"
        },
        {
            path: '/user/budgetcommander/:id',
            component: BudgetCommander,
            name: "Budget Commander"
        },
        {
            path: '/user/adtest/:id',
            component: AdTest,
            name: "Ad Testing"
        },
        {
            path: '/user/adtest_holding',
            component: AdTestHolding,
            name: "Ad Testing (Coming Soon)"
        },
        {
            path: '/user/accounts/:user_id',
            component: Accounts,
            name: "Accounts"
        },
        {
            path: '/user/matrix/:id',
            component: Matrix,
            name: "Matrix"
        },
        {
            path: '/user/coming_soon',
            component: ComingSoon,
            name: "Coming Soon"
        },
        {
            path: '(\/privacy|\/register|\/login|\/handle-authentication|\/logout|\/admin.*|\/api.*|\/user.*|\/terms|\/password.*|\/logs|\/registration_error)',
            name: "404",
            component: NotFound,
        },
        {
            path: '/404',
            component: NotFound,
            name: "404 Page Not Found"
        },
        {
          path: '*',
          redirect: {name: '404'}
        },
        {
          path: '/feedback',
          component: Accounts,
          name: 'Feedback'  
        }
    ],
    mode: 'history',
    // linkActiveClass: "active"
});