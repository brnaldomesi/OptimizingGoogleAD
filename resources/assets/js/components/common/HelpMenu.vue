<template>
  <div>
    <div class="float-right mt-2 mr-2 cursor-pointer">
        <a @click="$emit('close')">
          <i class="fas fa-times-circle text-danger"></i>
        </a>
    </div>
    <p class="p-6 font-bold mb-0">How to get started with the Accounts overview</p>
    <youtube video-id="Wy9q22isx3U" :width="500" :height="350"></youtube>
    <p class="px-6 pt-6 mb-0">Budget groups are a simple but powerfull tool for Google Ads automation.</p>

    <p class="px-6 pt-6 mb-0">This feature allows you to</p>

    <p class="px-6 pt-4 mb-0">1. Segment your campaigns by spend and performance<br/>target (CPA or ROAS)</p>
    <p class="px-6 pt-4 mb-0">2. Budget groups are a simple but powerful tool for <br/>Google Ads automation.</p>
    <p class="px-6 pt-4 mb-0">3. This feature allows you to segment your campaigns <br/>by spend and performance target (CPA or ROAS)</p>

    <p class="px-6 pt-6 mb-0 font-bold">Still need help? Talk to a human</p>

    <div class="px-8 pt-6">
      <a href="javascript:void(Tawk_API.toggle())" @click="$emit('close')" class="cursor-pointer">
        <i class="far fa-comments"></i>&nbsp;&nbsp;Speak to us on chat
      </a><br/>
      <a href="mailto:support@adevolver.com" class="cursor-pointer">
        <i class="far fa-envelope"></i>&nbsp;&nbsp;or you can email us
      </a>
    </div>
    <div class="fixed" style="bottom: 20px; right: 25px;">
      <a @click="$emit('close')" class="cursor-pointer">
        Close this help window
      </a>
    </div>
  </div>
</template>
<script>
  
  export default {
    name: "HelpMenu",
    props: {
    },
    components: { 
    },
    data() {
      return {
        
      }
    },
    methods: {
      onHelpEmailClick: function() {
        console.log('Help Email clicked')
      },
      onHelpChatClick: function() {
        console.log('Help Chat Clicked')
      },
    }
  }
</script>