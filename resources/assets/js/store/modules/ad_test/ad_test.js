export const namespaced = true
import axios from 'axios'
import DataState from '../../DataState';
let data_state = new DataState()

export default {
	state:{
    adgroup_response_data:{},
    adgroup_response:{},
    data_state: data_state,
    ad_test_promise: {}
  },
  getters: {
    adgroup_response_data: state => state.adgroup_response_data,
    adgroup_response: state => state.adgroup_response,
    data_state: state => state.data_state,
    ad_test_promise: state => state.ad_test_promise,
  },
  mutations: {
    SET_ADGROUP_DATA(state, payload) {
      state.adgroup_response_data = payload['adgroup_response_data'] || {};
      state.adgroup_response = payload['adgroup_response'] || {};
      state.ad_test_promise = payload['ad_test_promise'] || {}
      state.data_state = data_state;
    },
    CLEAR_ADGROUP_DATA(state){
      state.adgroup_response_data = {};
      state.adgroup_response = {};
      state.ad_test_promise = {}
      state.data_state.idle()
    }
   
  },

  actions: {

    clearAdGroupData({commit}){
      commit('CLEAR_ADGROUP_DATA')
    },
    getAdGroupData({ commit }, payload) {
      const account_id = payload['account_id']
      const page = payload['page']

      let results = {}

      data_state.pending()
      
      let ad_test_promise = axios.get('/api/account/'+account_id+'/adtest/adgroup?page='+page)

      commit('SET_ADGROUP_DATA', {
        'adgroup_response_data' : {},
        'adgroup_response' : {},
        'ad_test_promise' : ad_test_promise
      })

      ad_test_promise.then(response => {

        if(typeof results==='undefined'){
          data_state.error()
          commit('SET_ADGROUP_DATA', {})
          return
        }

        if(response.data.total==0){
          data_state.empty()
          commit('SET_ADGROUP_DATA', {})
          return
        }

        let adgroup_response_data = response.data.data[0]=='object' ? response.data.data[0] : response.data.data[Object.keys(response.data.data)[0]]

        adgroup_response_data = parseResponsiveAdText(adgroup_response_data)

        commit('SET_ADGROUP_DATA',{
          'adgroup_response_data' : adgroup_response_data,
          'adgroup_response': response.data,
          'ad_test_promise' : ad_test_promise
        })
        
        data_state.success()

      }).catch(error => {
          data_state.error()
          console.log(error)
      });

    },
  
  }
}

function parseResponsiveAdText(adgroup_response_data){
  for(let ad in adgroup_response_data['responsive_search_ads']){

    let text = adgroup_response_data['responsive_search_ads'][ad]['responsive_search_ad_descriptions']
    adgroup_response_data['responsive_search_ads'][ad]['responsive_search_ad_descriptions'] = JSON.parse(text)
    
    text = adgroup_response_data['responsive_search_ads'][ad]['responsive_search_ad_headlines']
    adgroup_response_data['responsive_search_ads'][ad]['responsive_search_ad_headlines'] = JSON.parse(text)
   
  }
  return adgroup_response_data
}

