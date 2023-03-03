<template>
  <form class="BudgetCommander__actions">
    <!-- Email Me -->
    <div class="mt-10 BudgetCommander__settings-alert">
      <div class="card-body">
        <div class="form-row">
          <div class="w-1/2 mr-6">
            <Tooltip
              :content="'If you enable Optimisation, we’ll also email you about those changes too.'"
              :title="'Email me'"
              :position="'center-left'"
            />
            if spend exceeds budget
          </div>
          <label class="form-control flex" for="emailMe">
            <input
              class="form-control switch"
              type="checkbox"
              name="st1"
              id="emailMe"
              v-model="emailMeOption"
              @change="postBCdata()"
            />
            <span class="marker"></span>
          </label>
        </div>
      </div>
    </div>

    <div class="mt-10 BudgetCommander__settings-optimisation">
      <div class="card-body">
        <!-- Pause Campaigns -->
        <div class="form-row">
          <div class="w-1/2 mr-6">
            <Tooltip
              :content="'If spend exceeds the total monthly budget, pause all campaigns in the account.'"
              :title="'Pause account'"
              :position="'center-left'"
            />
            if monthly budget reached
          </div>
          <label class="flex" for="pauseCampaignsOption">
            <input
              class="form-control switch"
              type="checkbox"
              :value="true"
              name="st1"
              id="pauseCampaignsOption"
              v-model="pauseCampaignsOption"
              @change="postBCdata()"
            />
            <span class="marker"></span>
          </label>
        </div>

        <!-- Re-enable Campaigns -->
        <div class="form-row">
          <div class="w-1/2 mr-6">
            <Tooltip
              :content="'Re-enable paused campaigns the following month (or if budget is increased above spend).'"
              :title="'Re-enable Campaigns'"
              :position="'center-left'"
            />
          </div>
          <label class="flex" for="reenableCampaignsOption">
            <input
              class="form-control"
              type="checkbox"
              id="reenableCampaignsOption"
              v-model="reenableCampaignsOption"
              @change="postBCdata()"
              :disabled="!pauseCampaignsOption"
            />
            <span class="marker"></span>
          </label>
        </div>

          <div class="card-title">Budget Optimisation</div>


        <!-- Rollover Budget -->
        <div class="form-row">
          <div class="w-1/2 mr-6">
            <Tooltip
              :content="'Any unspent budget from this month will be added to your budget for next month.'"
              :title="'Rollover'"
              :position="'center-left'"
            />
            Budget
          </div>
          <label class="flex" for="rolloverBudgetOption">
            <input
              class="form-control"
              type="checkbox"
              id="rolloverBudgetOption"
              v-model="rolloverBudgetOption"
              @change="postRolloverData()"
            />
            <span class="marker"></span>
          </label>
        </div>

        <!-- Control Spend -->
        <div class="form-row">
          <div class="w-1/2 mr-6">
            <Tooltip
              :content="'Hourly monitoring - pause all campaigns for the day if they spend more than your expected daily budget.'"
              :title="'Emergency Stop'"
              :position="'center-left'"
            />
          </div>
          <label class="flex" for="emergencyStopOption">
            <input
              class="form-control"
              type="checkbox"
              id="emergencyStopOption"
              v-model="emergencyStopOption"
              @change="postBCdata()"
            />
            <span class="marker"></span>
          </label>
        </div>
      </div>

      <div class="card-body">
        <!-- Budget automation -->
        <div class="form-row">
          <div class="w-1/2 mr-6">
  
            <i class="fas fa-lock"></i>
            Budget
            <Tooltip
              :content="'Coming Soon! Automatically adjust bids and budgets each day to help you hit your target spend and KPI.'"
              :title="'Automation'"
              :position="'center-left'"
            />
          </div>
          <label class="flex" for="controlSpendOption">
            <input
              class="form-control"
              type="checkbox"
              id="controlSpendOption"
              v-model="controlSpendOption"
              @change="postBCdata()"
              disabled
            />
            <span class="marker" style="
                cursor: no-drop;
            "></span>
          </label>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import axios from "axios";
import Tooltip from "@/components/common/Tooltip.vue";
import { mapActions,mapMutations } from 'vuex'
import * as types from '@/store/modules/accounts/types'

export default {
  name: "BudgetCommanderActions",
  props: {
    account: {
      type: String,
      required: true,
    },
  },
  components: {
    Tooltip,
  },
  data() {
    return {
      emailMeOption: 0,
      pauseCampaignsOption: 0,
      reenableCampaignsOption: 0,
      rolloverBudgetOption: 0,
      controlSpendOption: 0,
      emergencyStopOption: 0,
    };
  },
  created() {
    this.getBCdata();
  },
  methods: {
    ...mapActions([
      'setBudgetCommanderSettings',
      'setBudgetCommanderExcessBudget',
    ]),
    ...mapMutations('accounts', [
      types.UPDATE_ACCOUNT_ROLLOVER_SETTING_SUCCESS
    ]),
    getBCdata: async function () {
      await axios
        .get("/api/account/" + this.account + "/actions")
        .then((response) => {
          this.emailMeOption = response.data.notify_via_email;
          this.pauseCampaignsOption = response.data.pause_campaigns;
          this.reenableCampaignsOption = response.data.enable_campaigns;
          this.rolloverBudgetOption = response.data.rollover_spend;
          this.controlSpendOption = response.data.control_spend;
          this.emergencyStopOption = response.data.emergency_stop;
          this.setBudgetCommanderSettings({rollover_spend:this.rolloverBudgetOption})

        });
    },
    async postRolloverData(){
      //unchecking rollover should wipe the excess budget
      if(!this.rolloverBudgetOption){
        const payload = {
          excess_budget: 0,
        }
        let self = this
        await axios
        .post("/api/account/" + this.account + "/actions", payload)
        .then(function (response) {
          self.setBudgetCommanderExcessBudget(0)
        });
      }
      this.postBCdata()
    },
    postBCdata: async function () {
      const payload = {
        notify_via_email: this.emailMeOption,
        pause_campaigns: this.pauseCampaignsOption,
        enable_campaigns: this.reenableCampaignsOption,
        rollover_spend: this.rolloverBudgetOption,
        control_spend: this.controlSpendOption,
        emergency_stop: this.emergencyStopOption,
      }
      let self = this
      await axios
        .post("/api/account/" + this.account + "/actions", payload)
        .then(function (response) {
          self[types.UPDATE_ACCOUNT_ROLLOVER_SETTING_SUCCESS]({ id: self.account, rollover_spend: self.rolloverBudgetOption })
          self.setBudgetCommanderSettings(payload)
        });
    },
  },
};
</script>

<style scoped></style>
