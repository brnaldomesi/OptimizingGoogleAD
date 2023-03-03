<template>
   <div>



        <slot></slot>


    </div>
</template>

<script>
export default {

    name: 'tabs',
    
    data(){

        return{

            tabs: [],
            account_id: this.$route.params.id,

        }

    },
    mounted(){

        this.tabs = this.$children;

        Event.$on("selected", (name) => {
            this.selectTab(name)
        })
        

    },
    methods:{

        selectTab(selectedTabName){
            this.tabs.forEach(tab_button =>{

                tab_button.isActive = (tab_button.name == selectedTabName)

            })

        }

    }


}
</script>



