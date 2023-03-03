<template>
  <div class="Accounts w-full" v-scroll="handleScroll">
    <main :class="{'collpased': helpMenuClicked}">

      <div class="py-2">
        <div class="flex flex-wrap justify-between">
          <div class="flex">
            <div class="flex mr-6 ml-10">
              <div class="flex flex-wrap bg-white py-1 px-3 text-xl font-semibold">
                <div class="mt-auto mb-auto">
                  <span style="color:#CA2D78;">{{number_of_synced_accounts}}</span>
                  <span>/&nbsp;{{list.length - number_of_synced_accounts }}</span>
                </div>
              </div>
              <div class="flex text-xl">
                <div class="m-auto">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Active Accounts</div>
              </div>
            </div>
            <Search
              placeholder="Search accounts.."
              @handleSearchInput="debounceSearch"
            />
          </div>
          <div class="pr-4">Last 30 days</div>

        </div>
      </div>

      <div class="py-2 flex content-end">  
       
      </div>
      
      <div class="py-2">
        <LoadingIndicator v-if="loading" />
        <div v-else>
          <AccountsList
            :list='list.filter(el => el.is_synced == true && el.ad_performance_report_processed_at)'
            :searchKey='searchKey'
            :perPage='perPage'
            :is_synced='true'
          />

          <p class="font-bold mt-4 mb-4 ml-10 text-xl"> Inactive Accounts </p>

          <AccountsList
            :list='list.filter(el => el.is_synced == false || (el.is_synced == true && !el.ad_performance_report_processed_at))'
            :searchKey='searchKey'
            :perPage='perPage'
            :is_synced='false'
          />
        </div>
      </div>
      <!-- <sidebar-modals-container/>
      <modal name="featureModal" 
            :width="1100"
            :height="700"
          >
        <Canny :user = 'user' />
      </modal>
      <Help 
        @onHelpClick='onHelpClick'
        @onHelpMenuClick='onHelpMenuClick'
        @onChatMenuClick='onChatMenuClick'
        @onSuggestMenuClick='onSuggestMenuClick'
        :helpClicked='helpClicked'
      /> -->
    </main>
  </div>
</template>

<script>
import AccountsList from "./AccountsList.vue"
import { 
  mapState,
  mapActions, 
} from 'vuex'
import * as types from '@/store/modules/accounts/types'
import { debounce } from 'debounce'
import { DEBOUNCE } from '@/config/constants'
import { isBottom } from '@/helpers/helpers'
import LoadingIndicator from '@/components/common/LoadingIndicator.vue'
import Search from '@/components/common/Search.vue'
// import Help from '@/components/common/Help.vue'
// import HelpMenu from '@/components/common/HelpMenu.vue'
// import Canny from '@/components/common/Canny.vue'
import _ from 'lodash'

export default {
  name: "Accounts",
  data () {
    return {
      user: this.$route.params.user_id,
      searchKey: '',
      perPage: 20,
      // helpClicked: false,
      // helpMenuClicked: false
    }
  },
  components: {
    AccountsList,
    LoadingIndicator,
    Search,
    // Help,
    // Canny
  },
  directives: {
    scroll: {
      inserted: function(el, binding) {
        let f = function (evt) {
          if (binding.value(evt, el)) {
            window.removeEventListener('scroll', f)
          }
        }
        window.addEventListener('scroll', f)
      }
    }
  },
  created () {
    window.scrollTo(0, 0)

    if(this.list.length === 0) {
      this[types.GET_ACCOUNTS](this.user).then(res => {
        this[types.GET_CURRENCY](res[0].id)

        if(res.length < 20) {
          this.perPage = res.length===0 ? 20 : res.length
        }
      })
    }

    this[types.GET_NUMBER_OF_SYNCED_ACCOUNTS](this.user).then(res => {
      
    }, error => {
      console.error(error)
    })

    this[types.GET_ACCOUNTS](this.user).then(res => {

    }, error => {
      console.error(error)
    })

    let self = this
    this.debounceSearch = debounce( e => {
      self.searchKey = e.target.value
      self.perPage = self.list.length < 20 && self.list.length > 0 ? self.list.length : 20
    }, DEBOUNCE.DEFAULT)
  },
  
  methods:{
    ...mapActions('accounts', [
      types.GET_ACCOUNTS,
      types.GET_CURRENCY,
      types.GET_NUMBER_OF_SYNCED_ACCOUNTS,
    ]),
    handleScroll: function(evt) {
      if(isBottom()) {
        this.perPage + 20 < this.list.length ? this.perPage += 20 : this.perPage = this.list.length
      }
    },
    // onHelpMenuClick: function() {
    //   this.helpMenuClicked = true
    //   this.$sidebarModal.show(HelpMenu, {
    //     text: ''
    //   }, {
    //     height: '100%',
    //     width: '500px',
    //     clickToClose: false,
    //   }, {
    //     'before-close': (event) => { this.helpClicked = false; this.helpMenuClicked = false },
    //   })
    // },
    // onChatMenuClick: function() {
    //   this.helpClicked = false
    // },
    // onSuggestMenuClick: function() {
    //   this.helpClicked = false
    //   this.$modal.show('featureModal')
    // },
    // onHelpClick: function(clicked) {
    //   this.helpClicked = clicked
    // },
  },

  computed:{
    ...mapState('accounts', {
      list: state => state.list,
      loading: state => state[`${_.camelCase(types.GET_ACCOUNTS)}Pending`],
      number_of_synced_accounts: state => state.number_of_synced_accounts
    }),
    ...mapState({helpMenuClicked: state => state.helpMenuClicked})

  },
}

</script>

<style scoped>

</style>