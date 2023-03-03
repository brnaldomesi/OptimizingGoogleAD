import axios from 'axios'
import mutations from '../modules/accounts/mutations'

const doAsync = (commit, { url, mutationTypes, callback }) => {
  commit(mutationTypes.BASE, { type: mutationTypes.PENDING, value: true })

  return axios(url, {})
    .then(response => {
      const data = callback ? callback(response) : response
      commit(mutationTypes.BASE, { type: mutationTypes.SUCCESS, data, statusCode: response.status })
      commit(mutationTypes.BASE, { type: mutationTypes.PENDING, value: false})
    })
    .catch(error => {
      //The user should never 404 or 401
      //Unless they tamper with cookies, the url or their session expires
      //Re-direct to login if so
      if(error.response && (error.response.status===404 || error.response.status===401)){
        window.location.replace('/login');
      }
      commit(mutationTypes.BASE, { type: mutationTypes.PENDING, value: false })
      commit(mutationTypes.BASE, { type: mutationTypes.FAILURE, statusCode: error.response.status })
    })
}
export default doAsync
