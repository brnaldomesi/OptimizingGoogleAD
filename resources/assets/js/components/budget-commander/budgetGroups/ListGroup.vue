<template>
  <div class="col w-full">
    <div class="card">
      <div class="card-title flex justify-between">
        Budget Group
        <div v-if="updateStatus">
          <button 
            class="gray-secondary"
            @click="changeUpdateStatus(false)"
          >
            Cancel
          </button>
        </div>
        <div class="flex justify-center" v-else>
          <button 
            class="red-primary"
            @click="$emit('updateGroupState', 'create')"
          >
            Create Budgets
          </button>
          <button 
            class="gray-secondary"
            @click="changeUpdateStatus(true)"
          >
            Edit Budgets
          </button>
        </div>
      </div>
      <div class="card-body w-full">
        <table class="w-full mt-5">
          <thead>
            <tr>
              <th class="w-4/12 border border-t-0 border-l-0 border-gray-400 px-4 py-2">
                Budget Groups
              </th>
              <th class="w-2/12 border border-t-0 border-gray-400 px-4 py-2 text-left">
                Date range
              </th>
              <th class="w-1/12 border border-t-0 border-gray-400 px-4 py-2">
                KPI Target
              </th>
              <th class="w-2/12 border border-t-0 border-gray-400 px-4 py-2 text-right">
                Target spend
              </th>
              <th class="w-2/12 border border-t-0 border-gray-400 px-4 py-2 text-right">
                Current spend
              </th>
              <th class="w-1/12 border border-t-0 border-r-0 border-gray-400 px-4 py-2">
                Actual KPI
              </th>
              <th v-if="updateStatus" class="w-1/12 border border-l-0 border-t-0 border-r-0 border-gray-400 px-4 py-2">
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="group in budgetGroupList">
              <td class="border border-l-0 border-gray-400 px-4 py-2">
                {{ group.budget_group_name }}
              </td>
              <td class="border border-gray-400 px-4 py-2">
                {{ dateRange.firstDay | moment("Do MMM") }} - {{ dateRange.lastDay | moment("Do MMM YYYY") }}
              </td>
              <td class="border border-gray-400 px-4 py-2 text-center">{{ group.kpi_value || 0 | currency(currencyCode) }}</td>
              <td class="border border-gray-400 px-4 py-2 text-right">{{ group.budget || 0 | currency(currencyCode) }}</td>
              <td class="border border-gray-400 px-4 py-2 text-right">{{ group.cost || 0 | currency(currencyCode) }}</td>
              <td class="border border-r-0 border-gray-400 px-4 py-2 text-center">
                {{ getActualKPI(group) || 0 | currency(currencyCode) }}
              </td>
              <td class="border border-l-0 border-r-0 border-gray-400 px-4 py-2 text-center" v-if="updateStatus">
                <div class="flex justify-between">
                  <font-awesome-icon 
                    :icon="['fas', 'pencil-alt']" 
                    :style="{ cursor: 'pointer' }" 
                    @click="$emit('setSelectedGroup', group.budget_group_id)"
                  />
                  <font-awesome-icon 
                    :icon="['fas', 'trash-alt']" 
                    :style="{ color: '#da3d4c', cursor: 'pointer' }"
                    @click="$emit('goDeleteSelectedGroup', group.budget_group_id)"
                  />
                </div>
              </td>
            </tr>
            <!-- <tr v-if="budgetGroupList.length > 0">
              <td class="border border-l-0 border-gray-400 px-4 py-2">
                Everything else
              </td>
              <td class="border border-gray-400 px-4 py-2 text-left">
                {{ dateRange.firstDay | moment("Do MMM") }} - {{ dateRange.lastDay | moment("Do MMM YYYY") }}
              </td>
              <td class="border border-gray-400 px-4 py-2 text-center">{{ 123 || 0 | currency(currencyCode )}}</td>
              <td class="border border-gray-400 px-4 py-2 text-right">{{ 123 || 0 | currency(currencyCode) }}</td>
              <td class="border border-gray-400 px-4 py-2 text-right text-redPrimary">{{ 432423 || 0 | currency(currencyCode) }}</td>
              <td class="border border-r-0 border-gray-400 px-4 py-2 text-center text-redPrimary">
               {{ 123213 || 0 | currency(currencyCode) }}
              </td>
              <td v-if="updateStatus" class="border border-l-0 border-r-0 border-gray-400 px-4 py-2 text-center">
              </td>
            </tr> -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import Vue from 'vue'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import { mapGetters } from "vuex";

Vue.use(VueMoment, {
    moment,
})

export default {
  name: "ListGroup",
  props: {
    budgetGroupList: {
      type: Array,
      required: true,
    },
    updateEnabled: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      updateStatus: this.updateEnabled

    }
  },
  computed: {
    ...mapGetters([
      'selected_account',
    ]),
    currencyCode(){
      return this.selected_account.account_currency_symbol
    },
    dateRange(){
      const dates = {}
      dates.firstDay = moment().startOf('month').format('YYYY-MM-DD hh:mm')
      dates.lastDay = moment().endOf('month').format('YYYY-MM-DD hh:mm')
      return dates
    }
  },
  methods: {
    changeUpdateStatus: function (stat) {
      this.updateStatus = stat
    },
    getActualKPI(group) {
      if (group.kpi_name === 'cpa') {
        return parseFloat(group.cost)/parseFloat(group.conversions)
      } else {
        return parseFloat(group.conversion_value)/parseFloat(group.cost)
      }
    }
  }
}
</script>

<style scoped>

</style>