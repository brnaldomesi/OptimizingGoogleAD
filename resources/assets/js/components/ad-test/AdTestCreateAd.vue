<template>
	<div class="pr-6">
		<div class="flex justify-between">
			<div>
				<span class="text-gray-700 font-semibold text-lg">Create new Ad</span>&nbsp;(<span id='drag-instructions'>drag to this area</span>)
			</div>
			<div class="flex pr-8">
				<span class="text-3xl flex relative mr-4">T<p class="text-lg font-black custom-sub absolute">T</p></span>
				<span :class="{'text-danger':isOver}" class="m-auto font-semibold">{{input}}/{{max_characters}}</span>

			</div>
		</div>

		<div>
			<div class="border p-4 mb-6 font-sans">
				<div class="flex flex-wrap">
					<input 
						class="text-2xl text-url-title droptarget" 
						:placeholder="headline1Placeholder"  
						id="input_headline_1"
						v-on:drop="drop"
						v-on:dragover="allowDrop"
						v-model='input_headline_1'
						v-autowidth
						@focus="setInputFocus($event, 30)"

						/>
					<span class="text-2xl text-url-title">&nbsp;|&nbsp;</span>
					<input
						class="text-2xl text-url-title" :placeholder="headline2Placeholder" id="input_headline_2"
						v-on:drop="drop"
						v-on:dragover="allowDrop"
						v-model='input_headline_2'
						@focus="setInputFocus($event, 30)"

						v-autowidth="{maxWidth: '500px', minWidth: '20px', comfortZone: '10px'}"
						>
					<span class="text-2xl text-url-title">&nbsp;|&nbsp;</span>
					<input 
						class="text-2xl text-url-title" :placeholder="headline3Placeholder" id="input_headline_3"
						v-on:drop="drop"
						v-on:dragover="allowDrop"
						v-model='input_headline_3'
												@focus="setInputFocus($event, 30)"

						v-autowidth="{maxWidth: '500px', minWidth: '20px', comfortZone: '10px'}"
						>
				</div>
				<div class="flex">
					<div class="text-url-site mr-2">
						<span class="ad-border">Ad</span>
						<cite class="break-all not-italic">{{baseUrl}}
							<span v-if="input_path_1 != ''">/</span>
							<input 
							v-model='input_path_1'
							id='input_path_1'
							@focus="setInputFocus($event, 15)"
							v-autowidth >
							<span v-if="input_path_2 != ''">/</span>

							<input 
							v-model='input_path_2'
							id='input_path_2'
							@focus="setInputFocus($event, 15)"
							v-autowidth >
						</cite>&lrm;
					</div>
					<div class="flex">
						<span class="caret m-auto text-url-site"></span>
					</div>
				</div>
				<div class="flex flex-wrap">
					<input
						:placeholder="descriptionPlaceholder" 
						class='droptarget' 
						id="input_description"
						v-on:drop="drop"
						v-on:dragover="allowDrop"
						v-model="input_description"
						@focus="setInputFocus($event, 90)"

						v-autowidth>
							&nbsp;
					<input
						:placeholder="description2Placeholder" 
						class='droptarget' 
						id="input_description_2"
						v-on:drop="drop"
						v-on:dragover="allowDrop"
						v-model="input_description_2"
						@focus="setInputFocus($event, 90)"

						v-autowidth>
				</div>
			</div>
		</div>

		<div class="text-gray-700 font-semibold text-lg mb-4">Final url</div>
		<div class="flex border rounded p-2 mb-6 w-3/4">
			<img src="/assets/img/ad-testing/icon-domain.svg" class="mr-2" />
			<input type="text" class="outline-none w-full font-sans" v-model="input_url"  id="input_url">
		</div>

		<div class="flex">
			<!-- <button class="bg-gray-700 hover:bg-gray-900 text-white py-3 px-4 rounded mr-1 w-1/4"><span class="caret mr-1"></span>URL options</button> -->
			<button @click='handleCreateAdvert' class="bg-redPrimary hover:bg-redPrimaryHover text-white py-3 px-4 rounded w-3/4 cursor-pointer"  
      v-bind:class="{'button-disabled' : !isValid}" v-bind:disabled="!isValid">

       <div class="pr-1" v-if='data_state.isPending'>
        <div >
          <i class="fas fa-spinner fa-spin"></i>
        </div>
      </div>

      <div v-else>
        {{buttonText}}
      </div>
      
      </button>
		</div>
	</div>

