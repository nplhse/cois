<template>
  <div>
    <div id="chart"></div>

    <b-table striped hover :items="this.data"></b-table>
  </div>
</template>

<script>
import * as c3 from "c3";

export default {
  name: "PieChart.vue",
  props: {
    data: {
      required: true,
      type: Object,
    },
    width: {
      default: 250,
      type: Number,
    },
    height: {
      default: 250,
      type: Number,
    }
  },
  data() {
    return {
      fields: ['gender', 'value'],
      padding: 50,
    };
  },
  computed: {
    viewBox() {
      return `0 0 ${this.width} ${this.height}`;
    },
    columns() {
      let columns = [];

      columns =  Object.keys(this.data).map((key) => {
        return columns[key]
      })

      return columns;
    }
  },
  mounted() {
    var chart = c3.generate({
      bindto: '#chart',
      data: {
        // iris data from R
        columns: this.data,
        type : 'pie',
      }
    });
  }
}
</script>

<style lang="sass" scoped>

</style>
