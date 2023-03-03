import * as types from './types'

import createMutationWithState from '@/store/api/mutation-type-with-state'

//mutation types with various states(PENDING, SUCCESS, FAILURE)
export const GET_ACCOUNTS = createMutationWithState({
  type: types.GET_ACCOUNTS,
  stateKey: 'list'
})
