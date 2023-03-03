<template>
  <!-- Budget Commander View -->
  <div class="BudgetCommander w-full">
    <main>
      <div class="row">
        <!-- Budget Commander Summary -->
        <BudgetCommanderSummary
          :budget="budget"
          :average_cpc="average_cpc"
          :required_cpc="required_cpc"
          :kpi_value="cpa"
          :kpi-option="kpiOption"
          :kpi-target="kpiTarget"
          :account="account"
          :rollover_spend="budget_commander_rollover_spend"
          v-if="isSuccess"
        />
        <!-- Summary Graph -->
        <BudgetCommanderChartWrapper
          :budget="budget"
          :kpi-target="kpiTarget"
          :account="account"
          :rollover_spend="budget_commander_rollover_spend"
          :excess_budget="excessBudget"
        />

        <div class="col w-full lg:w-1/2 xl:w-1/3">
          <div class="card BudgetCommander__settings">
            <!-- Budget Commander Settings -->
            <BudgetCommanderSettings
              :account="account"
              @budgetUpdated="updateBudget"
              @kpiOptionUpdated="updateKPIOption"
              @kpiTargetUpdated="updateKPITarget"
            />

            <!-- Budget Commander Actions -->
            <BudgetCommanderActions
              :account="account"
            />
          </div>
        </div>

        <section class="col w-full">
          <EmptyGroup 
            class="BudgetCommander__group" 
            v-if="groupState === 'empty'" 
            @updateGroupState="updateGroupState"
            @showVideoModal="showVideoModal"
          />

          <CreateGroup
            v-else-if="groupState === 'create' | groupState === 'edit'"
            @updateGroupState="updateGroupState"
            @updateBudgetGroup="updateBudgetGroup"
            :initialValue="selectedGroup"
            :groupState="groupState"
          />

          <DeleteGroup
            v-else-if="groupState === 'delete'"
            @backToEditList="backToEditList"
            @deleteSelectedGroup="deleteSelectedGroup"
            :selectedGroup="selectedGroup"
          />

          <ListGroup
            v-else-if="groupState === 'list'"
            @updateGroupState="updateGroupState"
            @setSelectedGroup="setSelectedGroup"
            @goDeleteSelectedGroup="goDeleteSelectedGroup"
            :budgetGroupList = "budgetGroupList"
            :updateEnabled = "updateEnabled"
          />
          
        </section>
      </div>
      <sidebar-modals-container/>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import BudgetCommanderSettings from "./BudgetCommanderSettings.vue"
import BudgetCommanderSummary from "./BudgetCommanderSummary.vue"
import BudgetCommanderActions from "./BudgetCommanderActions.vue"
import BudgetCommanderChartWrapper from "./BudgetCommanderChartWrapper.vue"
import EmptyGroup from "./budgetGroups/EmptyGroup.vue"
import CreateGroup from "./budgetGroups/CreateGroup.vue"
import DeleteGroup from "./budgetGroups/DeleteGroup.vue"
import ListGroup from "./budgetGroups/ListGroup.vue"
import { 
  mapState, 
  mapGetters, 
  mapActions 
} from "vuex";

