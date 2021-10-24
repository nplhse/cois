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

        const margin = { top: 10, right: 30, bottom: 50, left: 40 };
        const width = 700 - margin.left - margin.right;
        const height = 400 - margin.top - margin.bottom;

        const svg = d3
            .select(this.targetValue)
            .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", `translate(${margin.left},${margin.top})`);

        const x = d3.scaleBand()
            .domain(data.map((d) => d.day))
            .range([0, width])
            .padding(0.2);

        svg.append("g")
            .attr("transform", `translate(0, ${height})`)
            .call(d3.axisBottom(x))
            .selectAll("text")
            .attr("transform", "translate(-10,0)rotate(-45)")
            .style("text-anchor", "end");

        const y = d3.scaleLinear()
            .domain([0, d3.max(data, d => d.count * 1.1)])
            .range([height, 0]);

        svg.append("g").call(d3.axisLeft(y));

        svg.selectAll("mybar")
            .data(data)
            .join("rect")
            .attr("x", (d) => x(d.day))
            .attr("y", (d) => y(d.count))
            .attr("width", x.bandwidth())
            .attr("height", (d) => height - y(d.count))
            .attr("fill", "#69b3a2");
    }
}
