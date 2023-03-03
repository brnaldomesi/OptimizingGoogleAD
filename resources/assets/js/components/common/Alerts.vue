<template>
  <div>

		<div class='alert-container' v-if='alert.headline!==""'>

			<!-- <div class='alert-sidebar'></div> -->
			  
			<div class='alert-right-container'>
				<b-alert
					:show="dismissCountDown"
					dismissible
          fade
					variant="warning"
					@dismissed="dismissCountDown=0"
					@dismiss-count-down="countDownChanged"
				>
					<h2>{{alert.headline}}</h2>
					<p v-if="alert.message!=''">{{alert.message}}</p>
					<p class='countdown'>{{ dismissCountDown }}</p>
					<b-progress
						variant="warning"
            fade  
						:max="dismissSecs"
						:value="dismissCountDown"
						height="4px"
					></b-progress>
				</b-alert>

			</div>
		</div>
  </div>
</template>

<script>
  import axios from "axios"
  import { mapGetters, mapActions } from 'vuex'

  export default {

		computed:{
      ...mapGetters([
        'alert'
      ]),
      dismissSecs(){
        return this.alert.dismissSecs
      },
      showHeadline(){
        return this.alert.headline
      }
		},
    data() {
      return {
        dismissCountDown: 0,
        showDismissibleAlert: true
      }
    },
    methods: {
      ...mapActions([
        'createAlert',
        'clearAlert'
      ]),
      countDownChanged(dismissCountDown) {
        this.dismissCountDown = dismissCountDown
        if(this.dismissCountDown==0){
          this.clearAlert()
        }
      },
      showAlert() {
        this.dismissCountDown = this.dismissSecs
      }
    },
    

    watch: {
      showHeadline () {
        this.showAlert()
      }
    },

    mounted(){
        //alert if ad blocker is switched on
        axios.get('/user/adtest/adblock_test')
        .catch( (error) => {
          if(error.message=='Network Error'){
              console.log('Network error show ad blocker alert')
              let payload = {headline:'Ad Blocker Detected', message: 'Please disable ad blocker then refresh the page.', dismissSecs:60}
              this.createAlert(payload)              
          }
        });

		}
  }
</script>

<style>

.alert-right-container{
    width: 60%;
    margin-left: 28.5%;
}
.alert-sidebar{
  width:18em;
  height:100%;
  background-color:#aaa;
  clear:both;
  position:relative;
  float:left;
}
.alert h2{
		font-size: 1.6em;
    margin-bottom: .5rem;
    font-weight: 600;
    line-height: 1.2;
}
.alert-container{
	  position:fixed;
  width:100%;
  height:81px;
  bottom:0;
	left: 0;
}
.alert {
    position: relative;
    width: 100%;
    background-color: #797e94;
    color: #fff;
    padding: .75rem 1.25rem;
    border: 1px solid transparent;
    border-radius: .25rem;
	
}

.alert .close{
    position: absolute;
   right: 7px;
    top: 0;
    font-size: 1.3em;
}

.alert .countdown{
		position: absolute;
    right: 4px;
    bottom: 0;
    font-size: 1em;
}
</style>