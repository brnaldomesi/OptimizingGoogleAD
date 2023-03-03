<template>
  <div class="col w-full">
    <div class="card">
      <div class="row">
        <div class="col w-full lg:w-1/2 xl:w-2/3">
          <div class="card shadow-none">
            <div class="card-title flex justify-between" v-if="groupState === 'create'">
              Creating a new Budget Group
              <Search
                placeholder="Search"
                @handleSearchInput="debounceSearch"
              />
            </div>
            <div class="card-title flex justify-between" v-else>
              Editing Budget Group
              <Search
                placeholder="Search"
                @handleSearchInput="debounceSearch"
              />
            </div>
            <div class="card-body w-full">
              <div class="h-6">
                <span v-if="selectedCampaigns.length > 0">
                  {{selectedCampaigns.length}} campaigns selected
                </span>
                <span class="text-sm text-red-700 text-right " v-else>
                  No campaigns selected
                </span>
              </div>
              <div class="mt-10 overflow-y-auto overflow-x-hidden" style="height: 610px;">
                <table class="w-full">
                  <thead>
                    <tr>
                      <th class="w-1/12 border border-t-0 border-l-0 border-gray-400 px-4 py-2">
                        <div class="flex my-auto">
                          <label class="form-control w-16 h-8 bg-white">
                            <input
                              type="checkbox"
                              class="form-control switch"
                              v-model="selectAllCampaign"
                            />
                            <span class="marker-bar m-auto"></span>
                          </label>
                        </div>
                      </th>
                      <th class="w-5/12 border border-t-0 border-gray-400 px-4 py-2 text-left">
                        Campaign
                      </th>
                      <!-- <th class="w-1/12 border border-t-0 border-gray-400 px-4 py-2 text-center">
                        Status
                      </th> -->
                      <th class="w-2/12 border border-t-0 border-gray-400 px-4 py-2">
                        Campaign Type
                      </th>
                      <th class="w-3/12 border border-t-0 border-r-0 border-gray-400 px-4 py-2 text-left">
                        Existing Budget Group
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="campaign in filterList" :key="campaign.id">
                      <td class="border border-l-0 border-gray-400 px-4 py-2">
                        
                        <Tooltip
                          :class="'text-center cursor-default mx-auto'"
                          :underline=false
                          :content="'You can not select this campaign since it already belongs to a Budget Group'"
                          :position="'center-right'"
                          v-if="campaign.budget_group_id && groupState === 'create'"
                        >
                          <div class="flex my-auto">
                            <label class="form-control w-16 h-8 bg-white">
                              <input
                                type="checkbox"
                                class="form-control switch"
                                :value="campaign.id"
                                disabled
                                :checked="selectedCampaigns.includes(campaign.id)"
                              />
                              <span class="marker-bar m-auto"></span>
                            </label>
                          </div>
                        </Tooltip>
                        <div class="flex my-auto" v-else>
                            <label class="form-control w-16 h-8 bg-white">
                              <input
                                type="checkbox"
                                class="form-control switch"
                                :value="campaign.id"
                                v-model="selectedCampaigns"
                                :checked="selectedCampaigns.includes(campaign.id)"
                              />
                              <span class="marker-bar m-auto"></span>
                            </label>
                        </div>
                      </td>
                       <td class="border border-gray-400 px-4 py-2 text-left relative">
                        <img :src="'/assets/img/ad-testing/'+campaign.status+'.svg'" :title="'Campaign ' +campaign.status" width="15px" class="m-auto absolute mt-1">
                        <span class="ml-6">{{ campaign.name }}</span>
                      </td>
                      <td class="border border-gray-400 px-4 py-2 text-center">
                        {{ campaign.ad_network_type_1 }}
                      </td>
                      <td class="border border-r-0 border-gray-400 px-4 py-2 text-left">
                        {{ campaign.budget_group_name }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col w-full lg:w-1/2 xl:w-1/3">
          <div class="card shadow-none">
            <div class="card-title">
              Budget Group Settings
            </div>
            <div class="card-body">
              <div class="form-row">
                <label class="w-1/3 mr-6" for="budget-group-name">Budget Name</label>
                <div class="flex-grow relative">
                  <input
                    class="form-control w-full"
                    v-model="budget_group_name"
                    id="budget-group-name"
                  />
                </div>
              </div>
              <div class="form-row flex justify-end -mt-5 mb-1" v-if="validationErr[0].err">
                <span class="text-sm text-red-700 text-right ">{{ validationErr[0].msg }}</span>
              </div>
              <div class="form-row">
                <label class="w-1/3 mr-6">Target Spend</label>
                <div class="flex-grow relative">
                  <Tooltip
                    :content=getBudgetTooltip
                    :position="'center-left'"
                    :underline="false"
                    class="w-full"
                    v-if="includeRollover"
                  >
                    <div class="absolute rollover_icon">
                      <i  class="fas fa-redo-alt" style="color:#02a27f;"></i>
                    </div>

                    <CurrencyInput
                      class="form-control w-full"
                      :value="combined_budget"
                      :currency="currencyCode"
                      locale="en"
                      @focus="changeToOriginalBudget"
                      @blur="updateCombinedBudget"
                    />
                  </Tooltip>

                  <CurrencyInput
                    class="form-control w-full"
                    :value="group_budget"
                    :currency="currencyCode"
                    locale="en"
                    v-if="!includeRollover"
                    @blur="updateCombinedBudget"
                  />
                </div>
              </div>
              <div class="form-row flex justify-end -mt-5 mb-2" v-if="validationErr[1].err">
                <span class="text-sm text-red-700 text-right ">{{ validationErr[1].msg }}</span>
              </div>
              <div class="form-row">
                <label class="w-1/3 mr-6">Target KPI</label>
                <div class="flex-grow flex flex-no-wrap">
                  <input class="form-control danger" type="radio"
                    id="group_cpa"
                    value="cpa"
                    v-model="group_kpi_name"
                  >
                  <label for="group_cpa" class="mr-4">CPA</label>
                  <input class="form-control dark" type="radio"
                    id="group_roas"
                    value="roas"
                    v-model="group_kpi_name"
                  >
                  <label for="group_roas">ROAS</label>
                </div>
              </div>

              <div class="form-row">
                <label class="w-1/3 mr-6">{{group_kpi_name | capitalize }} Target</label>
                <div class="flex-grow">
                  <CurrencyInput
                    :currency="currencyCode"
                    v-model="group_kpi_value"
                    class="form-control w-full"
                    locale="en"
                    />
                </div>
              </div>
              <div class="form-row flex justify-end -mt-5 mb-1" v-if="validationErr[2].err">
                <span class="text-sm text-red-700 text-right ">{{ validationErr[2].msg }}</span>
              </div>
              <div class="mt-10 BudgetCommander__settings-alert">
                <div class="card-body">
                  <div class="form-row">
                    <div class="w-1/2 mr-6">
                      <Tooltip
                        :content="'If you enable Optimisation, we’ll also email you about those changes too.'"
                        :title="'Email me'"
                        :position="'center-left'"
                      />
                      if spend exceeds budget
                    </div>
                    <label class="form-control flex" for="groupEmailMe">
                      <input
                        class="form-control switch"
                        type="checkbox"
                        name="st1"
                        id="groupEmailMe"
                        v-model="emailMeOption"
                      />
                      <span class="marker"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="mt-10 BudgetCommander__settings-optimisation">
                <div class="card-body">
                  <!-- Pause Campaigns -->
                  <div class="form-row">
                    <div class="w-1/2 mr-6">
                      <Tooltip
                        :content="'If spend exceeds the total monthly budget, pause all campaigns in the account.'"
                        :title="'Pause account'"
                        :position="'center-left'"
                      />
                      if monthly budget reached
                    </div>
                    <label class="flex" for="groupPauseCampaignsOption">
                      <input
                        class="form-control switch"
                        type="checkbox"
                        :value="true"
                        name="st1"
                        id="groupPauseCampaignsOption"
                        v-model="pauseCampaignsOption"
                      />
                      <span class="marker"></span>
                    </label>
                  </div>

                  <!-- Re-enable Campaigns -->
                  <div class="form-row">
                    <div class="w-1/2 mr-6">
                      <Tooltip
                        :content="'Re-enable paused campaigns the following month (or if budget is increased above spend).'"
                        :title="'Re-enable Campaigns'"
                        :position="'center-left'"
                      />
                    </div>
                    <label class="flex" for="groupReenableCampaignsOption">
                      <input
                        class="form-control"
                        type="checkbox"
                        id="groupReenableCampaignsOption"
                        v-model="reenableCampaignsOption"
                        :disabled="!pauseCampaignsOption"
                      />
                      <span class="marker"></span>
                    </label>
                  </div>

                  <div class="card-title">Budget Optimisation</div>
                  <!-- Rollover Budget -->
                  <div class="form-row">
                    <div class="w-1/2 mr-6">
                      <Tooltip
                        :content="'Any unspent budget from this month will be added to your budget for next month.'"
                        :title="'Rollover'"
                        :position="'center-left'"
                      />
                      Budget
                    </div>
                    <label class="flex" for="groupRolloverBudgetOption">
                      <input
                        class="form-control"
                        type="checkbox"
                        id="groupRolloverBudgetOption"
                        v-model="rolloverBudgetOption"
                        @change="changeRolloverOption"
                      />
                      <span class="marker"></span>
                    </label>
                  </div>

                  <!-- Control Spend -->
                  <div class="form-row">
                    <div class="w-1/2 mr-6">
                      <Tooltip
                        :content="'Hourly monitoring - pause all campaigns for the day if they spend more than your expected daily budget.'"
                        :title="'Emergency Stop'"
                        :position="'center-left'"
                      />
                    </div>
                    <label class="flex" for="groupEmergencyStopOption">
                      <input
                        class="form-control"
                        type="checkbox"
                        id="groupEmergencyStopOption"
                        v-model="emergencyStopOption"
                      />
                      <span class="marker"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-center mt-12 mb-6">
                <button 
                  class="red-primary"
                  @click="createNewBudgetGroup"
                >
                  Save Budget
                </button>
                <button 
                  class="gray-secondary"
                  @click="cancelSave"
                >
                  Cancel
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
import { mapGetters, mapState, mapActions } from 'vuex'
import { debounce } from 'debounce'
// import { uuid } from 'vue-uuid'
import { DEBOUNCE } from '@/config/constants'
import Search from '@/components/common/Search.vue'
import Tooltip from "@/components/common/Tooltip.vue"
import { uuidv4 } from '@/helpers/helpers'

export default {
  name: "CreateGroup",
  components: {
    Search,
    Tooltip
  },
  props: {
    initialValue: {
      type: Object,
      required: true,
    },
    groupState: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      budget_group_name: this.groupState === 'create' ? '' : this.initialValue.budget_group_name,
      group_budget: this.groupState === 'create' ? 0 : Number(this.initialValue.budget),
      group_kpi_name: this.groupState === 'create' ? 'cpa' : this.initialValue.kpi_name,
      group_kpi_value: this.groupState === 'create' ? 0 : Number(this.initialValue.kpi_value),
      emailMeOption: this.groupState === 'create' ? 0 : this.initialValue.notify_via_email,
      pauseCampaignsOption: this.groupState === 'create' ? 0 : this.initialValue.pause_campaigns,
      reenableCampaignsOption: this.groupState === 'create' ? 0 : this.initialValue.enable_campaigns,
      rolloverBudgetOption: this.groupState === 'create' ? 0 : this.initialValue.rollover_spend,
      controlSpendOption: this.groupState === 'create' ? 0 : this.initialValue.control_spend,
      emergencyStopOption: this.groupState === 'create' ? 0 : this.initialValue.emergency_stop,
      combined_budget: 0,
      search: '',
      group_id: this.groupState === 'create' ? '' : this.initialValue.budget_group_id,
      selectedCampaigns: [], 
      validationErr: [
        { 
          err: 0,
          msg: ''
        },
        { 
          err: 0,
          msg: ''
        },
        { 
          err: 0,
          msg: ''
        }
      ]
    }
  },
  created () { 
    let self = this
    this.debounceSearch = debounce( e => {
      self.search = e.target.value
    }, DEBOUNCE.DEFAULT) 
  },
  async mounted () {
    this.setup()
    await this.getBudgetCommanderCampaignData({
      account_id : this.selected_account.account_id
    })
    this.selectedCampaigns = this.groupState === 'create' ? [] : this.campaigns.filter(el => el.budget_group_id === this.initialValue.budget_group_id).map(el => el.id)
  },
  computed: {
    ...mapState({
      campaigns: state => state.budget_commander.campaign_list,
      budgetGroupList: state => state.budget_commander.budget_group_list
    }),
    ...mapGetters([
      'selected_account'
    ]),
    currencyCode() {
      return this.selected_account.account_currency_code
    },
    filterList() {
      if(this.search !== '') {
        const filteredCampaigns =  this.campaigns.filter(el => {
          let name = el.name + el.ad_network_type_1
          return name.toLowerCase().includes(this.search.toLowerCase())
        })
        const filteredSelectedCampaigns = this.selectedCampaigns.filter(el => filteredCampaigns.some(o => o.id === el))
        this.selectedCampaigns = filteredSelectedCampaigns
        return filteredCampaigns
      } else {
        return this.campaigns
      }
    },
    excessSpend(){
      return this.initialValue.excess_budget
    },
    includeRollover(){
      return this.rolloverBudgetOption && this.excessSpend>0
    },
    getBudgetTooltip(){
      const date = new Date()
      const month = date.toLocaleString('default', { month: 'long' });
      return this.selected_account.account_currency_symbol + this.excessSpend.toLocaleString() + ' of unspent budget rolled over from last month. Your budget for '+month+' is '+this.selected_account.account_currency_symbol+(this.group_budget+this.excessSpend).toLocaleString()
    },
    selectAllCampaign: {
      get: function () {
        return this.filterList ? this.selectedCampaigns.length == this.filterList.length : false
      },
      set: function (value) {
        let selected = []
        let self = this
        if (value) {
          this.filterList.forEach(function (campaign) {
            if ((campaign.budget_group_id === null) || ((self.groupState === 'edit') && (campaign.budget_group_id === self.initialValue.budget_group_id)))
              selected.push(campaign.id)
          })
        } else {
          selected = []
        }
        this.selectedCampaigns = selected
      }
    }
  },
  methods: {
    ...mapActions([
      "getBudgetCommanderCampaignData",
    ]),
    setup(){
      this.combined_budget = this.group_budget + this.excessSpend
    },
    selectBudgetGroup: function (event, campaignId) {
      _.find(this.campaigns, { id: campaignId }).group_id = event.target.value
    },
    cancelSave: function () {
      if (this.budgetGroupList.length === 0) {
        this.$emit('updateGroupState', 'empty')
      } else {
        this.$emit('updateGroupState', 'list')
      }
    },
    changeToOriginalBudget(){
      this.combined_budget = this.group_budget
    },
    async updateCombinedBudget(budget){
      if (isNaN(Number(budget.target.value))){//prevent the currency symbol from being submitted
        return
      }
      this.group_budget = Number(budget.target.value)
      this.combined_budget = this.group_budget + this.excessSpend
    },
    changeRolloverOption(event){
      if (event.target.value) {
        this.combined_budget = this.group_budget + this.excessSpend
      } else {
        this.combined_budget = this.group_budget
      }
    },
    updateValidationData(field, err, msg) {
      
      this.validationErr[field].err = err
      this.validationErr[field].msg = msg
    },
    checkBudgetGroupName() {
      
      if (this.budget_group_name.length === 0) {
        this.updateValidationData(0, 1, "Please enter a Budget Group name.")
        return true
      }
      
      if (this.budget_group_name.toLowerCase() === 'master') {
        this.updateValidationData(0, 1, "Sorry, 'Master' is reserved.")
        return true
      } 

      const checkCreating = (this.groupState === 'create') && (this.budgetGroupList.find(el => el.budget_group_name.toLowerCase() === this.budget_group_name.toLowerCase()))
      const checkUpdating = (this.groupState === 'edit') && (this.initialValue.budget_group_name.toLowerCase() !== this.budget_group_name.toLowerCase()) && (this.budgetGroupList.find(el => el.budget_group_name.toLowerCase() === this.budget_group_name.toLowerCase()))
      
      if (checkCreating || checkUpdating){
        this.updateValidationData(0, 1, "Please choose a unique Budget Group name.")
        return true
      }
      return false
    },
    checkBudget(){
      let err = false
      if (this.group_budget === 0) {
        this.updateValidationData(1, 1, "Target Spend must be greater than 0")
        err = true
      }
      if (this.group_kpi_value === 0) {
        this.updateValidationData(2, 1, "KPI Target must be greater than 0")
        err = true
      }
      return err
    },
    createNewBudgetGroup: function () {
      
      let budgetGroup = {}
      
      let errName = this.checkBudgetGroupName()
      let errBudget = this.checkBudget()
      let errCampaigns = this.selectedCampaigns.length === 0 ? true : false

      if (errName || errBudget || errCampaigns) {
        return
      }
      if (this.groupState === 'create') {
        const newUUID = uuidv4()
        budgetGroup.budget_group_id = newUUID
      } else {
        budgetGroup = this.initialValue
      }

      budgetGroup.account_id = this.selected_account.account_id
      budgetGroup.budget = this.group_budget
      budgetGroup.budget_group_name = this.budget_group_name
      budgetGroup.campaign_ids = this.selectedCampaigns
      budgetGroup.control_spend = this.controlSpendOption
      budgetGroup.emergency_stop = this.emergencyStopOption
      budgetGroup.enable_campaigns = this.reenableCampaignsOption
      budgetGroup.kpi_name = this.group_kpi_name
      budgetGroup.kpi_value = this.group_kpi_value
      budgetGroup.notify_via_email = this.emailMeOption
      budgetGroup.pause_campaigns = this.pauseCampaignsOption
      budgetGroup.rollover_spend = this.rolloverBudgetOption
      this.$emit('updateBudgetGroup', budgetGroup)
    }
  }
}
</script>

<style scoped>
.rollover_icon{
    right: 10px;
    top: 10px;
}
</style>