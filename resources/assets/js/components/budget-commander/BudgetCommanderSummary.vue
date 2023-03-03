<template>
  <div class="col">
    <!-- Budget Commander Summary -->
    <div class="card flex flex-wrap xl:flex-no-wrap BudgetCommander__overview">
      
      <div class="flex-grow mr-4">
        <p class="card-title">Overview</p>
        <div >
          <span >{{ summaryMessage }}</span>
        </div>
      </div>

      <!-- Summary Data -->
      <div class="flex flex-grow flex-wrap md:flex-no-wrap">
        <BudgetCommanderSummaryStat
          :budget="combinedBudget"
          :kpi-target="kpiTarget"
          :stat="stat"
          :key="stat.id"
          v-for="stat in getStats"        />
      </div>
    </div>
  </div>
</template>

<script>
import BudgetCommanderSummaryStat from "./BudgetCommanderSummaryStat.vue"
import { getSummaryMessage } from "./summary_message.js"
import { mapGetters } from 'vuex'


export default {
  name: "BudgetCommanderSummary",
  components: {
    BudgetCommanderSummaryStat
  },
  props: {
    budget: {
      type: Number,
      required: true
    },
    kpiOption: {
      type: String,
      required: true
    },
    kpiTarget: {
      type: Number,
      required: true
    },
    average_cpc: {
      type: Number,
      required: true
    },
    required_cpc: {
      type: Number,
      required: true
    },
    account: {
      type: String,
      required: true
    },
    rollover_spend: {
      type: [Number,Boolean],
      required: false
    }
  },
  computed: {
    ...mapGetters([
      'selected_account',
      'budget_commander_response_data', 
      'budget_commander_data_state',
      'budget_commander_excess_budget'
    ]),
    responseData(){
      if(this.budget_commander_data_state.isSuccess){
        return this.budget_commander_response_data
      }
      return false
    },
    spend(){
      if(!this.responseData)return
      let date = new Date;
      let yesterday = date.getDate()-2;
      let budgetactualdata = this.responseData.budget_actual_graph_data;
      return budgetactualdata[yesterday];
    },
    excessBudget(){
      if(!this.responseData)return 0
      if(!this.rollover_spend)return 0
      return this.budget_commander_excess_budget
    },
    combinedBudget(){
      return this.budget + this.excessBudget
    },
    conversions(){
      if(!this.responseData)return
      return this.responseData.this_month_performance.conversions
    },
    conversionValue(){
      if(!this.responseData)return
      return this.responseData.this_month_performance.conversion_value
    },
    forecastSpend(){
      if(!this.responseData)return
      return this.responseData.budget_actual_graph_data.slice(-1).pop();
    },
    currencySymbol(){
      return this.selected_account.account_currency_symbol
    },
    kpiValue(){
      if(this.conversions==0)return 0
      return this.kpiOption==="CPA" ? this.spend/this.conversions : this.conversionValue/this.spend
    },
    summaryMessage(){

      if(this.conversions==0)return "There isn't enough data to provide a summary just yet."

      return getSummaryMessage(
        this.budget, 
        this.excessBudget,
        this.currencySymbol,
        this.forecastSpend,
        this.spend,
        this.kpiOption,
        this.kpiValue,
        this.kpiTarget
        )
    },
    getStats () {

      return [
        {
          id: 1,
          primaryAttribute: 'Spend',
          secondaryAttribute: 'Budget',
          primaryAttributeAmount: this.spend
        },
        {
          id: 2,
          primaryAttribute: 'Forecast Spend',
          secondaryAttribute: 'Budget',
          primaryAttributeAmount: this.forecastSpend
        },
        {
          id: 3,
          primaryAttribute: this.kpiOption,
          secondaryAttribute: 'Target',
          primaryAttributeAmount: this.kpiValue,

        },{
          id: 4,
          primaryAttribute: this.kpiOption=='ROAS' ? 'Conv. Value' : 'Conversions',
          primaryAttributeAmount: this.kpiOption=='ROAS' ? this.conversionValue : this.conversions
        },
      ]
    },
    getRequiredCpc(){
      if(this.budget >= this.forecastSpend || this.budget < 0.1){
        return this.average_cpc
      }
      if(this.spend > this.budget){
        return 0
      }
      return  Number((this.average_cpc)+(this.average_cpc*((this.budget-this.forecastSpend)/this.budget))).toFixed(2)
    },
    // Get Reduce Budget Amount
    getRequiredDailySpendReductionAmount () {
      //(forecast-budget)/remaining_days
      let days_in_month = new Date((new Date()).getFullYear(), (new Date()).getMonth()+1, 0).getDate();
      let remaining_days = days_in_month-((new Date()).getDate()-1)
      let remaining_budget = this.budget - this.spend
      let daily_spend = this.forecastSpend/days_in_month
      let required_spend = (remaining_budget / remaining_days)
      let required_daily_spend_reduction = daily_spend-required_spend
      if(required_daily_spend_reduction > daily_spend){
        return daily_spend.toFixed(2)
      }
      return this.budget >= this.forecastSpend ? 0 : (required_daily_spend_reduction).toFixed(2)
    },
    // Get Reduce Budget Percentage
    getRequiredDailySpendPercentage () {
      if(this.forecastSpend < this.budget){
        return 0
      }
      let days_in_month = new Date((new Date()).getFullYear(), (new Date()).getMonth()+1, 0).getDate();
      let daily_spend = this.forecastSpend/days_in_month
      return ((this.getRequiredDailySpendReductionAmount/daily_spend)*100).toFixed(1)
    }
  },
  created() {

  },
}
</script>

<style scoped>

</style>
