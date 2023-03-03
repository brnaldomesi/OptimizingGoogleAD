import { Line, mixins } from 'vue-chartjs'
import ChartAnnotationsPlugin from 'chartjs-plugin-annotation'
Chart.plugins.register(ChartAnnotationsPlugin)
const { reactiveProp } = mixins

export default {
  extends: Line,
  props: {
    chartData: {
      type: Object,
      default: null
    },
    options: {
      type: Object,
      default: null
    }
  },
  mixins: [reactiveProp],
  mounted () {
    this.renderChart(this.chartData, this.options)
  },
  methods: {
    update() {
      this.$data._chart.update()
    }
  }
}
