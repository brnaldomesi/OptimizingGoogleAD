<template>
  <div
    id="opacityAnimation"
    class="Feed__Item"
    :class="{ disappear: !card_is_visable }"
  >
    <div class="rounded bg-white mb-10 ">
      <div class="flex flex-wrap justify-between">
        <div class="flex flex-wrap">
          <div
            class="headline-max-width rounded-tl text-white border-right-bottom-cut"
            :class="headlineClass"
          >
            {{ card.headline }}&nbsp;&nbsp;&nbsp;
          </div>

          <div class="flex max-w-sm">
            <div class="mr-5"></div>
            <p
              class="px-2 m-auto mx-1 bg-gray-200 "
              v-bind:class="account_locations.length > 1 ? 'tailed' : ''"
            >
              {{ account_locations[0] }}
            </p>
            <p
              class="px-2 m-auto mx-1 bg-gray-200"
              v-if="account_locations.length > 1"
            >
              {{ account_locations[1] }}
            </p>
          </div>
        </div>

        <div class="flex pr-8 my-auto">
          <!-- <img class="mr-3 opacity-50 object-contain mt-auto" src="images/919bcc52dc0394d892a0b84a5d02b461.svg">
                <img class="mr-3 opacity-50" src="images/5d21e378a0789456e2139923575d1748.svg">
                <img class="opacity-50" src="images/df2cb9379bf5f8a9297e8d667c97299b.svg"> -->

          <a
            class="mr-3 opacity-50 object-contain mt-auto cursor-pointer"
            @click="archiveCard"
            ><i :class="feedItem.getIconClass('archive')"></i>
          </a>

          <a class="mr-3 opacity-50 cursor-pointer" @click="snoozeCard"
            ><i :class="feedItem.getIconClass('snooze')"></i>
          </a>

          <a
            class="opacity-50 cursor-pointer"
            @click="minimiseCard"
            :class="{ hidden: !minimised }"
          >
            <i :class="feedItem.getIconClass('maximise')"></i>
          </a>

          <a
            class="opacity-50 cursor-pointer"
            :class="{ hidden: minimised }"
            @click="minimiseCard"
          >
            <i :class="feedItem.getIconClass('minimise')"></i>
          </a>
        </div>
      </div>

      <div id="grow" :class="getMinimiseClass">
        <div class="px-6 pb-4 mt-8">
          <div class="border p-4 font-sans">
            <h3 class="text-2xl text-url-title"></h3>

            <div v-if="feed_item_type == 'keyword_feed'">
              <p>
                Keyword Text
                <span class="ad-border">{{ this.card.keyword_text }}</span>
              </p>
              <p>
                Keyword Match Type
                <span class="ad-border">{{
                  this.card.keyword_match_type
                }}</span>
              </p>

            </div>
            <div v-if="feed_item_type == 'adgroup_feed'">
              <p>
                AdGroup&nbsp;&nbsp;
                <span class="ad-border">{{ this.card.adgroup.name }}</span>
              </p>
              <p v-if="card.adgroup.message == 'too_few_ads'">
                Add Count&nbsp;&nbsp;<span class="ad-border">{{
                  this.card.adgroup.ad_count
                }}</span>
              </p>
            </div>

            <div v-if="feed_item_type == 'search_query_feed'">
              <p>
                Search Query&nbsp;&nbsp;
                <span class="ad-border">{{ this.card.query }}</span>
              </p>
              <small>This search query's data is aggregated from all match types.</small>

            </div>

            <div v-if="feed_item_type == 'search_query_n_gram_feed'">
              <p>
                Search Query Ngram&nbsp;&nbsp;
                <span class="ad-border">{{
                  this.card.performance.n_gram
                }}</span>
              </p>
              <p>
                Ngram Count&nbsp;&nbsp;
                <span class="ad-border">{{
                  this.card.performance.n_gram_count
                }}</span>
              </p>
              <small>This search query Ngram's data is aggregated from all match types.</small>

            </div>
            <div v-if="feed_item_type == 'ad_n_gram_feed'">
              <p>
                Advert Ngram&nbsp;&nbsp;
                <span class="ad-border">{{
                  this.card.performance.n_gram
                }}</span>
              </p>
              <p>
                Ngram Count&nbsp;&nbsp;
                <span class="ad-border">{{
                  this.card.performance.n_gram_count
                }}</span>
              </p>
            </div>

            <div v-if="feed_item_type == 'advert_feed'">
              <a href="#">
                <h3 class="text-2xl text-url-title">
                  {{ this.card.advert.headline_1 }} |
                  {{ this.card.advert.headline_2 }}
                  {{
                    this.advertLineExists(this.card.advert.headline_3)
                      ? "| " + this.card.advert.headline_3
                      : ""
                  }}
                </h3>
                <div class="flex">
                  <div class="text-url-site mr-2">
                    <span class="ad-border">Ad</span>
                    <cite class="break-all not-italic"
                      >{{ this.card.advert.domain
                      }}{{
                        this.advertLineExists(this.card.advert.path_1)
                          ? this.card.advert.path_1
                          : ""
                      }}{{
                        this.advertLineExists(this.card.advert.path_2)
                          ? "/" + this.card.advert.path_2
                          : ""
                      }}</cite
                    >‎
                  </div>
                  <div class="flex">
                    <span class="caret m-auto text-url-site"></span>
                  </div>
                </div>
              </a>
              <div>
                {{ this.card.advert.description }}
                {{
                  this.advertLineExists(this.card.advert.description_2)
                    ? this.card.advert.description_2
                    : ""
                }}
              </div>
            </div>

            <!-- <div>
                    Fast, Affordable, Licensed Commercial Plumbing Services <span class="">Call Before it's too Late</span>, 0% Finance Options Available - Call Now for a Free Estimate! 
                  </div> -->
          </div>

          <div class="flex py-6">
            <div class="flex flex-wrap justify-end ml-auto">
              <div class="flex mx-6">
                <p class="m-auto text-">
                  <small
                    ><strong>{{ styled_date_range }}</strong></small
                  >
                </p>
              </div>

              <div>
                Clicks&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{ clicks }}</span
                >
              </div>
              <div class="ml-8">
                Impressions&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{ impressions }}</span
                >
              </div>
              <div class="ml-8">
                Cost&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{
                    cost
                      | currency(this.selected_account.account_currency_symbol)
                  }}</span
                >
              </div>
              <div class="ml-8">
                CTR&nbsp;&nbsp;<span class="text-gray-700 text-lg font-semibold"
                  >{{ ctr }}%</span
                >
              </div>
              <div class="ml-8">
                Conversions&nbsp;&nbsp;<span class="text-gray-700 text-lg font-semibold"
                  >{{ conversions }}</span
                >
              </div>


              <div class="ml-8" v-if="feed_item_type == 'keyword_feed'">
                First Page Cpc&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{
                    firstPageCpc
                      | currency(this.selected_account.account_currency_symbol)
                  }}</span
                ><!--<span class="text-success font-semibold">&nbsp;&nbsp;(+22 %)</span>-->
              </div>

              <div class="ml-8" v-if="feed_item_type == 'keyword_feed'">
                Top of Page Cpc&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{
                    topOfPageCpc
                      | currency(this.selected_account.account_currency_symbol)
                  }}</span
                ><!--<span class="text-success font-semibold">&nbsp;&nbsp;(+22 %)</span>-->
              </div>


              <div class="ml-8" v-if="showConfidence">
                Confidence&nbsp;&nbsp;<span
                  class="text-gray-700 text-lg font-semibold"
                  >{{ confidence }}</span
                >
              </div>
            </div>
          </div>

          <div class="flex">
            <div class="flex flex-wrap justify-end ml-auto">
              <div class="mr-6 text-right">
                <div>{{ card.message }}</div>
                <div>{{ card.suggestion }}</div>
              </div>
              <div class="flex">
                <button
                  class="bg-gray-700 hover:bg-gray-900 text-white py-3 px-8 rounded mr-3"
                  @click="archiveCard"
                >
                  Archive
                </button>
                <button
                  class="evo-pink-bg hover:bg-red-900 text-white py-3 px-8 rounded"
                  @click="handlePause"
                  v-if="showPause"
                  :disabled="data_state.isSuccess"
                  :class="{'cursor-not-allowed':data_state.isSuccess}"
                >
                <div v-if='data_state.isIdle'>
                  Pause
                </div>
                <div class="pr-1" v-if='data_state.isPending'>
                  <div >
                    <i class="fas fa-spinner fa-spin"></i>
                  </div>
                </div>
                <div class="pr-1" v-if='data_state.isSuccess'>
                  <div >
                    <i class="fas fa-check pr-1"></i>Paused
                  </div>
                </div>



                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import DataState from "../../helpers/DataState";

