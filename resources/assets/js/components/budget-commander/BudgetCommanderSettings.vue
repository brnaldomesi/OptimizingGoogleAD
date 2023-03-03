<template>
  <!-- Budget Commander Settings -->
  <div class="BudgetCommander__settings-budget" v-if="dataDownloadedSuccessfully">
    <!-- Settings Title -->
    <div class="card-title">Budget Settings</div>
    <div class="card-body">
      <div class="form-row">
               
        <label class="w-1/3 mr-6" for="input-budget">Monthly Budget</label>
        <div class="flex-grow relative">
          <Tooltip
            :content=getBudgetTooltip
            :position="'center-left'"
            :underline="false"
            class="w-full"
            v-if="includeRollover"
          >
            <div v-if="includeRollover" class="absolute rollover_icon">
              <i  class="fas fa-redo-alt" style="color:#02a27f;"></i>
            </div>
            <CurrencyInput
              class="form-control w-full"
              :value="combined_budget"
              :currency="currencyCode"
              locale="en"
              @blur="updateBudget"
              @focus="changeToOriginalBudget"
              />

          </Tooltip>

          <CurrencyInput
            class="form-control w-full"
            :value="budget"
            :currency="currencyCode"
            locale="en"
            @blur="updateBudget"
            v-if="!includeRollover"
          />
        </div>
      </div>

      <div class="form-row">
        <label class="w-1/3 mr-6">Target KPI</label>
        <div class="flex-grow flex flex-no-wrap">
          <input class="form-control danger" type="radio" name="radio-inline"
            id="cpa"
            value="CPA"
            v-model="internal_kpi_option"
            @change="updateKPIOption('CPA')"
          >
          <label for="cpa" class="mr-4">CPA</label>
          <input class="form-control dark" type="radio" name="radio-inline"
            id="roas"
            value="ROAS"
            v-model="internal_kpi_option"
            @change="updateKPIOption('ROAS')"
          >
          <label for="roas">ROAS</label>
        </div>
      </div>

      <div class="form-row">
        <label class="w-1/3 mr-6" for="input-kpi-target">{{internal_kpi_option}} Target</label>
        <div class="flex-grow">
          <CurrencyInput
            class="form-control w-full"
            :value="internal_kpi_target_value"
            :currency="currencyCode"
            locale="en"
            @blur="updateKPITarget"
            v-if="internal_kpi_option=='CPA'"
            />
            <div  v-if="internal_kpi_option=='ROAS'">
              <CurrencyInput
              @blur="updateKPITarget"
              class="form-control w-full"
              :value="internal_kpi_target_value"
              v-currency="{ currency: {prefix:'',suffix:''} }"
              />
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapActions, mapGetters, mapMutations } from 'vuex'
import * as types from '@/store/modules/accounts/types'
import Tooltip from "@/components/common/Tooltip.vue";

