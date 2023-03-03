<template>
  <div>

    <div class="row pr-10">
      <div
        class="col flex justify-center cursor-pointer"
        :class="{ 'bg-background': isSelected('headlines') }"
        @click="getHeadlines"
      >
        <div class="mr-2">
          <img src="/assets/img/ad-testing/headline.svg" />
        </div>
        <div class="flex ">
          <p 
          class="mb-auto"
          :class="{ 'font-black': isSelected('headlines') }"
          >HeadLines</p>
        </div>
      </div>

      <div
        class="col flex justify-center bg-white cursor-pointer"
        :class="{ 'bg-background': isSelected('descriptions') }"
        @click="getDescriptions"
      >
        <div class="mr-2">
          <img src="/assets/img/ad-testing/description.svg" />
        </div>
        <div>
          <p 
          class="m-auto"
          :class="{ 'font-black': isSelected('descriptions') }"
          >Descriptions</p>
        </div>
      </div>

      <div
			class="col flex justify-center bg-white cursor-pointer"
				:class="{ 'bg-background': isSelected('n_grams') }"
        @click="getNGrams"
      >
        <div class="mr-2">
          <img src="/assets/img/ad-testing/tile.svg" />
        </div>
        <div>
          <p class="m-auto"
          :class="{ 'font-black': isSelected('n_grams') }"
          >Ngrams</p>
        </div>
      </div>
    </div>

    <div class="row pr-10">
      <div class="p-2 flex-grow relative text-center bg-background">
        <p
          class="mb-auto cursor-pointer"
          :class="{ ' font-black': isSelected('account') }"
          @click="getAccount"
        >
          Account
        </p>
      </div>
      <div class="p-2 flex-grow relative text-center bg-background">
        <p
          class="mb-auto  cursor-pointer"
          :class="{ ' font-black': isSelected('campaign') }"
          @click="getCampaign"
        >
          Campaign
        </p>
      </div>
      <div class="p-2 flex-grow relative text-center bg-background">
        <p
          class="mb-auto  cursor-pointer"
          :class="{ ' font-black': isSelected('adgroup') }"
          @click="getAdgroup"
        >
          Ad Group
        </p>
      </div>
    </div>

    <div v-for="line in lines" v-if='data_state.isSuccess'>
      <div class="row pr-10">
        <div class="flex mt-2 w-full border rounded">
          <div class="flex bg-background py-6 px-3 mr-4">
            <img
              class="m-auto"
              src="/assets/img/ad-testing/accordion-left.svg"
            />
          </div>
          <div class="flex">
            <p
              class="m-auto text-1xl text-url-title font-sans cursor-move"
              v-on:dragstart="dragStart"
              v-on:drag="dragging"
              draggable="true"
              id="dragtarget"
            >
              {{ line }}
            </p>
          </div>
        </div>
      </div>
    </div>

		<div class="row pr-10" v-if='data_state.isEmpty'>
        <div class="flex mt-2 w-full border rounded">
          <div class="flex bg-background py-6 px-3 w-full">
						<p>There isn't enough data for this {{bottom_selected_tab | capitalize}}</p>
					</div>
				</div>
			</div>

		<div class="row pr-10" v-if='data_state.isPending'>
        <div class="flex mt-2 w-full border rounded">
          <div class="flex bg-background py-6 px-3 w-full">
							<div>
								<i class="fas fa-spinner fa-spin" ></i>
							</div>
						<p>&nbsp;&nbsp;Loading {{bottom_selected_tab | capitalize}} {{top_selected_tab.replace('_','') | capitalize}}...</p>
					</div>
				</div>
			</div>

    <div class="row pr-10 mt-10 mr-14 mb-1">
      <div class="flex w-full">
        <div class="flex bg-background py-6 px-3 mr-4 opacity-0">
          <img class="m-auto" src="/assets/img/ad-testing/accordion-left.svg" />
        </div>
        <div class="flex" v-if="keyword_string != ''">
          <span class="m-auto flex font-black"
            >Keywords ({{ number_of_keywords }})
            <p class="font-medium">&nbsp;{{ keyword_string }}</p></span
          >
        </div>
        <div class="flex" v-else>
          <span class="m-auto flex font-black"
            >Keywords:
            <p class="font-medium">&nbsp;No keywords found</p></span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";
