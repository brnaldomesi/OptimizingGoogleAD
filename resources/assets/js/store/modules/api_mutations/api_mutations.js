export const namespaced = true
import axios from 'axios'
import { uuidv4 } from '@/helpers/helpers'
export default {
/*
* Update the mutations table
* A python script will then read from the table and update the api
*/
  mutations: {
   
  },
  actions: {
    createMutation({commit}, payload){
      
      payload['account_id'] = this.state.selected_account.selected_account.account_id
      payload['id'] = uuidv4()
      
      return axios.post('/api/account/' + payload.account_id + '/api_mutation',{
        payload
      })

    },
    updateAdvertStatus({dispatch}, {entity_google_id, entity_id, new_status}){
      return dispatch('createMutation', {
        type: 'advert',
        action: 'set',
        attribute: 'status',
        value: new_status,
        entity_google_id,
        entity_id
      })
    },
    updateKeywordStatus({dispatch}, {entity_google_id, entity_id, new_status}){
      return dispatch('createMutation', {
        type: 'keyword',
        action: 'set',
        attribute: 'status',
        value: new_status,
        entity_google_id,
        entity_id
      })
    },
    createAdvert({dispatch}, {value,destination_google_id}){
        console.log('creating advert')
        console.log('value', value)
        return dispatch('createMutation', {
            type: 'advert',
            action: 'add',
            value,
            destination_google_id
        })
    }

  }

}