export default {
  name: "budget-commander",
  data () {
    return {
      budget: 0,
      kpiOption: 'CPA',
      kpiTarget: 0,
      account: this.$route.params.id,
      average_cpc: 0,
      required_cpc: 0,
      cpa: 0,
      groupState : '',
      updateEnabled: false,
      selectedGroup: {}
    }
  },
  components: {
    BudgetCommanderSummary,
    BudgetCommanderSettings,
    BudgetCommanderActions,
    BudgetCommanderChartWrapper,
    EmptyGroup,
    CreateGroup,
    DeleteGroup,
    ListGroup
  },
  created() {
    this.getBudgetGroupData()
  },
  async mounted () {
    // Get Budget
    await axios.get('/api/account/' + this.account + '/budget').then(response => {
      this.results = response.data;
      this.budget = Number(this.results.data.budget);
      this.setBudgetCommanderBudget(this.budget)
    }, error => {
      console.error(error)
      if(error.response && (error.response.status === 401 || error.response.status === 404)){
        window.location.replace('/404');
      }
    });

    // Get KPI type and target
    await axios.get('/api/account/' + this.account + '/kpi').then(response => {
      this.results = response.data;
      this.kpiOption = this.results.data.kpi_name.toUpperCase()
      this.kpiTarget = Number(this.results.data.kpi_value);
      this.setBudgetCommanderKpiTarget(this.kpiTarget)
      this.setBudgetCommanderKpiOption(this.kpiOption)
    }, error => {
      console.error(error)
      if(error.response && error.response.status >= 400){
        window.location.replace('/404');
      }
    })

    this.getBudgetCommanderData({
      account_id: this.account
    });

  },
  methods: {
    ...mapActions([
      'createAlert', 
      "getBudgetCommanderData",
      "getBudgetCommanderGroupData",
      'setBudgetCommanderBudget',
      'setBudgetCommanderKpiOption',
      'setBudgetCommanderKpiTarget',
      'deleteBudgetCommanderGroupData',
      'updateBudgetCommanderGroupData',
    ]),

    // Update Budget
    updateBudget (budget) {
      this.budget = parseFloat(budget);
    },
    // Update KPI type
    updateKPIOption (option) {
      this.kpiOption = option
    },
    // Update KPI Target
    updateKPITarget (target) {
      this.kpiTarget = parseFloat(target)
    },
    //Update Group State
    updateGroupState(state) {
      this.groupState = state
      if (state === 'create') {
        this.selectedGroup = {}
      }      
    },
    setSelectedGroup(groupId) {
      this.updateGroupState('edit')
      this.selectedGroup = this.budget_commander_group_by_id(groupId)
    },
    goDeleteSelectedGroup(groupId) {
      this.updateGroupState('delete')
      this.selectedGroup = this.budget_commander_group_by_id(groupId)
      if(this.budgetGroupList.length === 0) {
        this.groupState = 'empty'
      }
    },
    backToEditList() {
      this.updateEnabled = true
      this.updateGroupState('list')
    },
    getBudgetGroupData(){
      this.getBudgetCommanderGroupData({
        account_id: this.account
      }).then(() => {
        this.groupState = this.$store.getters.budget_commander_group_list.length === 0 ? 'empty' : 'list'
      });
    },
    async deleteSelectedGroup(groupId) {
       await this.deleteBudgetCommanderGroupData({
        account_id: this.account,
        budget_group_id: groupId
      })
      this.groupState = this.budgetGroupList.length === 0 ? 'empty' : 'list'
    },
    async updateBudgetGroup(budgetGroup) {
      await this.updateBudgetCommanderGroupData(budgetGroup)
      this.groupState = 'list'
    },
    showVideoModal(){
      
      this.$sidebarModal.show({
        template: `
          <youtube video-id="Wy9q22isx3U" :height="600"></youtube>
        `,
      }, {
        height: 'auto',
      }, {
        width: '50%',
      }, {
        'before-close': (event) => { console.log('this will be called before the modal closes'); }
      })
    }
  },
  computed: {
    ...mapState({
      budgetGroupList: state => state.budget_commander.budget_group_list,
      selected_account: state => state.selected_account,
    }),
    ...mapGetters(["budget_commander_response_data", 
    "budget_commander_data_state", 
    "budget_commander_rollover_spend",
    "budget_commander_excess_budget",
    "budget_commander_group_list",
    "budget_commander_group_by_id"
    ]),
    isSuccess(){
      return this.budget_commander_data_state.isSuccess
    },
    isError(){
      return this.budget_commander_data_state.isError
    },
    excessBudget(){
      return this.budget_commander_excess_budget
    }
  },
  watch: {
    isError: function (old_value, new_value) {
      if(this.isError){
        const alert_payload = {headline:'Oh dear! There was an error fetching your data.', message: "Please refresh to try again. If the problem persists we're on hand at support@adevolver.com.",dismissSecs:60}
        this.createAlert(alert_payload)
      }
    }
  },
}
</script>

<style scoped>

</style>
