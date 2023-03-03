<template>
  <!-- Budget Commander Chart Wrapper -->
  <div class="col w-full lg:w-1/2 xl:w-2/3 BudgetCommander__budget-commander">
    <div class="card">
      <div class="card-title flex justify-between">
        Budget Commander
        <!-- <div class="flex justify-between"> -->
          <!-- <div class="w-48 mr-1 mt-2">Budget Group</div> -->
          <!-- <select 
            v-model="selectedBudgetGroup" 
            @change="onChangeBudgetGroup($event)"
            class="w-full border py-2 px-4 pr-8 rounded-full focus:outline-none focus:bg-white focus:border-gray-500"
          >
            <option value="">All</option>
            <option v-for="group in budgetGroup" :value="group.value">
              {{ group.text }}
            </option>
          </select> -->
        <!-- </div> -->
      </div>
      <div class="card-body">
        <div class="flex justify-between flex-wrap mb-5">
          <div class="flex items-center mt-3 mr-3">
            <img
              src="/assets/img/budget-commander/calendar.svg"
              class="inline"
            />
            <span class="ml-2">This Month</span>
          </div>
          <!-- <div class="mt-3 flex mr-3">
            <div class="flex items-center mr-3">
              <span class="w-4 h-4 bg-success rounded-full mr-1"></span>
              Budget
            </div>
            <div class="flex items-center mr-3">
              <span class="w-4 h-4 bg-info rounded-full mr-1"></span>
              Spend
            </div>
            <div class="flex items-center mr-3">
              <span class="w-4 h-4 bg-danger rounded-full mr-1 relative BudgetCommander__legend-item--line"></span>
              Forecast
            </div>
          </div> -->
          <!-- <div class="button-group mt-3">
            <input type="radio" id="legend-year" name="legend" />
            <label for="legend-year">Year</label>
            <input type="radio" id="legend-quater" name="legend" />
            <label for="legend-quater">Quater</label>
            <input type="radio" id="legend-month" name="legend" checked />
            <label for="legend-month">Month</label>
            <input type="radio" id="legend-week" name="legend" />
            <label for="legend-week">Week</label>
            <input type="radio" id="legend-fortnight" name="legend" />
            <label for="legend-fortnight">14d</label>
          </div> -->
        </div>
        <div>
          <!-- Budget Commander Chart -->
          <BudgetCommanderChart
            :chartData="datacollection"
            :options="options"
            ref="budgetCommanderChart"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BudgetCommanderChart from "../charts/BudgetCommanderChart.js";
import moment from "moment";
import { mapGetters } from "vuex";