export default {
  name: "BudgetCommanderSettings",
  props: {
    account: {
      type: String,
      required: true
    }
  },
  components: {
    Tooltip,
  },
  data() {
    return {
      combined_budget: 0,
      budget: 0,
      internal_kpi_option: 'CPA',
      internal_kpi_target_value: 0
    }
  },
  watch: {
    kpiOption: function () {
      this.internal_kpi_option = this.kpiOption
    },
    kpiTarget: function () {
      this.internal_kpi_target_value = this.budget_commander_kpi_target
    },
  },
  computed:{
    ...mapGetters([
      'selected_account',
      'budget_commander_response_data', 
      'budget_commander_data_state',
      'budget_commander_promise',
      'budget_commander_budget',
      'budget_commander_rollover_spend',
      'budget_commander_excess_budget',
      'budget_commander_kpi_option',
      'budget_commander_kpi_target',
    ]),
    dataDownloadedSuccessfully(){
      return this.budget_commander_data_state.isSuccess
    },
    currencyCode(){
      return this.selected_account.account_currency_code
    },
    excessSpend(){
      return Number(this.budget_commander_excess_budget)
    },
    rolloverSpend(){
      return this.budget_commander_rollover_spend//boolean of whether spend will be rolled over
    },
    kpiTarget(){
      return Number(this.budget_commander_kpi_target)
    },
    kpiOption(){
      return this.budget_commander_kpi_option
    },
    getBudgetTooltip(){
      const date = new Date()
      const month = date.toLocaleString('default', { month: 'long' });
      return this.selected_account.account_currency_symbol + this.excessSpend.toLocaleString() + ' of unspent budget rolled over from last month. Your budget for '+month+' is '+this.selected_account.account_currency_symbol+(this.budget+this.excessSpend).toLocaleString()
    },
    includeRollover(){
      return this.rolloverSpend&&this.excessSpend>0
    }
  },
  watch: {
    dataDownloadedSuccessfully(newValue) {
      this.getData();
      this.setup()

    },
  },
  mounted(){
      this.setup()
  },
  methods: {
    // Update Budget in DB
    ...mapActions([
      'createAlert', 
      'setBudgetCommanderBudget',
      'setBudgetCommanderKpiOption',
      'setBudgetCommanderKpiTarget',
    
    ]),
    ...mapMutations('accounts', [
      types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS
    ]),
    setup(){
      this.assignBudgetValue()
      this.updateCombinedBudget()
      this.internal_kpi_option = this.kpiOption
      this.internal_kpi_target_value = this.kpiTarget
    },
    assignBudgetValue(){
      this.budget = Number(this.budget_commander_budget)
    },
    updateCombinedBudget(){
      this.combined_budget = Number(this.budget) + this.excessSpend
    },
    changeToOriginalBudget(){
      //when the user edits the budget they're editing the original budget excluding the rollover
      this.combined_budget = Number(this.budget)
    },
    getData(){
      if(!this.budget_commander_data_state.isSuccess)return

    },
    async updateBudget (budget) {
      this.updateCombinedBudget()
      if (isNaN(Number(budget.target.value))){//prevent the currency symbol from being submitted
        return
      }
      const new_budget = budget.target.value.trim()=='' ? 0 : parseFloat(budget.target.value)
      if(new_budget==0){
        const alert_payload = {headline:'There was a problem', message: 'Please select a value above 0',dismissSecs:5}
        this.createAlert(alert_payload)
        return
      }
      if(this.budget==new_budget)return
      await axios.patch('/api/account/' + this.account + '/budget',{
        budget: new_budget
      }).then(response => {
        this.$emit('budgetUpdated', new_budget)
        const payload = {headline:'Changes saved', message: 'Budget Updated to ' + new_budget,dismissSecs:5}
        this.createAlert(payload)
        this[types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS]({ id: this.account, budget: new_budget })
        this.budget = new_budget
        this.setBudgetCommanderBudget(this.budget)
        this.updateCombinedBudget()
      }).catch(error => {
        console.log(error)
      });
    },
    // Update KPI Option
    async updateKPIOption (option) {
      await axios.patch('/api/account/' + this.account + '/kpi',{
        kpi_name: option,
        kpi_value: this.kpiTarget
      }).then(response => {
        this.$emit('kpiOptionUpdated', option)
        const payload = {headline:'Changes saved', message: 'KPI Updated to ' + option,dismissSecs:5}
        this.createAlert(payload)
        
        const requestKpi = {
          kpi_name: option,
          kpi_value: this.kpiTarget,
          budget: this.budget,
          id: this.account
        }
        this[types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS](requestKpi)
        this.setBudgetCommanderKpiOption(option)
      });

    },
    // Update KPI Target
    async updateKPITarget (target) {
      if (isNaN(Number(target.target.value))){//prevent the currency symbol from being submitted
        return
      }
      const new_kpi_target = target.target.value.trim()=='' ? 0 : parseFloat(target.target.value)
      if(new_kpi_target==0){
        const alert_payload = {headline:'There was a problem', message: 'Please select a value above 0',dismissSecs:5}
        this.createAlert(alert_payload)
        return
      }
      if(new_kpi_target===this.kpiTarget)return
      await axios.patch('/api/account/' + this.account + '/kpi',{
        kpi_value: new_kpi_target
      }).then(response => {
        this.$emit('kpiTargetUpdated', new_kpi_target)
        const alert_payload = {headline:'Changes saved', message: 'KPI Target Updated to ' + new_kpi_target,dismissSecs:5}
        this.createAlert(alert_payload)
        const requestKpi = {
          kpi_value: new_kpi_target,
          id: this.account,
          budget: this.budget,
        }
        this[types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS](requestKpi)
        this.setBudgetCommanderKpiTarget(new_kpi_target)
      });

    }
  }
}
</script>

<style scoped>
.rollover_icon{
    right: 10px;
    top: 8px;
}
</style>
