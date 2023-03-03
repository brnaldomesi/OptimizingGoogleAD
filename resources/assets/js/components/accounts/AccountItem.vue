<template>
  <div class="py-2 Accounts__item">
    <div class="card row m-0 relative" :class="{ 'Accounts__item--active': is_synced && !accountSyncing && (expanded.length === 0 || expanded.includes(account.id)), 'Accounts__item--active-collapsed': (is_synced && expanded.length !== 0 && !expanded.includes(account.id)) || accountSyncing }" >
      <div
        class="absolute right-0 top-0 p-4"
        style="z-index: 1;"
        v-if="accountIsEnabled"
      >
        <img
          class="cursor-pointer"
          src="../../../../assets/image/icons/restore.svg"
          alt=""
          @click="toggleShowStats"
        />
      </div>

      <AccountInfo
        :isSynced="is_synced"
        :ad_performance_report_processed_at="ad_performance_report_processed_at"
        :account="account"
        :spend="getSpend"
        :kpi_target_value="kpi_target_value"
        :kpi_name="kpi"
        :budget="budget"
        :rollover_spend="rollover_spend"
        :excess_budget="excess_budget"
        :kpi_value="getKpiValue(kpi)"
        :account_currency_symbol="currencySymbol"
        :account_is_enabled="accountIsEnabled"
        :expanded="expanded"
      />

      <div class="col w-full md:w-7/12">
        <div class="row">
          <div class="col w-full flex flex-wrap justify-between">
            <div class="px-2">
              <label class="block mb-2" for="username">BUDGET</label>
              <Tooltip
                :content="getBudgetTooltip"
                :position="'center-left'"
                :underline="false"
                class="w-full"
                v-if="includeRollover"
              >
                <div v-if="includeRollover" class="absolute rollover_icon">
                  <!-- <input type="text" id="input-budget" class="form-control w-full" value="£7,500.00" /> -->
                  <i class="fas fa-redo-alt" style="color:#02a27f;"></i>
                </div>
                <CurrencyInput
                  class="appearance-none border rounded w-26 py-3 px-3 leading-tight focus:outline-none focus:shadow-outline"
                  type="text"
                  v-model.number="combined_budget"
                  v-on:input="settingChanged"
                  v-currency="{ currency: account.currency_code }"
                  @focus="changeToOriginalBudget"
                  @blur="updateCombinedBudget"
                />
              </Tooltip>
              <CurrencyInput
                class="appearance-none border rounded w-26 py-3 px-3 leading-tight focus:outline-none focus:shadow-outline"
                type="text"
                v-model.number="budget"
                v-on:input="settingChanged"
                v-currency="{ currency: account.currency_code }"
                v-if="!includeRollover"
              />
            </div>

            <div class="px-2 flex-grow" style="max-width:100px;">
              <label class="block mb-2" for="username">KPI</label>
              <div class="relative">
                <select
                  class="block appearance-none w-full border py-3 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  v-model="kpi"
                  @change="handleKpiUpdate"
                >
                  <option>{{ account.kpi_name.toUpperCase() }}</option>
                  <option>{{
                    account.kpi_name.toLowerCase() === "cpa" ? "ROAS" : "CPA"
                  }}</option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2"
                >
                  <svg
                    class="fill-current h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <div class="px-2">
              <label class="block mb-2" for="username"
                >{{ kpi.toUpperCase() }} TARGET</label
              >
              <CurrencyInput
                class="appearance-none border rounded w-24 py-3 px-3 leading-tight focus:outline-none focus:shadow-outline"
                type="text"
                v-model.number="kpi_target_value"
                v-on:input="settingChanged"
                v-currency="{ currency: account.currency_code }"
                v-if="kpi.toUpperCase() == 'CPA'"
              />
              <CurrencyInput
                class="appearance-none border rounded w-24 py-3 px-3 leading-tight focus:outline-none focus:shadow-outline"
                type="text"
                v-model.number="kpi_target_value"
                v-on:input="settingChanged"
                v-currency="{ currency: { prefix: '', suffix: '' } }"
                v-if="kpi.toUpperCase() == 'ROAS'"
              />
            </div>

            <button
              class="py-3 px-8 rounded mt-auto"
              v-bind:class="[
                { 'cursor-not-allowed': !changed },
                { 'hover:bg-red-900': changed },
                { 'bg-gray-200 ': !changed },
                { 'bg-redPrimary ': changed },
                { 'text-white ': changed },
              ]"
              @click="handleBudgetSettingUpdate"
              :disabled="!changed"
            >
              {{ buttonText }}
            </button>
            <div class="flex flex-col justify-between">
              <Tooltip
                :class="'text-center cursor-default mx-auto'"
                :content="
                  'Enable or disable data syncronisation for this account. If you disable sync, we will not update this account and all AdEvolver features will be paused.'
                "
                :title="'Sync'"
                :position="'center-left'"
              />
              <div class="flex my-auto">
                <label
                  class="form-control w-16 h-8 bg-white"
                  @click="showAlert"
                >
                  <input
                    type="checkbox"
                    class="form-control switch"
                    @change="handleIsSyncedSettingUpdate"
                    v-model="is_synced"
                    :disabled="syncIsDisabled"
                  />
                  <span class="marker-bar m-auto"></span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-none mt-8" v-if="show_stats">
        <div class="row flex justify-between">
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              slug="Cost"
              color="green"
              :value="Number(getImpressions).toLocaleString()"
              percent="11.11"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              slug="Clicks"
              color="green"
              :value="Number(getClicks).toLocaleString()"
              percent="11.11"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              slug="CTR"
              color="red"
              :value="getCtr"
              percent="11.11"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              slug="Average CPC"
              color="yellow"
              :value="getCtr"
              percent="11.11"
              :account_currency_symbol="currencySymbol"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              slug="Conversion Rate"
              color="green"
              :value="getCtr"
              percent="11.11"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              :slug="kpi"
              color="red"
              :value="getKpiValue(kpi)"
              percent="11.11"
              :account_currency_symbol="currencySymbol"
            />
          </div>
          <div class="col-divider"></div>
          <div class="Accounts__item--AccountCharts">
            <AccountChart 
              :data="clickData"
              :slug="kpi === 'CPA' ? 'Conversions' : 'Conversion Value'"
              color="green"
              :value="kpi === 'CPA' ? getConversions : getConversionValue"
              percent="11.11"
              :account_currency_symbol="currencySymbol"
            />
          </div>
        </div>
      </div>

      <!-- <div class="account_message" v-if="showAccountMessage">
        <div
          v-if="accountSyncing"
          title="Syncing"
          style="float: left;padding: 0 10px;"
        >
          <i class="fas fa-spinner fa-spin"></i>
        </div>
      </div> -->
    </div>
  </div>
