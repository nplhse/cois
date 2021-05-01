<template>
  <div>
    <svg
        class="pie-chart"
        :viewBox="viewBox"
    >
    </svg>

    <div id="my_dataviz"></div>
  </div>
</template>

<script>
import * as d3 from "d3";

export default {
  name: "PieChart.vue",
  props: {
    data: {
      required: true,
      type: Array,
    },
    width: {
      default: 500,
      type: Number,
    },
    height: {
      height: 500,
      type: Number,
    }
  },
  data() {
    return {
      padding: 50,
    };
  },
  computed: {
    viewBox() {
      return `0 0 ${this.width} ${this.height}`;
    }
  },
  mounted() {
    var width = 450;
    var height = 450;
    var margin = 40;

    // The radius of the pieplot is half the width or half the height (smallest one). I subtract a bit of margin.
    var radius = Math.min(width, height) / 2 - margin;

    // append the svg object to the div called 'my_dataviz'
    var svg = d3
        .select('#my_dataviz')
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .append('g')
        .attr(
            'transform',
            'translate(' + width / 2 + ',' + height / 2 + ')'
        );

    // set the color scale
    var color = d3
        .scaleOrdinal()
        .domain(Object.keys(this.data))
        .range(['#98abc5', '#8a89a6', '#7b6888', '#6b486b', '#a05d56']);

    // Compute the position of each group on the pie:
    var pie = d3.pie().value(function (d) {
      return d[1];
    });

    var data_ready = pie(Object.entries(this.data));

    // Build the pie chart: Basically, each part of the pie is a path that we build using the arc function.
    svg
        .selectAll('whatever')
        .data(data_ready)
        .enter()
        .append('path')
        .attr(
            'd',
            d3
                .arc()
                .innerRadius(100) // This is the size of the donut hole
                .outerRadius(radius)
        )
        .attr('fill', function (d) {
          return color(d.data[0]);
        })
        .attr('stroke', 'black')
        .style('stroke-width', '2px')
        .style('opacity', 0.7);
  }
}
</script>

<style lang="sass" scoped>

</style>