export default {
  name: "LineChartContainer",
  components: { BudgetCommanderChart },
  props: {
    budget: {
      type: Number,
      required: true,
    },
    account: {
      type: String,
      required: true,
    },
    create:{
      type: Boolean,
      required: false
    },
    rollover_spend:{
      type: [Boolean,Number],
      required: false
    },
    excess_budget:{
      type: Number,
      required: false
    }
  },

  data() {
    return {
      datacollection: null,
      options: null,
      actualdata: [],
      forecastdata: [],
      targetdata: [],
      initialchartdata: [],
      budgetGroup: [
        { text: 'Target', value: 'Target'},
        { text: 'Actual', value: 'Actual'},
        { text: 'Forecast', value: 'Forecast'},
        { text: 'Today', value: 'Today'}
      ],
      selectedBudgetGroup: ''
    };
  },

  computed: {
    ...mapGetters([
      "selected_account",
      'budget_commander_response_data', 
      'budget_commander_data_state',
      'budget_commander_promise',
    ]),
    getBudget() {
      if(!this.rollover_spend || !this.excess_budget)return this.budget;
      return this.budget + this.excess_budget
    },
    getDataState(){
      return this.budget_commander_data_state.isSuccess
    },
    currencySymbol() {
      return this.selected_account.account_currency_symbol;
    },
    currencyCode() {
      return this.selected_account.account_currency_code;
    },
  },

  watch: {
    getBudget(newValue) {
      this.getChartData();
    },
    getDataState(newValue) {
      this.getChartData();
    },
  },
  created() {
    if(this.create){
      //passed from the feed as the budget watcher won't fire
      this.getChartData();
    }
  },

  methods: {
    fillData() {
      const vm = this;
      this.datacollection = {
        labels: this.getDates(),
        datasets: [
          {
            label: "Target",
            data: this.targetdata,
            fill: false,
            borderWidth: 2,
            backgroundColor: "transparent",
            borderColor: "gray",
          },
          {
            label: "Actual",
            data: this.actualdata,
            fill: false,
            borderColor: "black",
            backgroundColor: "transparent",
          },
          {
            label: "Forecast",
            data: this.forecastdata,
            fill: false,
            borderDash: [10, 4],
            borderColor: "black",
            backgroundColor: "transparent",
          },
          {
            label: "Today",
            data: [],
            fill: false,
            borderColor: "#01AA85",
            backgroundColor: "transparent",
          },
        ],
      };
      this.options = {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: true,
          position: "bottom",
        },
        elements: {
          point: {
            radius: 1,
          },
        },
        tooltips: {
          callbacks: {
            label: function (tooltipItem) {
              return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: vm.currencyCode,
              }).format(tooltipItem.value);
            },
          },
        },
        annotation: {
          annotations: [
            {
              type: "line",
              mode: "vertical",
              scaleID: "x-axis-0",
              value: moment(new Date()).format("DD ddd"),
              borderColor: "#01AA85",
              borderWidth: 4,
              label: {
                content: "TODAY",
                enabled: false,
                position: "top",
              },
            },
          ],
        },
        scales: {
          yAxes: [
            {
              ticks: {
                callback: function (value, index, values) {
                  return !vm.currencySymbol ? value : vm.currencySymbol + value;
                },
              },
            },
          ],
        },
      };
      this.initialchartdata = this.datacollection.datasets
    },
    // Get dates in current month
    getDates() {
      const currentMonthDates = new Array(moment().daysInMonth())
        .fill(null)
        .map((x, i) =>
          moment().startOf("month").add(i, "days").format("DD ddd")
        );
      return currentMonthDates;
    },
    onChangeBudgetGroup: function (event) {
      const selectedBudgetGroup = event.target.value
      switch (selectedBudgetGroup) {
        case "Target":
          this.datacollection.datasets = this.initialchartdata.filter(el => el.label === "Target")
          break
        case "Actual":
          this.datacollection.datasets = this.initialchartdata.filter(el => el.label === "Actual")
          break
        case "Forecast":
          this.datacollection.datasets = this.initialchartdata.filter(el => el.label === "Forecast")
          break
        case "Today":
          this.datacollection.datasets = this.initialchartdata.filter(el => el.label === "Today")
          break
        default:
          this.datacollection.datasets = this.initialchartdata
      }
      this.$refs.budgetCommanderChart.update()
    },
    getChartData: function () {

      if(!this.budget_commander_data_state.isSuccess)return

      this.budget_commander_promise.then(response => {

        this.results = this.budget_commander_response_data
        // Actual data is up until yesterday, forecast the rest of the month
        let yesterday = moment().subtract(1, 'day').date()
        let budgetactualdata = this.results.budget_actual_graph_data;
        this.days_in_month = budgetactualdata.length;
        this.actualdata = budgetactualdata.slice(0, yesterday);
        this.forecastdata = budgetactualdata;

        this.targetdata = [];
        let daily_budget = this.getBudget / this.days_in_month;
        let running_total = 0;
        for (let i = 0; i < this.days_in_month; i++) {
          running_total += daily_budget;
          this.targetdata.push(running_total);
        }
        this.fillData();

      })
    },
  },
};
</script>

<style scoped></style>
