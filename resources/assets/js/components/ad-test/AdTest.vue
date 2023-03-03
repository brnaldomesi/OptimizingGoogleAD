<template>
  <div class="AdTest w-full">
    <main>
      <div class="row" v-if="Object.keys(adgroup_response_data).length > 0">
        <div class="col w-full">
          <div class="flex justify-between">
            <div class="flex">
              <div class="flex mr-6">
                <div class="flex bg-white w-8 h-8">
                  <span class="m-auto font-black text-xl text-danger">{{
                    ad_count
                  }}</span>
                </div>
                <div class="m-auto">&nbsp;&nbsp;Active Ads</div>
                <p class="m-auto" v-if="message == 'has_winners'">
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This Ad Group has a
                  winning ad.
                </p>
              </div>

              <div class="flex" v-if="message == 'has_winners'">
                <label class="form-control w-16 h-8 bg-white" title='Toggle on to highlight the advert differences'>
                  <input
                    type="checkbox"
                    class="form-control switch"
                    :checked="highlight_differences"
                    @change="toggleHighlightDifferences()"
                  />
                  <span class="marker-bar m-auto"></span>
                </label>
              </div>
            </div>

            <div class="pr-4" v-if="message == 'has_winners'">
              {{ date_range }}
            </div>
          </div>
        </div>

        <div v-if="message == 'no_ads'" class="pl-4 pb-4">
          <div
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
            role="alert"
          >
            <strong class="font-bold"
              >This Ad Group does not have any ads.</strong
            >
            <span class="block sm:inline">Create some below.</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
              <!-- <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg> -->
            </span>
          </div>
        </div>

        <div v-if="message == 'too_few_ads'" class="pl-4 pb-4">
          <div
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
            role="alert"
          >
            <strong class="font-bold"
              >This Ad Group does not have enough ads.</strong
            >
            <span class="block sm:inline"
              >At least 2 ads are recommended. Create some more below.</span
            >
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
              <!-- <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg> -->
            </span>
          </div>
        </div>

        <div id="adverts" class="col w-full" v-if="message == 'has_winners'">
          <advert
            :winning="true"
            :losing="false"
            :ad_data="advert_1"
            :conversion_rate_change_percentage="conversionRateChangePercentage"
            :ctr_change_percentage="ctrChangePercentage"
            :conversion_rate_significance="conversionRateSignificance"
            :ctr_significance="ctrSignificance"
            :differences='differences'
            :highlight_differences='highlight_differences'
          />

          <advert
            :winning="false"
            :losing="true"
            :ad_data="advert_2"
            :conversion_rate_change_percentage="conversionRateChangePercentage"
            :ctr_change_percentage="ctrChangePercentage"
            :conversion_rate_significance="conversionRateSignificance"
            :ctr_significance="ctrSignificance"
            :differences='differences'
            :highlight_differences='highlight_differences'
          />
        </div>

        <div class="col">
          <div class="card">
            <div class="row">
              <div class="AdTest__CreateAd col md:w-1/2">
                <AdTestCreateAd
                  :url_placeholder="url_placeholder"
                  :path_1_placeholder="path_1_placeholder"
                  :path_2_placeholder="path_2_placeholder"
                  :headline_1_placeholder="headline_1_placeholder"
                  :headline_2_placeholder="headline_2_placeholder"
                  :headline_3_placeholder="headline_3_placeholder"
                  :description_placeholder="description_placeholder"
                  :description_2_placeholder="description_2_placeholder"
                  :adgroup_id="adgroup_id"
                  :adgroup_google_id="adgroup_google_id"
                />
              </div>

              <div class="col md:w-1/2 mt-5">
                <ad-test-ad-builder />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <AllAdverts
          :ad_count="ad_count"
          :all_ads="all_ads"
          v-if="ad_count > 0"
        />
      </div>

      <div class="row" v-if="data_state.isPending">
        <div class="col w-full">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
      </div>

      <div class="row" v-if="data_state.isError">
        <div class="col w-full">
          <h1>Error</h1>
          <h2>
            There was an error fetching your data. Please refresh the page.
          </h2>
          <h2>
            If errors continue please contact support.
          </h2>
        </div>
      </div>

      <div class="row" v-if="data_state.isEmpty">
        <div class="col w-full">
          <h1 class='text-xl mb-20'>No Ad Group improvements were found</h1>
          <p>We checked for:</p>
          <ul>
            <li> - Winning ads</li>
            <li> - Ad Groups without ads</li>
            <li> - Ad Groups with too few ads</li>
          </ul>
          <p>But couldn't find any improvements. Check back tomorrow.</p>

        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Advert from "./Advert";
