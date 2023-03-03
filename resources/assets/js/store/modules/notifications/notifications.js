import axios from 'axios'

export default {
  state: {
    unreadCount: 0,
    items: []
  },

  mutations: {
    LOAD_NOTIFICATIONS (state, res) {
      state.unreadCount = res.unreadCount
      state.items = res.notifications
    },

    NOTIFICATION_MARK_ALL_AS_READ (state, res) {
      state.unreadCount = 0
      state.items = res
    }
  },

  actions: {
    getNotifications (context) {
      return axios.get('/user/notifications')
                    .then(response => context.commit('LOAD_NOTIFICATIONS', response.data))
    },

    markAllAsRead (context) {
      return axios.put('/user/notifications')
                    .then(response => context.commit('NOTIFICATION_MARK_ALL_AS_READ', response.data))
    }
  }
}