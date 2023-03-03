<template>
  <div data-canny />
</template>
<script>
  import axios from "axios"

  export default {
    name: "Canny",
    async mounted () {
      (function(w,d,i,s){function l(){if(!d.getElementById(i)){var f=d.getElementsByTagName(s)[0],e=d.createElement(s);e.type="text/javascript",e.async=!0,e.src="https://canny.io/sdk.js",f.parentNode.insertBefore(e,f)}}if("function"!=typeof w.Canny){var c=function(){c.q.push(arguments)};c.q=[],w.Canny=c,"complete"===d.readyState?l():w.attachEvent?w.attachEvent("onload",l):w.addEventListener("load",l,!1)}})(window,document,"canny-jssdk","script");
      await axios.get('/api/user/' + localStorage.getItem("user_id") + '/ssoToken').then(response => {
        Canny('render', {
          boardToken: response.data.boardToken,
          basePath: null, // See step 2
          ssoToken: response.data.ssoToken, // See step 3
        });
      }, error => {
        console.error(error)
      });

      
    }
  }
</script>