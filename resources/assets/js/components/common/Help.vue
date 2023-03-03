<template>
  <div class="fixed cursor-pointer Accounts__help" v-on-clickaway="away">
    <vue-context ref="menu" class="Accounts__popOver" @open="onOpen" :closeOnScroll="false">
      <li>
        <a @click.prevent="$emit('onHelpMenuClick')">
          <i class="far fa-life-ring"></i>&nbsp;&nbsp;Get help for this page
        </a>
      </li>
      <li>
        <a @click.prevent="$emit('onSuggestMenuClick')">
          <i class="far fa-lightbulb"></i>&nbsp;&nbsp;Suggest a feature
        </a>
      </li>
      <li>
        <a href="javascript:void(Tawk_API.toggle())" @click="$emit('onChatMenuClick')">
          <i class="far fa-comments"></i>&nbsp;&nbsp;Chat with us
        </a>
      </li>
    </vue-context>
    <div :key="helpClicked">
      <div :key="clicked" class="text-4xl">
        <a @click.stop.prevent="onHelpClick($event)">
          <i v-if="clicked" class="fas fa-times-circle" style="color:#303555;"></i>
          <i v-else class="fas fa-question-circle" style="color:#303555;"></i>
        </a>
      </div>
    </div>
  </div>
</template>
<script>
  import VueContext from 'vue-context'
  import 'vue-context/src/sass/vue-context.scss'
  import { mixin as clickaway } from 'vue-clickaway'

  export default {
    name: "Help",
    mixins: [ clickaway ],
    props: {
      helpClicked : {
        type : Boolean,
        required: true
      }
    },
    components: { 
      VueContext 
    },
    data() {
      return {
        clicked : this.helpClicked
      }
    },
    methods: {
      onHelpClick(event) {
        this.clicked = !this.clicked
        if(this.clicked){
          this.$refs.menu.open(event)
        } else {
          this.$refs.menu.close()
        }
        this.$emit('onHelpClick', this.clicked)
      },

      onOpen(event, data, top, left) {
        this.$refs.menu.top = top - 35
        this.$refs.menu.left = left - 10
      },
      away() {
        this.clicked = false
        this.$emit('onHelpClick', this.clicked)
      }
    },
    updated: function() {
      this.clicked = this.helpClicked
    }
  }
</script>