class FeedItem {
  getIconClass(name) {
    var icons = {
      minimise: "fas fa-minus",
      maximise: "fas fa-plus",
      archive: "fas fa-archive",
      snooze: "fas fa-clock",
      danger: "fas fa-exclamation-triangle",
      success: "fas fa-smile",
      normal: "fas fa-bolt",
      warning: "fas fa-exclamation-triangle"
    };
    return icons[name];
  }
}

export default {
  name: "feed-items",

  props: ["card"],

  data() {
    return {
      feedItem: new FeedItem(),
      minimised: true,
      card_is_visable: true,
      alert_class: "alert-primary",
      table_name: getFeedItemType(this.card),
      data_state: new DataState()

    };
  },

  beforeCreate() {},

  mounted() {
    this.alert_class = "alert-" + this.card.priority;
    if (this.card.priority == "success") this.headline_icon = "fas smile";
    // console.log(this.card)
  },

  methods: {
    ...mapActions(["updateAdvertStatus","updateKeywordStatus", "createAlert"]),
    handlePause() {

      //send request
      //update button if successful
      //add checkmark and update text to 'paused'
      //disable the button

      this.data_state = new DataState();
      this.data_state.pending();
      
      let promise = this.feed_item_type=='advert_feed' ? this.updateAdvertStatus({
        entity_google_id: this.card.advert.google_id,
        entity_id: this.card.advert.id,
        new_status: 'paused'
      }) : this.updateKeywordStatus({
        entity_google_id: this.card.keyword.google_id,
        entity_id: this.card.keyword.id,
        new_status: 'paused'
      })

      promise.then(response => {
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
 
        })
        .catch(e => {
          const alert_payload = {
            headline: "Something went wrong",
            message:
              "We had trouble updating this advert's status. Please check your internet connection and try again. " +
              e,
            dismissSecs: 5
          };
          this.createAlert(alert_payload);
          this.data_state.error();

        });
    },
    minimiseCard() {
      this.minimised = !this.minimised;
    },
    archiveCard() {
      console.log("archiving card");
      this.card_is_visable = false;
      this.archiveFeedItem(this.card.id, this.table_name);
    },
    snoozeCard() {
      this.card_is_visable = false;
      this.snoozeFeedItem(this.card.id, this.table_name);
    },
    async archiveFeedItem(feed_item_id, table_name) {
      await axios
        .post("/api/account/" + this.account + "/archive_feed_item", {
          id: feed_item_id,
          table_name: table_name
        })
        .then(response => {
          console.log("archived...");
        })
        .catch(error => {
          console.log(error);
        });
    },
    async snoozeFeedItem(feed_item_id, table_name) {
      await axios
        .post("/api/account/" + this.account + "/snooze_feed_item", {
          id: feed_item_id,
          table_name: table_name
        })
        .then(response => {
          // console.log("snoozed...")
        });
    },
    // async pauseEntity (feed_item_id, table_name) {
    //     await axios.post('/api/account/' + this.account + '/snooze_feed_item',{
    //         id: feed_item_id,
    //         table_name: table_name
    //     }).then(response => {
    //         // console.log("archived...")
    //     });

    // },
    advertLineExists(line) {
      if (line.indexOf("--") > -1) return false;
      if (!line) return false;
      return true;
    }
  },
  computed: {
    ...mapGetters(["selected_account"]),
    headlineClass() {
      if (this.card.priority == "danger") {
        return "bg-danger";
      }

      if (this.card.priority == "success") {
        return "bg-success";
      }

      return "bg-warning";
    },
    getMinimiseClass() {
      return this.minimised ? "minimise" : "maximise";
    },
    cardPerformance() {
      if (this.feed_item_type == "adgroup_feed") {
        return this.card.adgroup.performance[
          Object.keys(this.card.adgroup.performance)[0]
        ];
      }
      if (this.feed_item_type == "search_query_feed") {
        return this.card.search_query.performance;
      }
      return this.card.performance;
    },
    styled_date_range() {
      // `this` points to the vm instance
      if (this.feed_item_type == "adgroup_feed") {
        return this.cardPerformance.date_range.replace(/_/g, " ");
      }
      return this.card.date_range == "" || this.card.date_range == null
        ? ""
        : this.card.date_range.replace(/_/g, " ");
    },
    account_locations() {
      var feed_item_type = getFeedItemType(this.card);
      return getParent(this.card, feed_item_type);
    },
    feed_item_type() {
      return getFeedItemType(this.card);
    },

    clicks() {
      return this.cardPerformance ? this.cardPerformance.clicks : 0;
    },
    cost() {
      return this.cardPerformance ? this.cardPerformance.cost : 0;
    },
    conversions() {
      return this.cardPerformance ? this.cardPerformance.conversions : 0;
    },
    impressions() {
      return this.cardPerformance ? this.cardPerformance.impressions : 0;
    },
    confidence() {
      return this.cardPerformance ? this.cardPerformance.confidence : 0;
    },
    cpa() {
      return this.cardPerformance ? this.cardPerformance.cpa : 0;
    },
    ctr() {
      return this.cardPerformance ? this.cardPerformance.ctr : 0;
    },
    firstPageCpc() {
      return Number(this.card.keyword.first_page_cpc)/1000000
    },
    topOfPageCpc() {
      return Number(this.card.keyword.top_of_page_cpc)/1000000
    },
    showPause() {
      return (
        this.feed_item_type == "keyword_feed" ||
        this.feed_item_type == "advert_feed"
      );
    },
    showConfidence() {
      return this.confidence > 0 && this.feed_item_type == "advert_feed";
    }
  }
};

