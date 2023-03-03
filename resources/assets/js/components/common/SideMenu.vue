<template>
  <!--    TODO: Add active to the LI element, to display white "tab" -->
  <div class="flex">
    <ul class="Sidemenu" :class="{'minimized': activeItem=='accounts'}">
      <li class="Sidemenu__logo">
         <router-link :to="{ path: '/user/accounts/' + selected_account.user_id }">
          <img 
            :src="activeItem == 'accounts' ? '/assets/img/sidemenu/compressed-logo.svg' : '/assets/img/sidemenu/logo_early_access.png' "
          />
        </router-link>
      </li>
      <li
        class="Sidemenu__item account"
        :class="{ active: isActive('accounts') }"
      >
        <router-link
          :to="{ path: '/user/accounts/' + selected_account.user_id }"
        >
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ activeItem == 'accounts' ? '' : 'Accounts' }}
        </router-link>
      </li>
      <div v-if="activeItem!='accounts'">
      <div v-if="!navigationDisabled()">
        <div class="account_info">
          <li class="Sidemenu__item grime">
            {{ selected_account.account_name }}
          </li>
          <li class="Sidemenu__item grime">
            {{ getStyledAccountId }}
          </li>
        </div>

        <hr />
        <li
          class="Sidemenu__item budget"
          :class="{ active: isActive('budgetcommander') }"
        >
          <router-link
            :to="{
              path: '/user/budgetcommander/' + selected_account.account_id,
            }"
            >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Budget Commander</router-link
          >
        </li>

        <!-- <li
          class="Sidemenu__item ngram"
          :class="{ active: isActive('adtest_holding') }"
          title="Coming soon!"
        >
          <router-link :to="{ path: '/user/adtest_holding/' }"
            >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ad
            Test&nbsp;&nbsp;<i class="fas fa-lock"></i
          ></router-link>
        </li> -->
        
        <!-- <li class="Sidemenu__item grime"  :class="{ active: isActive('feed') }" >
                    <router-link :to="{ path: '/user/feed/' + selected_account.account_id } ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feed&nbsp;&nbsp;</router-link>
                </li> -->
           <li class="Sidemenu__item ngram"  :class="{ active: isActive('adtest') }" >
                    <router-link :to="{ path: '/user/adtest/' + selected_account.account_id}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ad Test&nbsp;&nbsp;</router-link>
                </li>

                
        <li
          class="Sidemenu__item grime"
          :class="{ active: isActive('feed_holding') }"
          title="Coming soon!"
        >
          <router-link :to="{ path: '/user/feed_holding' }"
            >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feed&nbsp;&nbsp;<i
              class="fas fa-lock"
            ></i
          ></router-link>
        </li>
        <!-- <li class="Sidemenu__item search">
                    <router-link to="/user/adtest">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search Queries</router-link>
                </li> -->

        <!-- <li class="Sidemenu__item matrix"  :class="{ active: isActive('matrix') }">
                    <router-link :to="{ path: '/user/matrix/' + selected_account.account_id}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The Matrix</router-link>
                </li> -->
      </div>

      <div v-if="navigationDisabled()">
        <ul>
          <hr />
          <li>Please select an account</li>
        </ul>
      </div>

      <div
        style="
          position: absolute;
          bottom: 10px;
          color: #797e94;
          font-size: .9rem;
          margin-left: 10px;
        "
      >
        <p>
          <a href="/terms" target='_blank'>Terms &amp; Conditions</a> &nbsp;|&nbsp;
          <a href="https://adevolver.com/privacy-policy/" target='_blank'>Privacy Policy</a>
        </p>
      </div>
      </div>

    </ul>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  name: "sidemenu",
  props: {},
  data() {
    return {
      activeItem: "accounts",
    };
  },
  computed: {
    ...mapGetters(["selected_account"]),
    getStyledAccountId() {
      if (this.selected_account.account_google_id === "") return "";
      if (this.selected_account.account_google_id === null) return "";
      let split = this.selected_account.account_google_id.split("");
      return (
        split[0] +
        split[1] +
        split[2] +
        "-" +
        split[3] +
        split[4] +
        split[5] +
        "-" +
        split[6] +
        split[7] +
        split[8] +
        split[9]
      );
    },
  },
  methods: {
    isActive: function(menuItem) {
      this.$emit('updateActiveItem', menuItem)
      return this.activeItem === menuItem;
    },
    navigationDisabled() {
      if (this.selected_account.account_google_id === null) return true;
      if (
        typeof this.selected_account.account_name == "undefined" ||
        this.selected_account.account_name == "" ||
        this.selected_account.account_name == "null"
      )
        return true;
      if (
        typeof this.selected_account.account_id == "undefined" ||
        this.selected_account.account_id == "" ||
        this.selected_account.account_id == "null"
      )
        return true;
      if (
        typeof this.selected_account.account_google_id == "undefined" ||
        this.selected_account.account_google_id == "" ||
        this.selected_account.account_google_id == "null"
      )
        return true;
      return false;
    },
    setActive(name) {
      //'ing' replace is for ad testing > ad test
      if (name == null) return;
      this.activeItem = name
        .toLowerCase()
        .replace(" ", "")
        .replace("ing", "");
    },
  },
  mounted() {
    this.$store.watch(
      (state, getters) => getters.account_id,
      (newValue, oldValue) => {
        this.account_id = newValue;
      }
    );

    this.setActive(this.$route.name);
  },
  watch: {
    $route(to, from) {
      this.setActive(to.name);
    },
  },
};
</script>

<style scoped>
.account_info {
  padding-bottom: 0px;
  padding-inline-start: 14px;
  padding-left: 14px;
  padding-right: 8.4px;
  padding-top: 10px;
}
</style>
