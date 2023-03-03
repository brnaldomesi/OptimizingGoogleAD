export const namespaced = true

export default {
	state:{
    selected_account:{
      account_currency_code: '',
      account_currency_symbol: '',
      account_id: '',
      account_name: '',
      account_timezone: '',
      account_google_id: '',
      user_id: '',
      is_admin: 0,
    }
  },
  getters: {
    selected_account: state => state.selected_account,
  },
  mutations: {
    CHANGE_SELECTED_ACCOUNT(state, payload) {

      state.selected_account.account_currency_code = payload.account_currency_code
      state.selected_account.account_currency_symbol = payload.account_currency_symbol
      state.selected_account.account_id = payload.account_id
      state.selected_account.account_name = payload.account_name
      state.selected_account.account_timezone = payload.account_timezone
      state.selected_account.account_google_id = payload.account_google_id


      localStorage.setItem('account_currency_code', payload.account_currency_code)
      localStorage.setItem('account_currency_symbol', payload.account_currency_symbol)
      localStorage.setItem('account_id', payload.account_id)
      localStorage.setItem('account_name', payload.account_name)
      localStorage.setItem('account_timezone', payload.account_timezone)
      localStorage.setItem('account_google_id', payload.account_google_id)

    },
    CHANGE_SELECTED_USER(state, payload){
      state.selected_account.user_id = payload['user_id']
      state.selected_account.is_admin = payload['is_admin']
      localStorage.setItem('user_id',  payload['user_id'])
    }
  },

  actions: {
    changeSelectedAccount({ commit }, payload) {
      commit('CHANGE_SELECTED_ACCOUNT', payload)
    },
    changeSelectedUser({ commit }, payload) {
      commit('CHANGE_SELECTED_USER', payload)
    }
  }
}