import DataState from "../../helpers/DataState";

export default {
  name: "AdTestAdBuilder",
  data() {
    return {
      lines: [],
      account: this.$route.params.id,
      top_selected_tab: "headlines",
      bottom_selected_tab: "account",
      keyword_string: "",
      number_of_keywords: 0,
      adgroup_id: "",
			campaign_id: "",
			data_state: new DataState()
    };
  },
  created() {
    this.updateLines();
    this.getKeywordString();
  },
  methods: {
    updateLines() {
			this.data_state = new DataState();
      this.data_state.pending();
      let id =
        this.bottom_selected_tab == "adgroup"
          ? this.adgroup_id + "/"
          : this.campaign_id + "/";
      if (this.bottom_selected_tab == "account") id = "";
      let url =
        "/api/account/" +
        this.account +
        "/adtest/" +
        this.bottom_selected_tab +
        "/" +
        id +
        this.top_selected_tab;
      axios
        .get(url)
        .then(response => {
          this.lines = response.data;
					this.data_state.success();
					if(this.lines.length==0)this.data_state.empty()
        })
        .catch(() => {
          this.data_state.error();
        });
    },

    getNGrams() {
      this.top_selected_tab = "n_grams";
      this.updateLines();
    },
    getDescriptions() {
      this.top_selected_tab = "descriptions";
      this.updateLines();
    },
    getHeadlines() {
      this.top_selected_tab = "headlines";
      this.updateLines();
    },
    getAccount() {
      this.bottom_selected_tab = "account";
      this.updateLines();
    },
    getCampaign() {
      this.bottom_selected_tab = "campaign";
      this.updateLines();
    },
    getAdgroup() {
      this.bottom_selected_tab = "adgroup";
      this.updateLines();
    },
    isSelected(name) {
      if (name == "headlines" || name == "descriptions" || name=='n_grams') {
        return this.top_selected_tab == name;
      }
      return this.bottom_selected_tab == name;
    },
    dragStart: function(event) {
      event.dataTransfer.setData("Text", event.target.innerHTML.trim());
    },
    dragging: function(event) {
      let type = this.top_selected_tab.split("");
      type.pop();
      document.getElementById("drag-instructions").innerHTML =
        "drop over a " + type.join("");
    },
    getKeywordString() {
      function formatKeyword(text, match_type) {
        if (match_type == "Exact") return "[" + text + "]";
        if (match_type == "Phrase") return '"' + text + '"';
        return text;
      }
      const max_keywords = 5;
      let suffix = "";
      this.ad_test_promise.then(response => {
        this.campaign_id = this.adgroup_response_data.campaign_id;
        this.adgroup_id = this.adgroup_response_data.id;
        let keyword_array = [];
        let keywords = this.adgroup_response_data.keywords;
        let ad_number = 1;
        for (let key in keywords) {
          let keyword_data = keywords[key];
          keyword_array.push(
            formatKeyword(
              keyword_data.keyword_text,
              keyword_data.keyword_match_type
            )
          );
          if (ad_number >= max_keywords) {
            let remaining_keywords = parseInt(Object.keys(keywords).length) - (ad_number + 1);
            suffix = " + " + remaining_keywords + " more";
            break;
          }
          ad_number++;
        }

        this.keyword_string = keyword_array.join(", ") + suffix;
        this.number_of_keywords = keywords.length;
        if (this.number_of_keywords == 0) {
          this.keyword_string = "";
        }
      });
    }
  },
  computed: {
    ...mapGetters(["adgroup_response_data", "ad_test_promise"])
  }
};
</script>

<style scoped>
.keywords {
  margin: 30px 20px 0 0;
}
</style>
