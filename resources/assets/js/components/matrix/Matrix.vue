<template>
  <div class="Matrix w-full">
    <main>
      <ejs-treemap 
        id="treemap"
        :dataSource='dataSource' 
        :enableDrillDown= 'enableDrillDown' 
        :weightValuePath='weightValuePath' 
        :levels='levels'
        :tooltipSettings='tooltipSettings'
        :background='background'
        :rangeColorValuePath='rangeColorValuePath'
        :leafItemSettings='leafItemSettings'
        :legendSettings='legendSettings'
      >
      </ejs-treemap>
    </main>
  </div>  
</template>
<script>
import { theme } from '../../../../../tailwind.config.js'
import { TreeMapPlugin, TreeMapTooltip, TreeMapLegend  } from '@syncfusion/ej2-vue-treemap'
import DrillDown from './drilldown-sample'
import Vue from 'vue'

Vue.use(TreeMapPlugin)

const {extend: {colors: {treemapColor}}} = theme

export default {
  name: "Matrix",
  data:function() {
    return {
      //palette: ["#f44336", "#29b6f6", "#ab47bc", "#ffc107", "#5c6bc0", "#009688"],
      enableDrillDown: true,
      useGroupingSeparator: true,
      dataSource: DrillDown,
      weightValuePath: 'EmployeesCount',
      rangeColorValuePath:'EmployeesCount',
      tooltipSettings: {
        visible: true,
        format: 'Count : ${EmployeesCount}'
      },
      background: treemapColor.background,
      levels: [
        { groupPath: 'Country', border: { color: 'black', width: 0.5 } },
        { groupPath: 'JobDescription', border: { color: 'black', width: 0.5 } },
        { groupPath: 'JobGroup', border: { color: 'black', width: 0.5 } },
      ],
      legendSettings: {
        visible: true,
        //mode:'Interactive',
        position:'Bottom',
        alignment:'Far',
        //border:{color:'black',width:2}
      },
      leafItemSettings: {
        labelPath: 'JobGroup',
        colorMapping:[{
          from:0,
          to:30,
          color:'#f44336'
        },
        {
            from:31,
            to:50,
            color:'#29b6f6'
        },
        {
            from:51,
            to:70,
            color:'#ab47bc'
        },
        {
            from:71,
            to:90,
            color:'#ffc107'
        },
        {
            from:91,
            to:120,
            color:'#5c6bc0'
        },
        {
            from:121,
            to:190,
            color:'#009688'
        }]
      }
    }
  },
  provide:{
    treemap:[TreeMapTooltip, TreeMapLegend],
  }
}
</script>
<style scoped>

</style>