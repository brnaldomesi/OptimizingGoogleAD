import Accounts from "./components/accounts/Accounts";
import { AlertPlugin } from 'bootstrap-vue'
import App from "./views/App.vue";
import BudgetCommander from "./components/budget-commander/BudgetCommander.vue"
import Feed from './components/feed/Feed.vue'
import Vue from 'vue'
import Vue2Filters from 'vue2-filters'
import VueCurrencyInput from 'vue-currency-input'
import VueInputAutowidth from 'vue-input-autowidth'
import VueYoutube from 'vue-youtube'
import VModal from 'vue-js-modal'
import routes from "./routes";
import store from './store'
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import SidebarModal from 'vue-sidebar-modal'
import TrendChart from "vue-trend-chart"
import "vue-sidebar-modal/dist/vue-sidebar-modal.css";
// import Vuetify from 'vuetify'
// import 'vuetify/dist/vuetify.min.css'
// import vuetify from '@/plugins/vuetify' // path to vuetify export
library.add(fas)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.use(SidebarModal)
Vue.use(AlertPlugin)
Vue.use(VueInputAutowidth)
Vue.use(VueCurrencyInput)
Vue.use(Vue2Filters)
Vue.use(VueYoutube)
Vue.use(VModal)
Vue.use(TrendChart)
window.Event = new Vue();
Vue.use(require('vue-resource'));
Vue.use(require('bootstrap-vue'));

Vue.config.productionTip = false
// Vue.use(Vuetify)

// const opts = {}

// export default new Vuetify(opts)


try{
  Vue.prototype.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');
}catch{
  Vue.prototype.$userId = ''
}

try{
  Vue.prototype.$userImageUrl = document.querySelector("meta[name='user-img-url']").getAttribute('content');
}catch{
  Vue.prototype.$userImageUrl = ''
}

//the account id meta might not be defined
try{
  Vue.prototype.$accountId = document.querySelector("meta[name='account-id']").getAttribute('content');
}catch{
  Vue.prototype.$accountId = ''
}

Vue.component('InfiniteLoading', require('vue-infinite-loading'));
Vue.component('feed', require('./components/feed/Feed.vue'));
Vue.component('notifications', require('./components/common/notifications/Notifications.vue'));
// Vue.component('budget-commander', require('../../js/components/budget-commander/BudgetCommander.vue'));
// Vue.component('ad-test', require('../../js/components/ad-test/AdTest.vue'));

new Vue({
  store,
  el: "#app",
  router: routes,
  components: {
    App,
    Feed,
    BudgetCommander,
    Accounts,
  }
})

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});