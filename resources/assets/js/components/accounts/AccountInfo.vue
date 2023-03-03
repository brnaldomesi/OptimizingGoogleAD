<template>
  <div class="col w-full md:w-5/12 Accounts__info">
    <div class="row">
      <div class="col w-full sm:w-5/12">
        <div class="flex">
          <div class="flex mb-auto mt-2 mr-2">

            <div v-if="accountSyncing" title="Syncing">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <img
              v-else
              :src="(isSynced && (expanded.length === 0 || expanded.includes(account.id))) ? '/images/calendar-category-2.svg' : '/images/calendar-category.svg' "
              alt=""
              class="Accounts__info--category"
            />
          </div>

          <div
            @click="changeSelectedAccount(accountPayload)"
            v-if="account_is_enabled"
          >
            <router-link
              class="font-semibold text-2xl"
              :to="{ path: '/user/budgetcommander/' + this.account_id }"
            >
              <span class="font-semibold text-2xl" v-html="accountNameHtml" />
            </router-link>
          </div>

          <div 
          @click="displayAccountDisabledAlert" 
          v-if="!account_is_enabled"
          :title="accountSyncing ? 'Syncing' : 'Sync to access account' "
          >
            <a
              class="font-semibold text-2xl cursor-not-allowed"
              :disabled="!account_is_enabled"
            >
              <span class="font-semibold text-2xl" v-html="accountNameHtml">
              </span>
            </a>
          </div>
        </div>
      </div>

      <div v-if="isSynced" class="col w-full sm:w-7/12 flex flex-wrap">
        <div class="w-full sm:w-1/2 px-2">
          <label class="block mb-5" for="username"
            >SPEND ({{ spend | currency(account_currency_symbol) }})</label
          >
          <div class="bg-background w-full rounded h-6">
            <div
              class=" rounded"
              :class="[parseInt(budgetWidth) > 99 ? 'bg-danger' : 'bg-success']"
              :style="{ width: budgetWidth }"
            >
              &nbsp;
            </div>
          </div>
        </div>

        <div class="w-full sm:w-1/2 px-2">
          <label
            class="block mb-5"
            for="username"
            v-if="kpi_name.toUpperCase() == 'ROAS'"
            >ROAS ({{ kpi_value }})</label
          >
          <label class="block mb-5" for="username" v-else
            >CPA ({{ kpi_value | currency(account_currency_symbol) }})</label
          >
          <div class="bg-background w-full rounded h-6">
            <div
              class=" rounded"
              :class="barGraphClass"
              :style="{ width: kpiWidth }"
            >
              &nbsp;
            </div>
          </div>
        </div>
      </div>
      <div v-else class="col w-full sm:w-7/12 flex flex-wrap">
        <span class="mt-auto mb-2">Toggle sync on to access this account</span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "AccountInfo",
  props: {
    spend: {
      type: Number,
      required: true,
    },
    kpi_target_value: {
      type: Number,
      required: true,
    },
    kpi_name: {
      type: String,
      required: true,
    },
    budget: {
      type: Number,
      required: true,
    },
    rollover_spend: {
      type: [Number, Boolean],
      required: false,
    },
    excess_budget: {
      type: Number,
      required: false,
    },
    kpi_value: {
      type: Number,
      required: true,
    },
    account_currency_symbol: {
      type: String,
      required: true,
    },
    account_is_enabled: {
      type: Boolean,
      required: true,
    },
    account:{
      type: Object,
      required: true
    },
    isSynced:{
      type: Boolean,
      required: true
    },
    ad_performance_report_processed_at:{
      type: [Boolean, String],
      required: false
    },
    expanded:{
      type: Array,
      required: true
    }
  },
  data() {
    return {
      account_currency_code: this.account.currency_code,
      account_id: this.account.id,
      account_name: this.account.name,
      account_timezone: this.account.timezone,
      account_google_id: this.account.google_id,
    };
  },
  computed: {
    accountSyncing(){
      return !this.ad_performance_report_processed_at && this.isSynced
    },
    accountNameHtml() {
      let name = "";
      let words = this.account_name.split(" ");
      if (words.length < 3) {
        name = "<p>" + this.account_name + "</p><p>&nbsp;</p>";
      } else {
        name = "<p>";
        name += words.splice(0, 2).join(" ");
        name += "</p><p>";
        name += words.join(" ");
        name += "</p>";
      }
      return name;
    },
    budgetWidth() {
      let budget =
        this.rollover_spend && this.excess_budget > 0
          ? this.budget + this.excess_budget
          : this.budget;
      let vs =
        this.spend / budget > 1 ? 100 : Number(this.spend / budget) * 100;
      return vs + "%";
    },
    kpiWidth() {
      let vs =
        this.kpi_value / this.kpi_target_value > 1
          ? 100
          : Number(this.kpi_value / this.kpi_target_value) * 100;
      return vs + "%";
    },
    barGraphClass(){
      if(this.kpi_name=='cpa'){
        return parseInt(this.kpiWidth) > 99 ? 'bg-danger' : 'bg-success'
      }
      return parseInt(this.kpiWidth) < 100 ? 'bg-danger' : 'bg-success'
    },
    accountPayload() {
      return {
        account_currency_code: this.account_currency_code,
        account_currency_symbol: this.account_currency_symbol,
        account_id: this.account_id,
        account_name: this.account_name,
        account_timezone: this.account_timezone,
        account_google_id: this.account_google_id,
      };
    } 
  },
  methods: {
    ...mapActions(["changeSelectedAccount", "createAlert"]),
    displayAccountDisabledAlert() {
      this.createAlert({
        headline: "Inactive Account",
        message: "This account hasn't been synced yet",
        dismissSecs: 5,
      });
    },
  },
  mounted() {},
};
</script>

<style scoped></style>
