<template>
  <div 
    class="Header__notifications relative" 
    @focusout="hideNotificationsListArea"
    tabindex="0"
  >
    <div 
      class="cursor-pointer"
      @click="toggleNotificationsListArea"
    >
      <img src="/assets/img/header/alert.svg" />
      <span class="Header__notifications-unread-number" v-if="notifications.unreadCount">{{ notifications.unreadCount }}</span>
    </div>

    <div v-if="is_open" class="dropdown-content">
      <a 
        v-for="notification in notifications.all" 
        :key="notification.id" 
        href="#"
      >
        <p class="w-84 inline-block">{{ notification.data.message }}</p>
      </a>
    </div>
  </div>
</template>


<script>
export default {
  name: "Notifications",
  
  data() {
    return {
      is_open: false
    }
  },
  
  mounted() {
    this.$store.dispatch('getNotifications')
    setInterval(() => {
      this.$store.dispatch('getNotifications')
    }, 30000)
  },

  computed: {
    notifications () {
      return {
        unreadCount: this.$store.state.notifications.unreadCount,
        all: this.$store.state.notifications.items
      }
    }
  },

  methods: {
    markAllAsRead () {
        this.$store.dispatch('markAllAsRead')
    },

    toggleNotificationsListArea() {
      if(this.notifications.unreadCount) {
        this.markAllAsRead()
      }
      this.is_open = !this.is_open
    },
    
    hideNotificationsListArea() {
      this.is_open = false;
    }
  }
}
</script>

