import * as mutationType from './mutation-type'
import * as types from './types'

import generateMutationsWithState from '@/store/api/mutation-generate-with-state'

const mutations = {
  [types.GET_CURRENCY_SUCCESS]: (state, res) => {
    const currencyCode = Object.keys(res)[0]
    state.currencyCode.currency = currencyCode
    state.currency_symbol = res[currencyCode].symbol
    state.account_currency_code = currencyCode
    state.account_currency_symbol = res[currencyCode].symbol
  },

  [types.GET_NUMBER_OF_SYNCED_ACCOUNTS_SUCCESS]: (state, res) => {
    state.number_of_synced_accounts = res
  },

  [types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS]: (state, res) => {
    const foundIndex = state.list.findIndex( account => account.id === res.id )
    state.list[foundIndex] = {...state.list[foundIndex], ...res}
  },

  [types.UPDATE_ACCOUNT_ROLLOVER_SETTING_SUCCESS]: (state, res) => {
    const foundIndex = state.list.findIndex( account => account.id === res.id )
    if(foundIndex<0)return
    state.list[foundIndex].budget_commander.rollover_spend = res.rollover_spend
  },
  [types.TOGGLE_SYNC_SUCCESS]: (state, res) => {
    const foundIndex = state.list.findIndex( account => account.id === res.id )
    state.list[foundIndex] = {...state.list[foundIndex], ...res}
  },
  [types.UPDATE_ACCOUNT_PROCESSED_AT]: (state, res) => {
    //ad_performance_report_processed_at will only ever be set to true
    const foundIndex = state.list.findIndex( account => account.id === res.id )
    state.list[foundIndex].ad_performance_report_processed_at = true
  }
}

export default {...generateMutationsWithState(mutationType), ...mutations}
