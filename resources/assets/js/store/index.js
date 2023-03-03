import Vue from 'vue'
import Vuex from 'vuex'
import accounts from './modules/accounts'
import alerts from './modules/alerts/alerts'
import selected_account from './modules/selected_account/selected_account'
import ad_test from './modules/ad_test/ad_test'
import budget_commander from './modules/budget_commander/budget_commander'
import api_mutations from './modules/api_mutations/api_mutations'
import notifications from './modules/notifications/notifications'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    helpMenuClicked: false
  },
  getters: {
    help_menu_clicked: state => state.helpMenuClicked,
  },
  mutations: {
    UPDATE_HELP_MENU_CLICKED(state, clicked) {
      state.helpMenuClicked = clicked
    }
  },
  actions: {
    updateHelpMenuClicked({commit}, value){
      commit('UPDATE_HELP_MENU_CLICKED', value)
    },
  },
  modules: {
    notifications,
    accounts,
    alerts,
    selected_account,
    api_mutations,
    ad_test,
    budget_commander
  }
})