<template>
  <div class="col" :class="{ lower_advert: isLowerAdvert }">
    <div
      class="border-2 w-full h-full rounded bg-white"
      :class="'border-' + getColourClass"
    >
      <div class="flex justify-between">
        <div
          class="text-white border-right-bottom-cut"
          :class="'bg-' + getColourClass"
        >
          {{ typeAdvert() }}
        </div>

        <button class="flex pr-8 mt-auto" @click="changeStatus">

          <div class="pr-1" v-if='data_state.isPending'>
            <div >
              <i class="fas fa-spinner fa-spin"></i>
            </div>
          </div>

          <div v-else>
            <img class="mr-1" :src="statusSrc" style="width:20px" />
          </div>

          <span class="font-semibold" :title="statusButtonTitleText">{{
            getStatus | capitalize
          }}</span>
        </button>
      </div>

      <div class="px-6 py-4">
        <div class="border p-4 mb-6 font-sans inner-box">
          <a :href="advert.url" target="_blank">
            <h3 class="text-2xl text-url-title">
              <span :class="{ highlight: isDifferent('headline_1') }">{{
                advert.headline_1
              }}</span
              >&nbsp;|&nbsp;<span
                :class="{ highlight: isDifferent('headline_2') }"
                >{{ advert.headline_2 }}</span
              >&nbsp;
              <span v-if="advert.headline_3">|</span>
              &nbsp;<span :class="{ highlight: isDifferent('headline_3') }">{{
                advert.headline_3
              }}</span>
              <div v-if="isRsa()" class="more_headlines_amount">
                <span>+{{ numberOfRemainingRsaHeadlines }} more</span>
              </div>
            </h3>

            <div class="flex">
              <div class="text-url-site mr-2">
                <span class="ad-border">Ad</span>

                <cite class="break-all not-italic">
                  {{ baseUrl }}
                  <span
                    :class="{ highlight: isDifferent('path_1') }"
                    v-if="advert.path_1 != ''"
                    >/{{ advert.path_1 }}</span
                  >
                  <span
                    :class="{ highlight: isDifferent('path_2') }"
                    v-if="advert.path_2 != ''"
                    >/{{ advert.path_2 }}</span
                  >
                </cite>
                &lrm;
              </div>
              <div class="flex">
                <span class="caret m-auto text-url-site"></span>
              </div>
            </div>
          </a>
          <div>
            <span :class="{ highlight: isDifferent('description') }">{{
              advert.description
            }}</span
            >&nbsp;<span :class="{ highlight: isDifferent('description_2') }">{{
              advert.description_2
            }}</span>
          </div>

          <div class="mt-2">
            <img
              src="/assets/img/ad-testing/icon-domain.svg"
              class="mr-2 float-left"
            />
            <span :class="{ highlight: isDifferent('final_url') }">{{
              advert.url
            }}</span>
          </div>
        </div>

        <div class="flex stats">
          <div class="flex flex-wrap mx-auto mb-2" v-if="conversionRateAdvert">
            <div class="mr-8">
              Clicks&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">
                {{ advert.clicks | number("0,0") }}</span
              >
            </div>
            <div class="mr-8">
              Cost&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">{{
                advert.cost | currency(currencySymbol)
              }}</span>
            </div>
            <div class="mr-8">
              Conversions&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">{{
                advert.conversions | number("0,0")
              }}</span>
            </div>
            <div class="mr-8">
              Conv. Rate&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold"
                >{{ advert.conversion_rate }}%</span
              >
              <span
                v-bind:class="{
                  'text-success': winning,
                  'text-danger': !winning
                }"
                class="font-semibold"
                v-if="!isLowerAdvert"
                >&nbsp;&nbsp;({{ changePercentage }}%)</span
              >
            </div>
            <div v-if="!isLowerAdvert">
              Confidence&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold"
                >{{ conversion_rate_significance }}%</span
              >
            </div>
          </div>

          <div class="flex flex-wrap mx-auto mb-2" v-if="!conversionRateAdvert">
            <div class="mr-8">
              Clicks&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">{{
                advert.clicks | number("0,0")
              }}</span>
            </div>
            <div class="mr-8">
              Cost&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">{{
                advert.cost | currency(currencySymbol)
              }}</span>
            </div>
            <div class="mr-8">
              Impressions&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold">{{
                advert.impressions | number("0,0")
              }}</span>
            </div>
            <div class="mr-8">
              CTR&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold"
                >{{ advert.ctr }}%</span
              >
              <span
                v-bind:class="{
                  'text-success': winning,
                  'text-danger': !winning
                }"
                class="font-semibold"
                v-if="!isLowerAdvert"
                >&nbsp;&nbsp;({{ changePercentage }}%)</span
              >
            </div>
            <div v-if="!isLowerAdvert">
              Confidence&nbsp;&nbsp;
              <span class="text-gray-700 text-lg font-semibold"
                >{{ ctr_significance }}%</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import DataState from "../../helpers/DataState";

