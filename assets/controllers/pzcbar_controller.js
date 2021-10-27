import { Controller } from "stimulus";
import * as d3 from "d3";

export default class extends Controller {
    static values = {
        url: String,
        target: String,
    };

    connect() {
        this.render();
    }

    async render() {
        const response = await fetch(this.urlValue);
        const data = await response.json();

        const margin = { top: 10, right: 30, bottom: 50, left: 250 };
        const width = 700 - margin.left - margin.right;
        const height = 800 - margin.top - margin.bottom;

        const svg = d3
            .select(this.targetValue)
            .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", `translate(${margin.left},${margin.top})`);

        // Add X axis
        var x = d3
            .scaleLinear()
            .domain([0, d3.max(data, (d) => d.count * 1.1)])
            .range([0, width]);

        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x))
            .selectAll("text")
            .attr("transform", "translate(-10,0)rotate(-45)")
            .style("text-anchor", "end");

        // Y axis
        var y = d3
            .scaleBand()
            .range([0, height])
            .domain(
                data.map(function (d) {
                    return d.label;
                })
            )
            .padding(0.1);

        svg.append("g").call(d3.axisLeft(y));

        //Bars
        svg.selectAll("myRect")
            .data(data)
            .enter()
            .append("rect")
            .attr("x", x(0))
            .attr("y", function (d) {
                return y(d.PZC);
            })
            .attr("width", function (d) {
                return x(d.count);
            })
            .attr("height", y.bandwidth())
            .attr("fill", "#69b3a2");
    }
}