/*Get the parent(s) as an array
There will be 0, 1 or 2 parents
*/
function getParent(card, feed_item_type) {
  if (feed_item_type === "ad_n_gram_feed") return ["Account Level"];
  if (feed_item_type === "search_query_feed") return ["Account Level"];
  if (feed_item_type === "search_query_n_gram_feed") return ["Account Level"];

  if (feed_item_type === "adgroup_feed")
    return [card.adgroup.campaign.name, card.adgroup.name];
  if (feed_item_type === "advert_feed")
    return [card.advert.adgroup.campaign.name, card.advert.adgroup.name];
  if (feed_item_type === "keyword_feed")
    return [card.keyword.adgroup.campaign.name, card.keyword.adgroup.name];

  throw "Error finding feed item parent";
}

//get the type of feed item e.g. keyword
//we can then store data against the type e.g. level - adgroup, campaign, or account

function getFeedItemType(card) {
  if (card.n_gram_id) {
    return "ad_n_gram_feed";
  }
  if (card.adgroup_id) {
    return "adgroup_feed";
  }
  if (card.advert_id) {
    return "advert_feed";
  }
  if (card.keyword_text) {
    return "keyword_feed";
  }
  if (card.query) {
    return "search_query_feed";
  }
  if (card.search_query_n_gram_id) {
    return "search_query_n_gram_feed";
  }

  throw "Error: the feed item type couldn't be determined";
}
</script>

<style scoped>
.disappear {
  opacity: 0 !important;
  max-height: 0 !important;
}

#opacityAnimation {
  -moz-transition: opacity 1s, max-height 1.5s;
  -ms-transition: opacity 1s, max-height 1.5s;
  -o-transition: opacity 1s, max-height 1.5s;
  -webkit-transition: opacity 1s, max-height 1.5s;
  transition: opacity 1s, max-height 1.5s;
  opacity: 1;
  max-height: 600px;
  overflow: hidden;
}
</style>
