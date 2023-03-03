import * as mutationTypes from './mutation-type'
import * as types from './types'

import axios from 'axios'
import doAsync from '@/store/api/async-util'

export default {

  [types.TOGGLE_SYNC]: ({ commit,dispatch }, {
    account_id, 
    user_id,
    is_synced
  }) => {
    axios
    .post("/api/account/" + account_id + "/toggle_is_synced")
    .then(() => {
      dispatch(types.GET_NUMBER_OF_SYNCED_ACCOUNTS, user_id);
      commit(types.TOGGLE_SYNC_SUCCESS, {id: account_id, is_synced})
    });
  },
  [types.UPDATE_ACCOUNT_BUDGET_SETTING]: ({ commit }, {
    account_id, 
    budget, 
    kpi_name, 
    kpi_value,
  }) => {
    axios.patch('/api/account/' + account_id + '/budget', { budget }).then(res => {
      commit(types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS, { budget, id: account_id})
    })

    const requestKpi = {
      kpi_name,
      kpi_value,
      id: account_id
    }

    axios.patch('/api/account/' + account_id + '/kpi', requestKpi).then(res => {
      commit(types.UPDATE_ACCOUNT_BUDGET_SETTING_SUCCESS, requestKpi)
    })
  },

  [types.GET_ACCOUNTS]: ({ commit }, userId) => {
    return new Promise((resolve, reject) => {
      const getTitleOnly = (response) => {
        resolve(response.data)
        return response.data
      }

      const url = '/api/user/' + userId + '/accounts'

      return doAsync(
        commit, {
          url, mutationTypes: mutationTypes.GET_ACCOUNTS, callback: getTitleOnly
        }
      )
    })
  },

  [types.GET_CURRENCY]: ({ commit }, accountId) => {
    axios.get('/api/account/' + accountId + '/currency').then(res => {
      commit(types.GET_CURRENCY_SUCCESS, res.data)
    })
  },

  [types.GET_NUMBER_OF_SYNCED_ACCOUNTS]: ({ commit }, user_id) => {
    axios.get("/api/user/" + user_id + "/number_of_synced_accounts")
    .then((response) => {
      commit(types.GET_NUMBER_OF_SYNCED_ACCOUNTS_SUCCESS, response.data)
    })
  }
}