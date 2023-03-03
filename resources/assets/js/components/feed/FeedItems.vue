<template>

     <div>
        <feed-item
        v-for="card in cards"
        v-bind:key="card.id"
        v-bind:card="card"
        ></feed-item>
    </div>

</template>

<script>

import FeedItem from './FeedItem.vue'

export default {

    components:{
        FeedItem
    },

    name: 'feed-items',

    computed: {

    },

     props:["cards"],

     data(){
        return {
            
        }
    },
    mounted(){
        
       
    },

}

</script>