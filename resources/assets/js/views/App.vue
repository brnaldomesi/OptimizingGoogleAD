<template>
  <div class="flex">
    <sidemenu :user_id="user_id" @updateActiveItem='updateActiveItem' />

    <headermenu :user_id="user_id" :accountSelected="accountSelected"/>

    <div class="flex bg-gray-200 w-full h-full">
      <router-view></router-view>
    </div>
    <sidebar-modals-container/>
      <modal name="featureModal" 
            :width="1100"
            :height="700"
          >
        <Canny />
      </modal>
      <Help 
        @onHelpClick='onHelpClick'
        @onHelpMenuClick='onHelpMenuClick'
        @onChatMenuClick='onChatMenuClick'
        @onSuggestMenuClick='onSuggestMenuClick'
        :helpClicked='helpClicked'
      />
    <alerts />
  </div>
</template>

<script>
import axios from "axios";
import Sidemenu from "../components/common/SideMenu";
import Headermenu from "../components/common/HeaderMenu";
import Alerts from "../components/common/Alerts.vue";
import { mapActions } from "vuex";
import Help from '@/components/common/Help.vue'
import HelpMenu from '@/components/common/HelpMenu.vue'
import Canny from '@/components/common/Canny.vue'

export default {
  data: function() {
    return {
      user_id: "",
      accountSelected: true,
      helpClicked: false,
    };
  },
  methods: {
    ...mapActions(["changeSelectedUser", "changeSelectedAccount", "updateHelpMenuClicked"]),
    updateUserId() {
      this.loading = false;

      if (typeof this.$route.params.user_id !== "undefined") {
        this.changeSelectedUser({user_id:this.$route.params.user_id, is_admin:false});
        return
      }

      if (localStorage.getItem("user_id")) {
        this.changeSelectedUser({user_id:localStorage.getItem("user_id"), is_admin:false});
        return
      }

      throw("Error assigning user id")

    },
    updateActiveItem(menuItem) {
      this.accountSelected = menuItem == 'accounts' ? true : false
    },
    onHelpMenuClick: function() {
      this.updateHelpMenuClicked(true)
      this.$sidebarModal.show(HelpMenu, {
        text: ''
      }, {
        height: '100%',
        width: '500px',
        clickToClose: false,
      }, {
        'before-close': (event) => { this.helpClicked = false; this.updateHelpMenuClicked(false) },
      })
    },
    onChatMenuClick: function() {
      this.helpClicked = false
    },
    onSuggestMenuClick: function() {
      this.helpClicked = false
      this.$modal.show('featureModal')
    },
    onHelpClick: function(clicked) {
      this.helpClicked = clicked
    },
  },
  created() {
    this.updateUserId()

    const selected_account_payload = {
      account_currency_code: localStorage.getItem("account_currency_code"),
      account_currency_symbol: localStorage.getItem("account_currency_symbol"),
      account_id: localStorage.getItem("account_id"),
      account_name: localStorage.getItem("account_name"),
      account_timezone: localStorage.getItem("account_timezone"),
      account_google_id: localStorage.getItem("account_google_id")
    };

    this.changeSelectedAccount(selected_account_payload);
  },
  components: {
    Alerts,
    Sidemenu,
    Headermenu,
    Help,
    Canny
  }
};
</script>
<style scoped>
</style>