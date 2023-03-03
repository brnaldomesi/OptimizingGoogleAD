
<template>

<div class="Feed">

     <main>
     
         <div class="row">

             <div class="col w-full">
                <div class="flex flex-wrap justify-between">

              <tabs>

            <ul class="flex border-b" >

              <div @click='updateActive("New")'>
                <tab-button  :class="{'font-black':isActive('New')}" name="New"  ></tab-button>
              </div>
              <div @click='updateActive("Snoozed")'>
                <tab-button  :class="{'font-black':isActive('Snoozed')}" name="Snoozed"  ></tab-button>
              </div>
              <div @click='updateActive("Archived")'>
                <tab-button  :class="{'font-black':isActive('Archived')}" name="Archived"  ></tab-button>
              </div>

             </ul>

        </tabs>

            </div>
            
          </div>       

          <div class="col w-full md:w-7/12" style='min-width:420px;'>
            <tab-content>

            </tab-content>
          </div>

      <div class="col md:w-5/12">
        
           
            <BudgetCommanderChartWrapper
          :budget="budget"
          :kpi-target="kpiTarget"
          :account="account"
          :create="true"
        />

            
          </div> 

       </div>
    
     </main>
</div>
	
</template>

<script>

import Tabs from './Tabs.vue'
import TabButton from './TabButton.vue'
import TabContent from './TabContent.vue'
import BudgetCommanderChartWrapper from "../budget-commander/BudgetCommanderChartWrapper.vue"
// import BudgetGraph from './BudgetGraph.vue'


export default {

	components:{

		Tabs,
		TabButton,
    TabContent,
    BudgetCommanderChartWrapper
        // BudgetGraph

	},

	name: 'feed',
	
	
    data(){

        return {
            cards: {},
            account: this.$route.params.id,
            active_tab: "New",
            budget: 20000,
            kpiTarget: 0
        }

    },

    methods:{
        isActive(name){
          if(this.active_tab==name)return true
          return false
        },
        updateActive(name){
          if(this.isActive(name))return
          this.active_tab=name
        }
    }

}
</script>

<style scoped>

</style>
