<template>
  <div class="Accounts__list">
    <div v-show="searchKey === ''">
      <AccountItem
        :key="account.id"
        :account="account"
        v-for="account in this.list.slice(0, resultsPerPage)"
        :expanded="expanded"
        @updateExpandedList="updateExpandedList"
      />
    </div>
    <div v-show="searchKey !== ''">
      <AccountItem
        :key="account.id"
        :account="account"
        v-for="account in filteredList.slice(0, resultsPerPage)"
        :expanded="expanded"
        @updateExpandedList="updateExpandedList"
      />
    </div>
    <div v-if="searchKey !== '' && filteredList.length==0">
      <p class="ml-10" v-if="is_synced">No active accounts found.</p>
      <p class="ml-10" v-else>No inactive accounts found.</p>
    </div>
  </div>
</template>

<script>
import * as types from '@/store/modules/accounts/types'
import AccountItem from './AccountItem'

export default {
  name: "AccountsList",
  data() {
    return {
      expanded: []
    }
  },
  props: {
    list: {
      type: Array,
      required: true
    },
    searchKey: {
      type: String,
      required: true
    },
    perPage: {
      type: Number,
      required: true
    },
    is_synced: {
      type: Boolean,
      required: true
    }
  },
  components: {
    AccountItem
  }, 
  computed: {
    filteredList: function () {
      if(this.searchKey !== '') {
        return this.list.filter(account => {
          return account.name.toLowerCase().includes(this.searchKey.toLowerCase())
        })
      } else return []
    },
    resultsPerPage(){
      return this.perPage===0 ? 20 : this.perPage
    }
  },
  methods: {
    updateExpandedList(account_id, stats) {
      if (stats && !this.expanded.includes(account_id)) {
        this.expanded.push(account_id);
      }
      if (!stats && this.expanded.includes(account_id)) {
        this.expanded.splice(this.expanded.indexOf(account_id), 1);
      }
    }
  }
}

</script>

<style scoped>

</style>