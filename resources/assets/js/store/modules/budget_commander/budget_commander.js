export const namespaced = true
import Vue from 'vue'
import DataState from '../../DataState';
import axios from 'axios'
import { data } from 'jquery';

let budget_commander_data_state = new DataState()
let budget_groups_data_state = new DataState()

export default {
	state:{
    response_data:{},
    response:{},
    budget_commander_data_state: budget_commander_data_state,
    budget_groups_data_state: budget_groups_data_state,
    promise: {},
    budget_commander_group_promise: {},
    notify_via_email: 0,
    pause_campaigns: 0,
    enable_campaigns: 0,
    rollover_spend: 0,
    control_spend: 0,
    excess_budget: 0,
    emergency_stop: 0,
    budget: 0,
    kpi_option: 'CPA',
    kpi_target: 0,
    budget_group_list: [],
    campaign_list: []
  },
  getters: {
    budget_commander_response_data: state => state.response_data,
    budget_commander_response: state => state.response,
    budget_commander_data_state: state => state.budget_commander_data_state,
    budget_commander_promise: state => state.promise,
    budget_commander_pause_campaigns: state => state.pause_campaigns,
    budget_commander_enable_campaigns: state => state.enable_campaigns,
    budget_commander_rollover_spend: state => state.rollover_spend,
    budget_commander_control_spend: state => state.control_spend,
    budget_commander_excess_budget: state => state.excess_budget,
    budget_commander_emergency_stop: state => state.emergency_stop,
    budget_commander_budget: state => state.budget,
    budget_commander_kpi_option: state => state.kpi_option,
    budget_commander_kpi_target: state => state.kpi_target,
    budget_commander_campaign_list: state => state.campaign_list,
    budget_commander_group_list: state => state.budget_group_list,
    budget_commander_group_by_id: (state) => (id) => {
      return state.budget_group_list.find(group => group.budget_group_id === id)
    }
  },
  mutations: {
    SET_BUDGET_COMMANDER_DATA(state, payload) {
      state.response_data = payload['budget_commander_response_data'] || state.response_data;
      state.response = payload['budget_commander_response'] || state.response
      state.promise = payload['budget_commander_promise'] || state.promise
      state.budget = Number(payload['budget_commander_response_data'].budget) || state.budget
      state.excess_budget = Number(payload['budget_commander_response_data'].excess_budget) || state.excess_budget
      state.budget_commander_data_state = budget_commander_data_state
    },
    SET_BUDGET_GROUPS_DATA_STATE(state){
      state.budget_groups_data_state = budget_groups_data_state;
    },
    SET_BUDGET_COMMANDER_BUDGET(state, budget){
      state.budget = budget
    },
    SET_BUDGET_COMMANDER_KPI_OPTION(state, kpi_option){
      state.kpi_option = kpi_option
    },
    SET_BUDGET_COMMANDER_KPI_TARGET(state, kpi_target){
      state.kpi_target = kpi_target
    },
    SET_BUDGET_COMMANDER_EXCESS_BUDGET(state, excess_budget){
      state.excess_budget = excess_budget
    },
    SET_BUDGET_COMMANDER_SETTINGS(state, payload) {
      state.notify_via_email = payload['notify_via_email']
      state.pause_campaigns = payload['pause_campaigns']
      state.enable_campaigns = payload['enable_campaigns']
      state.rollover_spend = payload['rollover_spend']
      state.control_spend = payload['control_spend']
      state.emergency_stop = payload['emergency_stop']
    },
    SET_BUDGET_COMMANDER_GROUPS(state, payload) {
      state.budget_group_list = payload['budget_group_list'] || state.budget_group_list
      state.budget_commander_group_promise = payload['budget_commander_group_promise'] || state.budget_commander_group_promise
    },
    SET_BUDGET_COMMANDER_CAMPAIGNS(state, payload) {
      state.campaign_list = payload['campaign_list'] || state.campaign_list
    },
    DELETE_BUDGET_COMMANDER_GROUP(state, payload) {
      // let index = state.budget_group_list.findIndex(el => el.budget_group_id === payload['budget_group_id'])
      let budget_group = state.budget_group_list.filter(el => el.budget_group_id !== payload['budget_group_id'])
      // state.budget_group_list.splice(index, 1)
      Vue.set(state, 'budget_group_list', budget_group)
    },
    UPDATE_BUDGET_COMMANDER_GROUP(state, payload) {
      let index = state.budget_group_list.findIndex(el => el.budget_group_id === payload['budget_group_id'])
      if (index !== -1) {
        Vue.set(state.budget_group_list, index, payload)
      } else {
        state.budget_group_list.push(payload)
      }
    }
  },

  actions: {

    getBudgetCommanderData({ commit, rootState,state }, payload) {
      const selected_account_id = rootState.selected_account.selected_account.account_id
      if(state.response_data.id!==selected_account_id){
        budget_commander_data_state.idle()//the user has switched account so start again in the cycle
      }
      
      if(budget_commander_data_state.isSuccess)return
      
      
      const account_id = payload['account_id']
      budget_commander_data_state.pending()
      
      let budget_commander_promise = axios.get('/api/account/'+account_id+'/bcgraph')

      budget_commander_promise.then(response => {

        let budget_commander_response_data = response.data
        budget_commander_data_state.success()

        commit('SET_BUDGET_COMMANDER_DATA',{
          'budget_commander_response_data' : budget_commander_response_data,
          'budget_commander_response': response.data,
          'budget_commander_promise' : budget_commander_promise,
        })        

      }).catch(error => {
          budget_commander_data_state.error()
          console.log(error)
      });

    },
    async getBudgetCommanderGroupData({ commit, rootState, state }, payload) {
      budget_groups_data_state.pending()

      try{
        let response = await axios.get('/api/account/' + payload['account_id'] + '/budget_groups')
      
        commit('SET_BUDGET_COMMANDER_GROUPS', {
          'budget_group_list' : response.data
        })
        budget_groups_data_state.success()
        commit('SET_BUDGET_GROUPS_DATA_STATE')

      }catch(error){
        if(error.response && (error.response.status===404 || error.response.status===401)){
          window.location.replace('/login');
        }
        budget_groups_data_state.error()
        commit('SET_BUDGET_GROUPS_DATA_STATE')
        console.log(error)
      }
      
    },
    async getBudgetCommanderCampaignData({ commit, rootState, state }, payload) {
      try {
        const response = await axios.get('/api/account/' + payload['account_id'] + '/budget_commander_campaigns')
        console.log('Campaign data loaded')
        commit('SET_BUDGET_COMMANDER_CAMPAIGNS', {
          'campaign_list' : response.data
        })
      } catch (e) {
        console.log(e)
      }
    },
    async deleteBudgetCommanderGroupData({ commit, rootState, state }, payload) {
      try {
        await axios.delete('/api/account/' + payload['account_id'] + '/budget_groups', {data: payload})
        console.log('budget_group_deleted')
        commit('DELETE_BUDGET_COMMANDER_GROUP', payload)
      } catch (e) {
        console.log(e)
      }
    },
    async updateBudgetCommanderGroupData({ commit, rootState, state }, payload) {
      try {
        await axios.post('/api/account/' + payload['account_id'] + '/budget_groups', payload)
        window.location.reload();
        commit('UPDATE_BUDGET_COMMANDER_GROUP', payload)
      } catch (e) {
        console.log(e)
      }
    },
    setBudgetCommanderSettings({ commit }, payload){

      commit('SET_BUDGET_COMMANDER_SETTINGS', payload)

    },
    setBudgetCommanderBudget({commit}, value){
      commit('SET_BUDGET_COMMANDER_BUDGET', value)
    },
    setBudgetCommanderKpiOption({commit}, value){
      commit('SET_BUDGET_COMMANDER_KPI_OPTION', value)
    },
    setBudgetCommanderKpiTarget({commit}, value){
      commit('SET_BUDGET_COMMANDER_KPI_TARGET', value)
    },
    setBudgetCommanderExcessBudget({commit}, value){
      commit('SET_BUDGET_COMMANDER_EXCESS_BUDGET', value)
    },
  
  }
}
