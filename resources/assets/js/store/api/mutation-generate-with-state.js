import Vue from 'vue'

const generateMutationsWithState = types => {
  const mutations = {}

  Object.keys(types).forEach(type => {
    mutations[types[type].BASE] = (state, payload) => {
      switch (payload.type) {
        case types[type].PENDING:
          return Vue.set(state, types[type].loadingKey, payload.value)

        case types[type].SUCCESS:
          Vue.set(state, types[type].statusCode, payload.statusCode)
          return Vue.set(state, types[type].stateKey, payload.data)

        case types[type].FAILURE:
          return Vue.set(state, types[type].statusCode , payload.statusCode)
      }
    }
  })

  return mutations
}

export default generateMutationsWithState