</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import DataState from "../../helpers/DataState";

	export default {
		name: "AdTestCreateAd",
		 props: [
			'url_placeholder',
			'path_1_placeholder',
			'path_2_placeholder',
			'headline_1_placeholder',
			'headline_2_placeholder',
			'headline_3_placeholder',
			'description_placeholder',
			'description_2_placeholder',
            'adgroup_id',
            'adgroup_google_id'
		 ],
		data () {
		  return {
			base_url: '',
			input_url: '',
			input_path_1: '',
			input_path_2: '',
			input_headline_1: '',
			input_headline_2: '',
			input_headline_3: '',
			input_description: '',
			input_description_2: '',
			max_characters: 30,
			input_focus_id: 'input_headline_1',
      buttonText: 'Create Ad',
      data_state: new DataState()

		  }
		},
		methods: {
			...mapActions([
          'createAdvert',
          'createAlert'
			]),
			allowDrop:function(event) {
			// event.preventDefault();
		  },
		  handleCreateAdvert(){
        this.data_state = new DataState();
        this.data_state.pending();
			  this.createAdvert({

					value: 
						"`finalUrls`:`"+this.input_url+"`,\
						`path1`:`"+this.input_path_1+"`,\
						`path2`:`"+this.input_path_2+"`,\
						`headlinePart1`:`"+this.input_headline_1+"`,\
						`headlinePart2`:`"+this.input_headline_2+"`,\
						`headlinePart3`:`"+this.input_headline_3+"`,\
						`description`:`"+this.input_description+"`,\
						`description2`:`"+this.input_description_2+"`"
					,

					destination_google_id: this.adgroup_google_id
        }).then((response) => {   

          if (!response) {
            this.data_state.error();
            const alert_payload = {
              headline: "Something went wrong",
              message:
                "We had trouble updating this advert's status. Please check your\
                 internet connection and try again. Error: Network error.",
              dismissSecs: 5
            };
            this.createAlert(alert_payload);
            return;
          }
          this.data_state.success();
          this.buttonText = 'Advert Created!'
          setTimeout(() => {
            this.buttonText = 'Create Ad'
            this.changed = false
          }, 2000)

       }).catch((e)=>{
         const alert_payload = {headline:'Something went wrong', message: 'We had trouble creating the advert. Please check your internet connection and try again. ' + e, dismissSecs:60}
          this.createAlert(alert_payload)
          this.data_state.error();
        })
        
        
		  },
		  setInputFocus(event, limit){
			this.input_focus_id = event.target.id
			this.max_characters = limit
		  },
		  drop:function(event) {
			event.preventDefault();
			document.getElementById(event.target.id).focus()

			var data = event.dataTransfer.getData("Text");
			if(typeof data == 'undefined'){
				return
			}
			
			document.getElementById("drag-instructions").innerHTML = 'drag to this area';
			event.target.value = ''
			event.target.value=data.replace(/&quot;/g, '"')
							.replace(/&#39;/g, "'")
							.replace(/&lt;/g, '<')
							.replace(/&gt;/g, '>')
							.replace(/&amp;/g, '&');
			this[event.target.id] = event.target.value
		  },
		  getTextLength(text){
			text = text.toLowerCase()
			if(text.indexOf('{keyword:')>-1){
			  text = text.replace('{keyword:', '')
			  return text.length-1
			}

			if(text.indexOf('{=if')>-1){
			  text = text.replace(/(.*):/g, '')
			  return text.length-1
			}

			if(text.indexOf('countdown')>-1){
			  return 20
			}
			
			return text.length
		  }
		  
		},
		computed:{
			...mapGetters([]),

			baseUrl(){
				let input_url = this.url_placeholder
				this.input_url = input_url
				this.input_path_1 = this.path_1_placeholder
				this.input_path_2 = this.path_2_placeholder
				return input_url=='' ? this.url_placeholder.replace(/(\/\/[^\/]+)?\/.*/, '$1') : input_url.replace(/(\/\/[^\/]+)?\/.*/, '$1')
			},
			paths(){
				if(this.path_2_placeholder==''){
					return this.path_1_placeholder=='' ? '' : '/'+this.path_1_placeholder
				}
				return '/'+this.path_1_placeholder + '/' + this.path_2_placeholder
			},
			isValid(){
			 let lines = ["headline_1", "headline_2", "headline_3", "description", "description_2", "url"]
			 for(let l in lines){
				 let line = lines[l]
				 if(this['input_'+line]==''){
					 return false
				 }
			 }
			  return true
			},
			headline1Placeholder(){
				return String(this.headline_1_placeholder).trim() == '--' || this.headline_1_placeholder==null ? "Headline 1" : this.headline_1_placeholder
			},
			headline2Placeholder(){
				return String(this.headline_2_placeholder).trim() == '--' || this.headline_2_placeholder==null ? "Headline 2" : this.headline_2_placeholder
			},
			headline3Placeholder(){

				return String(this.headline_3_placeholder).trim() == '--' || this.headline_3_placeholder==null ? "Headline 3" : this.headline_3_placeholder
			},
			descriptionPlaceholder(){
				return String(this.description_placeholder).trim() == '--' || this.description_placeholder==null ? "Description" : this.description_placeholder
			},
			description2Placeholder(){
				return String(this.description_2_placeholder).trim() == '--' || this.description_2_placeholder==null ? "Description 2" : this.description_2_placeholder
			},
			input: function () {
			  let text = this[this.input_focus_id]
			  return this.max_characters - this.getTextLength(text);
			},
			isOver: function () {
			  return this.input < 0; 
			},
			charactersOver: function () {
			  return this.isOver ? this.input - this.max_characters : 0;
			},
			
		},
		mounted(){

		},
	}
</script>

<style scoped>
.button-disabled, .button-disabled:hover{
	background-color: #CCC;
	cursor: no-drop;
}
</style>