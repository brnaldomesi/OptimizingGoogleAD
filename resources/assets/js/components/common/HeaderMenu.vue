<template>
        <header class="Header" :class="{'minimized': accountSelected}">


            <ul class="Header__breadcrumb">
                <li class="Header__breadcrumb--title">{{ this.$route.name }}</li>
                <span class='flex' v-if='this.$route.name=="Ad Testing" && Object.keys(adgroup_response_data).length>0'>
                    <li class="text-2xl">&nbsp;&nbsp;›&nbsp;&nbsp;</li>
                    <li class="text-2xl mt-1">{{campaign_name}}</li>
                    <li class="text-2xl">&nbsp;&nbsp;›&nbsp;&nbsp;</li>
                    <li class="text-2xl mt-1">{{adgroup_name}}</li>
                    <a :href=previous_page_url v-if='next_page_number>2'><li class="mt-2 cursor-pointer">&nbsp;&nbsp;&nbsp;◁&nbsp;&nbsp;&nbsp;</li></a>
                    <a :href=next_page_url v-if='next_page_number <= last_page_number'><li class="mt-2 cursor-pointer">&nbsp;&nbsp;&nbsp;▷&nbsp;&nbsp;&nbsp;</li></a>
                </span>
            </ul>

            <Notifications />

            <div 
              class="dropdown"
              @focusout="hideUserMenu"
              tabindex="0"
            >
              <div 
                class="Header__avatar flex items-center cursor-pointer" 
                @click="toggleUserMenu"
              >
                  <img :src="userImageUrl" />
                  &#9661;
              </div>
              <div 
                v-if="is_usermenu_open" 
                class="dropdown-content"
                v-focus
                
              >
                <a href="/logout" class="menu-area"> Logout</a>
              </div>
            </div>
        </header>

</template>

<script>
    import Vue from 'vue'
    import axios from "axios"
    import Notifications from './notifications/Notifications.vue'
    import isMenuArea from '../../helpers/helpers'
    import { mapGetters } from 'vuex'

    export default {
        name: "headermenu",

        props:{
            user_id:{
              type: String,
              required: true
            },
            accountSelected: {
              type: Boolean,
              required: true
            }
        },

        data: function(){
            return{
                this_page_number:'',
                last_page_number:'',
                next_page_number:'',
                previous_page_number:'',
                next_page_url:'',
                previous_page_url:'',
                adgroup_name:'',
                campaign_name:'',
                account_id:'',
                is_usermenu_open: false,
                userImageUrl: this.$userImageUrl ? this.$userImageUrl : '/assets/img/header/avatar-placeholder.png'
            }
        },

        components: {
          Notifications
        },

        directives: {
          focus: {
            inserted: function(el) {
              Vue.nextTick(() => el.focus());               // <======== changed this line
            }
          }
        },afterMounted(){

          },

        mounted () {
          
          // if (localStorage.getItem('account_id')) this.account_id = localStorage.getItem('account_id');
          // console.log(this.$route.name)
          this.getAdTestingData()
          


        },
        methods:{
            getAdTestingData(){
              if(this.$route.name!=='Ad Testing')return
              // console.log('loading data from header')
              this.ad_test_promise.then(response => {
                // console.log('data from header')

                // console.log('adgroup_response_data', this.adgroup_response_data)

                this.account_id = this.selected_account.account_id
                // console.log(this.adgroup_response.to)
                this.this_page_number = parseInt(this.adgroup_response.to)
                this.last_page_number = parseInt(this.adgroup_response.last_page)
                this.next_page_number = (Number(this.this_page_number)+Number(1))
                this.previous_page_number = (Number(this.this_page_number)-Number(1))
                this.next_page_url = this.$route.path + '?page='+this.next_page_number
                this.previous_page_url = this.$route.path + '?page='+this.previous_page_number
                this.adgroup_name = this.adgroup_response_data["name"]
                this.campaign_name = this.adgroup_response_data["campaign"]["name"]
 

              })
            },

            hideUserMenu(event) {
              event.stopPropagation()
              event.preventDefault()
              if(!isMenuArea(event)) {
                this.is_usermenu_open = false
              }
            },

            toggleUserMenu() {
              //if(this.is_usermenu_open) {
                this.is_usermenu_open = !this.is_usermenu_open
              //}
            }
        },
        watch:{
            $route(to, from) {
                this.getAdTestingData()
            },
        },
        computed:{
          ...mapGetters([
            'adgroup_response_data',
            'adgroup_response',
            'data_state',
            'ad_test_promise',
            'selected_account'
          ]),
        }
    }
</script>

<style scoped>

</style>