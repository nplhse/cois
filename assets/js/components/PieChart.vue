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
      type: Array,
    },
  },
  data() {
    return {
      fields: ['gender', 'value'],
    };
  },
  computed: {
    columns() {
      let columns = [];

      this.data.forEach(function(item){
        columns[item.gender] = item.value;
        console.log(columns);
      });

      return columns;
    }
  },
  mounted() {
    var chart = c3.generate({
      bindto: '#chart',
      data: {
        // iris data from R
        url: '/vuestats/api/gender.json',
        mimeType: 'json',
        type: 'pie',
      }
    });
  }
}
</script>

<style lang="sass" scoped>

</style>
