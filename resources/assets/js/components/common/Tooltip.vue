<template>
  <div class="tooltip" :class="{'tooltip_underline':underline}">{{title}}
    <slot></slot>
    <span :class="position" class="tooltiptext">{{content}}</span>
  </div>
</template>

<script>
export default {
  name: 'Tooltip',
  data () {
    return {
      
    }
  },
  props: {
    title: {
      type: String
    },
    content: {
      type: String
    },
    position: {
      type: String
    },
    underline:{
      type: Boolean,
      default: true
    }
  },
}
</script>

<style scoped>

</style>
