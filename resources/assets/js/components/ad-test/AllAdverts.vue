

<template>

  <div>

    <div class="AdTest w-full">

      <div id="adverts" class="col w-full" >

        <advert v-for="(advert,key) in all_ads" :key='key'

          :winning='false'
          :losing='false'
          :ad_data='advert'
          :conversion_rate_change_percentage='null'
          :ctr_change_percentage='null'
          :conversion_rate_significance='null'
          :ctr_significance='null'

        />

      </div>
    </div>
  </div>
  
</template>

<script>
/*Show all adverts below the ad creator
 - include responsive text ads
 - include paused
 - order by winner/loser, then clicks, then enabled
*/
import Advert from "./Advert";

export default {
  name: "AllAdverts",
    props: {
      ad_count:{
        required: true,
        type: [Number,String]
      },
      all_ads:{
        required: true,
        type: [Object]
      },
    },
  data () {
    return {

    }
  },
  components: {
    Advert
  }
}
</script>

<style>
#adverts .col{
  float:left;
  width:49%;
  margin-left: 0;
  margin-right: 0;
  padding-left: 0;
  padding-right: 0;
}

#adverts .col:nth-child(1) {
    margin-right: 2% !important;
}
</style>