</template>

<script>
import AccountInfo from "./AccountInfo.vue";
import Tooltip from "@/components/common/Tooltip.vue";
import AccountChart from "./AccountChart.vue";
import * as types from "@/store/modules/accounts/types";
import { mapActions, mapState, mapMutations } from "vuex";
import axios from "axios";

export default {
  name: "AccountItem",
  props: {
    account: {
      type: Object,
      required: true,
    },
    expanded: {
      type: Array,
      required: true
    }
  },
  components: {
    AccountInfo,
    Tooltip,
    AccountChart
  },
  data() {
    return {
      buttonText: "Save Changes",
      changed: false,
      is_synced: this.account.is_synced == 0 ? false : true,
      budget: 0,
      kpi: this.account.kpi_name.toUpperCase() || "CPA",
      kpi_target_value: Number(this.account.kpi_value),
      excess_budget: this.account.budget_commander
        ? Number(this.account.budget_commander.excess_budget)
        : 0,
      rollover_spend: this.account.budget_commander
        ? this.account.budget_commander.rollover_spend
        : 0,
      combined_budget: 0,
      show_budget: false, //without these the initial value (0) is overwriting the data value
      show_kpi_target_value: false, //without these the initial value (0) is overwriting the data value
      account_message: "",
      show_stats: false,
      maximum_accounts: 500,
      ad_performance_report_processed_at: this.account.ad_performance_report_processed_at,
      clickData: [
        {value: 280947, day: "2020-07-17"},
        {value: 93578, day: "2020-07-18"},
        {value: 89202, day: "2020-07-19"},
        {value: 303201, day: "2020-07-20"},
        {value: 308798, day: "2020-07-21"},
        {value: 301291, day: "2020-07-22"},
        {value: 284537, day: "2020-07-23"}
      ]
    };
  },
  created() {
    this.updateAccountMessage();
    this.assignBudgetValue();
    this.updateCombinedBudget();
    this.pollSyncInfo();
  },
  computed: {
    ...mapState("accounts", ["number_of_synced_accounts"]),
    getBudgetTooltip() {
      const date = new Date();
      const month = date.toLocaleString("default", { month: "long" });
      return (
        this.currencySymbol +
        this.excess_budget.toLocaleString() +
        " of unspent budget rolled over from last month. Your budget for " +
        month +
        " is " +
        this.currencySymbol +
        (this.budget + this.excess_budget).toLocaleString()
      );
    },
    currencySymbol() {
      return this.account.currency[this.account.currency_code].symbol;
    },
    includeRollover() {
      return this.rollover_spend && this.excess_budget > 0;
    },
    getPerformance() {
      return this.account.performance.length > 0
        ? this.account.performance[0]
        : false;
    },
    getSpend: function() {
      if (!this.getPerformance) return 0;
      return Number(this.getPerformance["cost"]);
    },
    getClicks: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["clicks"];
    },
    getImpressions: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["impressions"];
    },
    getCtr: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["ctr"];
    },
    getAllConversions: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["conversions"];
    },
    getAllConversionValue: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["conversion_value"];
    },
    getConversions: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["conversions"];
    },
    getConversionValue: function() {
      if (!this.getPerformance) return 0;
      return this.getPerformance["conversion_value"];
    },
    accountIsEnabled() {
      //true if the account is synced and the data has processed for the first time
      if (!this.is_synced) return false;
      if (!this.ad_performance_report_processed_at) return false;
      return true;
    },
    getKpiValue: function() {
      return (kpiValue) => {
        const performance_available = this.account.performance.length > 0;
        const cpa = performance_available
          ? Number(this.account.performance[0].cpa)
          : 0;
        const roas = performance_available
          ? Number(this.account.performance[0].roas)
          : 0;
        return kpiValue.toLowerCase() === "cpa" ? cpa : roas;
      };
    },
    syncIsDisabled() {
      if (this.changed && !this.is_synced) {
        return true;
      }
      if ((!this.hasBudget || !this.hasKpiTargetValue) && !this.is_synced) {
        return true;
      }
      if (
        this.number_of_synced_accounts > this.maximum_accounts &&
        !this.is_synced
      ) {
        return true;
      }

      return false;
    },
    hasBudget() {
      return this.budget > 0;
    },
    hasKpiTargetValue() {
      return this.kpi_target_value > 0;
    },
    showAccountMessage() {
      return !(this.is_synced && this.ad_performance_report_processed_at);
    },
    accountSyncing(){
      return !this.ad_performance_report_processed_at && this.is_synced
    }
  },
  methods: {
    ...mapActions("accounts", [
      types.UPDATE_ACCOUNT_BUDGET_SETTING,
      types.GET_NUMBER_OF_SYNCED_ACCOUNTS,
      types.TOGGLE_SYNC
    ]),
    ...mapActions([
      "createAlert",
      "setBudgetCommanderBudget",
      "setBudgetCommanderKpiTarget",
      "setBudgetCommanderKpiOption",
    ]),
    ...mapMutations("accounts", [types.UPDATE_ACCOUNT_PROCESSED_AT]),
    toggleShowStats() {
      this.show_stats = !this.show_stats;
      this.$emit('updateExpandedList', this.account.id, this.show_stats);
    },
    updateAccountMessage() {
      if (!this.is_synced)
        this.account_message = "Toggle sync on to access this account";
      if (this.is_synced && !this.ad_performance_report_processed_at)
        this.account_message = "Please wait whilst we sync this account";
    },
    showAlert() {
      if (this.syncIsDisabled) {
        let payload = {
          headline: "Please set a budget & kpi for this account",
          message:
            "We'll need them to calculate suggestions. You can change these later.",
          dismissSecs: 5,
        };

        if (this.number_of_synced_accounts > this.maximum_accounts) {
          payload = {
            headline: "Maximum number of accounts exceeded",
            message:
              "Only a single account is available during the early access period.",
            dismissSecs: 5,
          };
        }

        this.createAlert(payload);
      }
    },
    handleIsSyncedSettingUpdate() {
      this.updateAccountMessage();

      this[types.TOGGLE_SYNC]({
        account_id: this.account.id,
        user_id: this.account.user_id,
        is_synced: this.is_synced,
      });

      this.pollSyncInfo();
    },
    pollSyncInfo() {
      if (!this.accountSyncing) return;
      window.Echo.private("account-syncs." + this.account.id).listen(
        "AccountSyncedSuccessfully",
        (event) => {
          this[types.UPDATE_ACCOUNT_PROCESSED_AT]({
            id: this.account.id,
          });
          this.ad_performance_report_processed_at = true
        }
      );
    },
    handleBudgetSettingUpdate() {
      const payload = {
        budget: this.budget,
        kpi_name: this.kpi,
        kpi_value: this.kpi_target_value,
        account_id: this.account.id,
        is_synced: this.is_synced,
      };
      this[types.UPDATE_ACCOUNT_BUDGET_SETTING](payload);
      this.setBudgetCommanderBudget(this.budget);
      this.setBudgetCommanderKpiTarget(this.kpi_target_value);
      this.setBudgetCommanderKpiOption(this.kpi);
      this.buttonText = "Changed saved!";
      setTimeout(() => {
        this.buttonText = "Save Changes";
        this.changed = false;
      }, 2000);
    },
    settingChanged() {
      this.changed = true;
    },
    handleKpiUpdate() {
      this.changed = true;
      this.kpi_value = this.getKpiValue(this.kpi);
    },
    changeToOriginalBudget() {
      //when the user edits the budget they're editing the original budget excluding the rollover
      this.combined_budget = this.budget;
    },
    updateCombinedBudget(event) {
      if (event) {
        this.budget = Number(event.target.value);
      }
      this.combined_budget = this.budget + this.excess_budget;
    },
    assignBudgetValue() {
      this.budget = Number(this.account.budget);
    },
  },
};
</script>

<style scoped>
.account_message {
  min-width: 25%;
  margin: auto;
  border-radius: 6px;
}

.rollover_icon {
  right: 10px;
  top: 10px;
}
</style>
