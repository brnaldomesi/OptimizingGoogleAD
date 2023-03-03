<template>

        <div>

            <div  @click="selectTab()" class='cursor-pointer'>
        
              <div class="flex mr-6">

                <div :class="getClass()">
                    <span class="m-auto" :class="getIconClass(name)"></span>
                    <div class="hidden-xs m-auto">&nbsp;&nbsp;{{name}}</div>

                </div>

              </div> 

            </div>

        </div>

</template>

<script>
export default {

    name: 'tab-button',

     props:["name", "selected"],
    
    data(){

        return{

             isActive: this.selected

        }

    },
    methods:
    {
        selectTab(){
            Event.$emit("selected", this.name.toLowerCase())
        },
        getClass(){

            return this.selected ? "flex flex-wrap bg-white py-2 px-3" : "flex flex-wrap"
            
        },
        getIconClass(name){
            var icons = {
                new: "fas fa-star",
                snoozed: "fas fa-clock",
                archived: "fas fa-archive"
            }
            return icons[name.toLowerCase()]
        }
    },
    mounted(){


    },

}

</script>