export default {
  name: "Advert",
  props: {
    winning: {
      required: true,
      type: Boolean
    },
    losing: {
      required: true,
      type: Boolean
    },
    ad_data: {
      required: true,
      type: Object
    },
    conversion_rate_change_percentage: {
      required: false,
      type: [Number, String]
    },
    ctr_change_percentage: {
      required: false,
      type: [Number, String]
    },
    conversion_rate_significance: {
      required: false,
      type: [Number, String]
    },
    ctr_significance: {
      required: false,
      type: [Number, String]
    },
    differences: {
      required: false,
      type: Object
    },
    highlight_differences: {
      required: false,
      type: Boolean
    }
  },
  data() {
    return {
      advert: {},
      data_state: new DataState()
    };
  },
  mounted() {
    this.advert = this.getAdvert(this.ad_data);
  },
  methods: {
    ...mapActions(["createAlert", "updateAdvertStatus"]),
    isDifferent(line) {
      if (!this.differences) return false;
      if (!this.highlight_differences) return false;
      return this.differences[line];
    },
    getTestType() {
      if (!this.differences) return "Ad";
      return this.differences.matching_ads ? "Landing Page" : "Ad";
    },
    typeAdvert: function() {
      if (this.winning) return "Winning " + this.getTestType();
      if (this.losing) return "Losing " + this.getTestType();
      return this.ad_data.ad_type;
      return "Ad";
    },
    changeStatus() {
      this.data_state = new DataState();
      this.data_state.pending();

      this.updateAdvertStatus({
        entity_google_id: this.ad_data.google_id,
        entity_id: this.ad_data.id,
        new_status: this.changeToStatus
      })
        .then(response => {
          if (!response) {
            this.data_state.error();
            const alert_payload = {
              headline: "Something went wrong",
              message:
                "We had trouble updating this advert's status. Please check your\
                 internet connection and try again. Error: Network error.",
              dismissSecs: 5
            };
            this.createAlert(alert_payload);
            return;
          }

          this.data_state.success();
          this.advert.status = this.changeToStatus;
          this.createAlert({
            headline: "Advert " + this.advert.status,
            message: "",
            dismissSecs: 5
          });
        })
        .catch(e => {
          this.data_state.error();
          const alert_payload = {
            headline: "Something went wrong",
            message:
              "We had trouble updating this advert's status. Please check your internet connection and try again. " +
              e,
            dismissSecs: 5
          };
          this.createAlert(alert_payload);
        });
    },
    isRsa() {
      return this.ad_data.ad_type == "Responsive search ad";
    },
    getAdvert(advert) {
      // advert = advert.adgroup_response_data
      const is_rsa = this.isRsa();

      const eta_rsa_map = {
        headline_1: "responsive_search_ad_headlines",
        headline_2: "responsive_search_ad_headlines",
        headline_3: "responsive_search_ad_headlines",
        description: "responsive_search_ad_descriptions",
        description_2: "responsive_search_ad_descriptions",
        path_1: "responsive_search_ad_path_1",
        path_2: "responsive_search_ad_path_2"
      };

      function getLine(eta_line, rsa_index) {
        let line;

        function formatLine(text) {
          if (text == null) return "";
          return text.trim() == "--" ? "" : text;
        }

        if (!is_rsa) {
          return formatLine(advert[eta_line]);
        }

        if (is_rsa && eta_line.indexOf("path") > -1) {
          return formatLine(advert[eta_rsa_map[eta_line]]);
        }

        if (is_rsa) {
          return formatLine(advert[eta_rsa_map[eta_line]][rsa_index].assetText);
        }
      }

      let performance =
        Object.keys(advert.performance).length == 1
          ? advert.performance[Object.keys(advert.performance)[0]]
          : advert.performance;

      return {
        headline_1: getLine("headline_1", 0),
        headline_2: getLine("headline_2", 1),
        headline_3: getLine("headline_3", 2),
        description: getLine("description", 0),
        description_2: getLine("description_2", 1),
        path_1: getLine("path_1"),
        path_2: getLine("path_2"),
        url: advert.final_urls[0],
        google_id: advert.google_id,
        id: advert.id,
        status: advert.status,
        clicks: performance.clicks,
        impressions: performance.impressions,
        conversions: performance.conversions,
        ctr: performance.ctr == null ? 0 : performance.ctr,
        cost: performance.cost,
        conversion_rate:
          performance.conversion_rate == null ? 0 : performance.conversion_rate,
        conversions: performance.conversions,
        conversion_rate_significance: performance.conversion_rate_significance,
        ctr_significance: performance.ctr_significance,
        conversion_rate_message: performance.conversion_rate_message,
        ctr_message: performance.ctr_message,
        performance: performance
      };
    }
  },

  computed: {
    ...mapGetters(["selected_account"]),
    statusButtonTitleText() {
      return "Click to change the status to " + this.changeToStatus;
    },
    numberOfRemainingRsaHeadlines() {
      if (this.isRsa) {
        return this.ad_data.responsive_search_ad_headlines.length < 4
          ? 0
          : this.ad_data.responsive_search_ad_headlines.length - 3;
      }
    },
    getStatus() {
      return this.advert.status;
    },
    changeToStatus() {
      return this.getStatus == "enabled" ? "paused" : "enabled";
    },
    statusSrc() {
      return "/assets/img/ad-testing/" + this.getStatus + ".svg";
    },
    baseUrl() {
      if (!this.advert.url) return "";
      return this.advert.url.replace(/(\/\/[^\/]+)?\/.*/, "$1");
    },
    paths() {
      if (this.advert.path_2 == "") {
        return this.path_1 == "" ? "" : "/" + this.path_1;
      }
      return "/" + this.path_1 + "/" + this.advert.path_2;
    },
    conversionRateAdvert() {
      if (!this.advert.performance) return false;
      if (
        !this.conversion_rate_change_percentage &&
        !this.ctr_change_percentage
      )
        return false;
      return this.advert.performance.conversion_rate_message !== "";
    },
    changePercentage() {
      if (!this.advert.performance) return "";
      if (
        !this.conversion_rate_change_percentage &&
        !this.ctr_change_percentage
      )
        return "";
      let prefix = "+";
      if (!this.winning) prefix = "-";

      if (this.conversionRateAdvert)
        return prefix + this.conversion_rate_change_percentage;
      if (!this.conversionRateAdvert)
        return prefix + this.ctr_change_percentage;
    },
    currencySymbol() {
      return this.selected_account.account_currency_symbol;
    },
    getColourClass() {
      if (this.winning) return "success";
      if (this.losing) return "danger";
      return "blue-600";
    },
    isLowerAdvert() {
      //whether the ads at the bottom i.e. is neither a winner or loser
      return !this.winning && !this.losing;
    }
  }
};
</script>

<style scoped>
.inner-box {
  min-height: 177px;
}

.stats {
  min-height: 54px;
}

.lower_advert:nth-child(odd) {
  margin-right: 2% !important;
}

.more_headlines_amount {
  font-size: 0.6em !important;
  color: #797e94;
  float: right;
  margin: 10px;
}

.highlight {
  background-color: aliceblue;
}
</style>