import AdTestCreateAd from "./AdTestCreateAd";
import AdTestAdBuilder from "./AdTestAdBuilder";
import AllAdverts from "./AllAdverts";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "AdTest",
  data() {
    return {
      headline: "",
      url: "",
      path_1: "",
      path_2: "",
      description: "",
      clicks: 0,
      impressions: 0,
      ctr: "",
      confidence: "",
      isLoser: true,
      account_id: this.$route.params.id,
      message: "",
      ad_count: 0,
      eta_ad_count: 0,
      rsa_ad_count: 0,
      adgroup_google_id: "",
      campaign_google_id: "",
      expanded_text_ads: {},
      responsive_search_ads: {},
      all_ads: {},
      date_range: "last_30_days",
      advert_1: {},
      advert_2: {},
      adgroup_id: "",
      highlight_differences: true,
      differences: {}
    };
  },
  components: {
    Advert,
    AdTestCreateAd,
    AllAdverts,
    AdTestAdBuilder
  },
  created() {
    this.clearAdGroupData();
    this.getAdGroupData({
      account_id: this.account_id,
      page: this.$route.query.page || 1
    });

    this.ad_test_promise.then(response => {
      this.getPlaceholders();

      this.message = this.adgroup_response_data["message"];

      this.ad_count = this.adgroup_response_data["ad_count"];
      this.eta_ad_count = this.adgroup_response_data["eta_ad_count"];
      this.rsa_ad_count = this.adgroup_response_data["rsa_ad_count"];

      this.adgroup_id = this.adgroup_response_data["id"];

      this.adgroup_google_id = this.adgroup_response_data["google_id"];
      this.campaign_google_id = this.adgroup_response_data["campaign"][
        "google_id"
      ];

      function sortAds(ads) {
        let sortable = [];
        for (let ad in ads) {
          sortable.push([
            ad,
            ads[ad].performance[Object.keys(ads[ad].performance)[0]].clicks
          ]);
        }

        sortable.sort(function(a, b) {
          return b[1] - a[1];
        });

        let sorted_ads = {};
        for (let ad_key in sortable) {
          let id = sortable[ad_key][0];
          sorted_ads[ad_key] = ads[id];
        }

        return sorted_ads;
      }

      this.expanded_text_ads = this.adgroup_response_data.expanded_text_ads;
      this.responsive_search_ads = this.adgroup_response_data.responsive_search_ads;

      this.all_ads = sortAds({
        ...this.expanded_text_ads,
        ...this.responsive_search_ads
      });

      for (let eta_key in this.expanded_text_ads) {
        let advert = this.expanded_text_ads[eta_key];
        let performance =
          advert.performance[Object.keys(advert.performance)[0]];
        advert.performance = performance;
        if (performance.ctr_message == "winning") this.advert_1 = advert;
        if (performance.ctr_message == "losing") this.advert_2 = advert;
        if (performance.conversion_rate_message == "winning")
          this.advert_1 = advert;
        if (performance.conversion_rate_message == "losing")
          this.advert_2 = advert;
      }

      this.date_range =
        typeof this.advert_1.performance != "undefined"
          ? styleDateString(this.advert_1.performance.date_range)
          : "";

      this.differences = {
        headline_1: this.advert_1.headline_1!==this.advert_2.headline_1,
        headline_2: this.advert_1.headline_2!==this.advert_2.headline_2,
        headline_3: this.advert_1.headline_3!==this.advert_2.headline_3,
        path_1: this.advert_1.path_1!==this.advert_2.path_1,
        path_2: this.advert_1.path_2!==this.advert_2.path_2,
        description: this.advert_1.description!==this.advert_2.description,
        description_2: this.advert_1.description_2!==this.advert_2.description_2,
      }
      this.differences['matching_ads'] =  Object.keys(this.differences).every((k) => !this.differences[k])
      this.differences['final_url'] = typeof this.advert_2.final_urls=='undefined' ? false : this.advert_1.final_urls[0]!==this.advert_2.final_urls[0]

    });


  },
  computed: {
    ...mapGetters(["adgroup_response_data", "data_state", "ad_test_promise"]),
    firstAdvertWinning() {
      return (
        this.advert_1.performance.conversion_rate_message == "winning" ||
        this.advert_1.performance.ctr_message == "winning"
      );
    },
    conversionRateChangePercentage() {
      return parseInt(
        ((this.advert_1.performance.conversion_rate -
          this.advert_2.performance.conversion_rate) /
          this.advert_2.performance.conversion_rate) *
          100
      );
    },
    ctrChangePercentage() {
      return parseInt(
        ((this.advert_1.performance.ctr - this.advert_2.performance.ctr) /
          this.advert_2.performance.ctr) *
          100
      );
    },
    ctrSignificance() {
      return this.advert_1.performance.ctr_significance;
    },
    conversionRateSignificance() {
      return this.advert_1.performance.conversion_rate_significance;
    }
  },
  methods: {
    ...mapActions(["getAdGroupData", "clearAdGroupData"]),
    toggleHighlightDifferences() {
      this.highlight_differences = !this.highlight_differences;
    },
    getPlaceholders() {
      /* Placeholders for the ad creator */
      this.url_placeholder = this.adgroup_response_data["url"];
      this.path_1_placeholder =
        this.adgroup_response_data["path_1"].trim() == "--"
          ? ""
          : this.adgroup_response_data["path_1"];
      this.path_2_placeholder =
        this.adgroup_response_data["path_2"].trim() == "--"
          ? ""
          : this.adgroup_response_data["path_2"];
      this.headline_1_placeholder = this.adgroup_response_data["headline_1"];
      this.headline_2_placeholder = this.adgroup_response_data["headline_2"];
      this.headline_3_placeholder = this.adgroup_response_data["headline_3"];
      this.description_placeholder = this.adgroup_response_data["description"];
      this.description_2_placeholder = this.adgroup_response_data[
        "description_2"
      ];
    }
  }
};

function styleDateString(string) {
  var sentence = string.toLowerCase().split("_");
  for (var i = 0; i < sentence.length; i++) {
    sentence[i] = sentence[i][0].toUpperCase() + sentence[i].slice(1);
  }
  return sentence.join(" ");
}
</script>

<style scoped>
#adverts .col {
  float: left;
  width: 49%;
  margin-left: 0;
  margin-right: 0;
  padding-left: 0;
  padding-right: 0;
}

#adverts .col:nth-child(1) {
  margin-right: 2% !important;
}
</style>
