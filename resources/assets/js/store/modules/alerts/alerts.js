export const namespaced = true

export default {
	state:{
		alert:{
			headline: '',
			message: '',
			dismissSecs: 0,
		}
	},
	getters: {
    alert: state => state.alert,
  },
  mutations: {
    CREATE_ALERT(state, payload) {

      state.alert.headline = payload.headline
      state.alert.message = payload.message
      state.alert.dismissSecs = payload.dismissSecs
    },
    CLEAR_ALERT(state) {
      state.alert.headline = ''
      state.alert.message = ''
      state.alert.dismissSecs = 0
    }
  },

  actions: {
    createAlert({ commit }, payload) {
      commit('CLEAR_ALERT')
      commit('CREATE_ALERT', payload)
    },
    clearAlert({ commit }) {
      commit('CLEAR_ALERT')
    },
  }
}