<template>
  <div
    class="w-full sm:w-1/2 md:w-1/4 mt-6 xl:mt-0 BudgetCommander__overview--stat"
  >
    <!-- Attributes VS -->
    <div>
      {{ getPrimaryAttribute }}
      <span v-if="getSecondaryAttribute">vs {{ getSecondaryAttribute }}</span>
    </div>

    <!-- Get Percentage -->
    <div>
      <p
        v-if="getSecondaryAttribute"
        class="text-2xl font-bold text-main-dark"
        :class="getStatPercentageClass"
      >
        <Tooltip
          :content="getTooltipText"
          :title="getStatPercentageText"
          :position="'center-right'"
          :class="'font-semibold'"
        />
      </p>
      <p
        v-if="!getSecondaryAttribute && getPrimaryAttribute == 'Conversions'"
        class="text-2xl font-bold text-main-dark"
      >
        {{ getPrimaryAttributeAmount || 0 }}
      </p>
      <p
        v-else-if="
          !getSecondaryAttribute && getPrimaryAttribute == 'Conv. Value'
        "
        class="text-2xl font-bold text-main-dark"
      >
        {{ getPrimaryAttributeAmount || 0 | currency(getCurrency) }}
      </p>
    </div>

    <div class="card-body">
      <small v-if="getSecondaryAttribute" class="block mb-2"
        >{{ getPrimaryAttribute }} vs {{ getSecondaryAttribute }}</small
      >
      <small v-if="!getSecondaryAttribute" class="block mb-2"
        >Forecasted {{ getPrimaryAttribute }}</small
      >
      <!-- <img class="inline-block" src="/assets/img/budget-commander/down-normal.svg" /> -->
      <strong class="text-main-light">
        <!-- Attributes VS Amounts -->
        <span class="text-normal" v-if="getPrimaryAttribute == 'Conversions'">{{
          getForecastedAmount(getPrimaryAttributeAmount) || 0
        }}</span>
        <span
          class="text-normal"
          v-else-if="getPrimaryAttribute == 'Conv. Value'"
          >{{
            getForecastedAmount(getPrimaryAttributeAmount) || 0
              | currency(getCurrency)
          }}</span
        >
        <span class="text-normal" v-else>{{
          getPrimaryAttributeAmount | currency(getCurrency)
        }}</span>
        <span v-if="getSecondaryAttribute"
          >vs {{ getSecondaryAttributeAmount | currency(getCurrency) }}</span
        >
      </strong>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Tooltip from "@/components/common/Tooltip.vue";

export default {
  name: "BudgetCommanderSummaryStat",
  components: {
    Tooltip,
  },
  props: {
    stat: {
      type: Object,
      required: false,
      default: () => {},
    },
    budget: {
      type: Number,
      required: true,
    },
    kpiTarget: {
      type: Number,
      required: true,
    },
  },
  computed: {
    ...mapGetters(["selected_account"]),
    getTooltipText() {
      if (this.getSecondaryAttribute == "Budget") {
        return (
          this.getPrimaryAttribute +
          " as a percentage of " +
          this.getSecondaryAttribute
        );
      }
      return (
        this.getPrimaryAttribute +
        " Vs " +
        this.getSecondaryAttribute +
        " percentage"
      );
    },
    getCurrency() {
      if (this.getPrimaryAttribute === "ROAS") return "";
      return this.selected_account.account_currency_symbol;
    },
    getPrimaryAttribute() {
      return this.stat.primaryAttribute;
    },
    getSecondaryAttribute() {
      return this.stat.secondaryAttribute;
    },
    getPrimaryAttributeAmount() {
      return this.stat.primaryAttributeAmount;
    },
    getSecondaryAttributeAmount() {
      if (this.getSecondaryAttribute === "Budget") {
        return this.budget;
      } else if (this.getSecondaryAttribute === "Target") {
        return this.kpiTarget;
      }
      return this.stat.secondaryAttributeAmount;
    },
    // Get Stat Percentage
    getStatPercentage() {
      let statPercentage = 0;
      const primary = this.getPrimaryAttributeAmount;
      const secondary = this.getSecondaryAttributeAmount;
      if (Math.min(primary, secondary) < 0.1) return 0;

      if (this.getSecondaryAttribute == "Budget") {
        return Math.round((primary / secondary) * 100);
      }

      return this.getVsPercentage(secondary, primary)
    },
    getStatPercentageText() {
      let prefix = this.getStatPercentage > 0 ? '+' : ''
      if (this.getSecondaryAttribute == "Budget")prefix = ''
      if(!this.getStatPercentage)return '-'
      return prefix + String(this.getStatPercentage) + "%";
    },
    // Get Colour Coding for Statistic Percentage
    getStatPercentageClass() {
      const statPercentage = Math.ceil(this.getStatPercentage);
      // console.log('statPercentage', statPercentage)
      // SET 1
      if (this.getSecondaryAttribute === "Budget") {
        // Under 5% and over -5% -- GREEN
        if (statPercentage > 105) return "text-danger";
        if (statPercentage <= 100) return "text-success";
        // Over 5% and under -20% -- RED
        // Everything else -- AMBER
        return "text-warning";
      } else if (this.getPrimaryAttribute === "ROAS") {
        //ROAS: high is good!
        // Under 5% and over -5% -- GREEN
        if (statPercentage >= 0) return "text-success";
        if (statPercentage >= -5) return "text-warning";
        // Over 5% and under -20% -- RED
        if (statPercentage < -20) return "text-danger";
        // Everything else -- AMBER
        return "text-warning";
      } else if (this.getPrimaryAttribute === "CPA") {
        if (statPercentage < 0 && statPercentage > -5) return "text-warning";
        if (statPercentage <= 0) return "text-success";
        return "text-danger";
      } else {
        // SET 2
        // Under 5% and over -5% -- GREEN
        if (statPercentage <= 5 && statPercentage >= -5) return "text-success";
        // Everything else -- RED
        return "text-danger";
      }
    },
  },
  methods: {
    getForecastedAmount(amount) {
      let now = new Date();
      let days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
      let date = now.getDate() - 1;
      if (date == 1) return 0;
      return date == 1 ? 0 : Math.round((amount / date) * days[now.getMonth()]);
    },
    getVsPercentage(kpiTarget, kpiValue){
      //used for cpa and roas vs target
      //returns the vs percentage as a number
      let vs = ((kpiValue-kpiTarget)/kpiTarget)*100
      return Math.round(vs)
    },
  },
};
</script>

<style scoped></style>
