<template>
  <section class="Accounts__item--AccountChart" v-if="data">
    <header class="Accounts__item--AccountChart__header">
      <div>
        <label class="Accounts__item--AccountChart__name">{{slug}}</label>
      </div>
      <div class="flex justify-between mt-4">
        <label v-if="['CTR', 'Conversion Rate'].includes(slug)">{{value}}%</label>
        <label v-else-if="['CPA', 'Average CPC', 'Conversion Value'].includes(slug)">{{value | currency(account_currency_symbol)}}</label>
        <label v-else>{{value}}</label>
        <label :class="`percent-${this.color}`">{{percent}}%</label>
      </div>
    </header>
    <div class="Accounts__item--AccountChart__data mt-4">
      <trend-chart 
        :datasets="[dataset]" 
        :min="0" padding="5 5 0" 
      >
      </trend-chart>
    </div>
  </section>
</template>
<script>
  export default {
    name: "AccountChart",
    props: {
      data: {
        type: Array,
        required: true
      },
      slug: {
        type: String,
        required: true
      },
      color: {
        type: String,
        required: true
      },
      value: {
        type: [String, Number],
        required: true
      },
      percent: {
        type: String,
        required: true
      },
      account_currency_symbol: {
        type: String,
        required: false,
      },
    },
    data() {
      return {
        currentInfo: null
      };
    },
    computed: {
      weeklyDownloads() {
        return this.numberWithSpaces(this.data.reduce((a, c) => a + c.value, 0));
      },
      info() {
        return {
          value: this.currentInfo ? this.currentInfo.value : this.weeklyDownloads
        }
      },
      dataset() {
        return {
          data: this.data,
          showPoints: true,
          fill: true,
          className: `curve-${this.color}`
        };
      },
    },
    methods: {
      numberWithSpaces(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
      }
    }
}
</script>