<template>
  <div>
    <feed-items :cards="cards" v-if="showContent" />

    <div v-if="showContent">
      Showing {{ results_loaded }} of {{ total_results }}
    </div>

      <div class="row" v-if="data_state.isPending">
        <LoadingIndicator />
      </div>

      <div class="row" v-if="data_state.isError">
        <div class="col w-full">
          <h1>Error</h1>
          <h2>
            There was an error fetching your data. Please refresh the page.
          </h2>
          <h2>
            If problems persist please contact support.
          </h2>
        </div>
      </div>

      <div class="row" v-if="data_state.isEmpty">
        <div class="col w-full">
          <h2>There's nothing more to show here.</h2>
        </div>
      </div>

  </div>
</template>

<script>
import FeedItems from "./FeedItems.vue";
import { throttle } from "throttle-debounce";
import LoadingIndicator from "@/components/common/LoadingIndicator.vue"
import { mapGetters } from 'vuex'
import axios from "axios";
import DataState from '../../helpers/DataState'
import { isBottom } from '@/helpers/helpers'

export default {
  components: {
    FeedItems,
    LoadingIndicator,
  },

  name: "tab-content",

  data() {
    return {
      name: "new",
      cards: {},
      page: 0,
      total_results: 0,
      results_loaded: 0,
      account_id: '',
      currency_symbol: "$", //TODO make this dynamic
      data_state: new DataState(),
      page_to: 0,
      bottom: false,

    };
  },

  methods: {
    async updateCards(){
      this.page += 1;
      if(this.page===1){
        this.resetForFirstLoad()
      }

      this.data_state = new DataState();
      this.data_state.pending()

       var url =
          "/api/account/" +
          this.account_id +
          "/feed/" +
          this.name.toLowerCase() +
          "?page=" +
          this.page;


        await axios
        .get(url)
        .then(response => {

            const data = response.data
            let new_card_data = data.data
            if(this.page==1)this.cards = new_card_data;
            if(this.page>1)this.cards = Object.assign({}, this.cards, new_card_data);
                        
            if (new_card_data.length == 0) {
              this.data_state.empty()
              return;
            }
            this.total_results = data.total;
            this.results_loaded = data.to > this.results_loaded ? data.to : this.results_loaded//without the conditional the numebr is lowering at times

            this.data_state.success()

            if (this.bottomVisible()) {
              this.updateCards()
            }

            // console.log("Cards in response: " + Object.keys(data.data).length)
          })
          .catch((error) => {
            console.log("Fetch failed");
            console.log('url', url)
            console.log(error);
            this.data_state.error()

          });

        console.log('Num cards',Object.keys(this.cards).length)
    },
    bottomVisible() {
      return isBottom()
    },
    resetForFirstLoad(){
      console.log('resetting for first load')
      this.cards = {}
      this.total_results = 0
      this.results_loaded = 0
    }
  },
  watch: {
    bottom(bottom) {
      if (bottom) {
        if(this.loadingComplete)return
        this.updateCards()
      }
    }
  },
  created() {
    console.log("created");
    this.account_id = this.selected_account.account_id

    window.addEventListener('scroll', () => {
      if(this.loadingComplete)return
      this.bottom = this.bottomVisible()
    })

    Event.$on("selected", (name) => {
      console.log(this.name, 'was selected')
      console.log(name, 'now selected')
      console.log(name===this.name, 'name===this.name')
      if(name===this.name)return
      this.name = name
      this.page=0
      // this.resetForFirstLoad()
      // if(this.data_state.isPending)return
      
      this.updateCards()
    })
    
    this.updateCards()

  },
  computed:{
    ...mapGetters(["selected_account"]),
    loadingComplete(){
      this.page==this.page_total
    },
    showContent(){
      return this.data_state.isSuccess || this.page > 1
    }
  }
};
</script>

<style scoped></style>
