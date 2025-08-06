<template>
  <div class="chart-container">
    <canvas :ref="chartId"></canvas>
  </div>
</template>

<script>

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend
)

export default {
  name: 'LineChart',
  props: {
    chartId: {
      type: String,
      default: 'line-chart'
    },
    title: {
      type: String,
      default: 'Chart'
    },
    labels: {
      type: Array,
      required: true
    },
    datasets: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      chart: null
    }
  },
  mounted() {
    this.createChart()
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
  methods: {
    createChart() {
      const ctx = this.$refs[this.chartId].getContext('2d')
      
      this.chart = new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: this.labels,
          datasets: this.datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: this.title
            },
            legend: {
              position: 'top'
            },
            tooltip: {
              mode: 'index',
              intersect: false
            }
          },
          scales: {
            x: {
              display: true,
              title: {
                display: true,
                text: 'Month'
              }
            },
            y: {
              display: true,
              title: {
                display: true,
                text: 'Number of Customers'
              },
              beginAtZero: true
            }
          },
          elements: {
            line: {
              tension: 0.4
            }
          }
        }
      })
    }
  }
}
</script>

<style scoped>
.chart-container {
  height: 400px;
  width: 100%;
}
</style>
