/* eslint-disable no-unused-vars */
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

        const svg = d3
            .select(this.targetValue)
            .append("svg")
            .append("g")
            .attr("transform", `translate(100, 100)`);

        let pie = d3
            .pie()
            .value((d) => d.count)
            .sort(null)
            .padAngle(0.025)(data);

        let arcMkr = d3.arc().innerRadius(50).outerRadius(100);

        let scC = d3
            .scaleOrdinal()
            .domain(data)
            .range([
                d3.schemeTableau10[2],
                d3.schemeTableau10[5],
                d3.schemeTableau10[4],
            ]);

        svg.selectAll("path")
            .data(pie)
            .enter()
            .append("path")
            .attr("d", arcMkr)
            .attr("fill", (d) => scC(d.index))
            .attr("stoke", "black");

        svg.selectAll("text")
            .data(pie)
            .enter()
            .append("text")
            .text((d) => d.data.label)
            .attr("x", (d) => arcMkr.innerRadius(50).centroid(d)[0])
            .attr("y", (d) => arcMkr.innerRadius(50).centroid(d)[1])
            .attr("font-family", "sans-serif")
            .attr("font-size", 14)
            .attr("text-anchor", "middle");

        let tr = d3
            .select(".objecttable tbody")
            .selectAll("tr")
            .data(data)
            .enter()
            .append("tr");

        let td = tr
            .selectAll("td")
            .data((d, i) => {
                return Object.values(d);
            })
            .enter()
            .append("td")
            .text((d) => {
                return d;
            });
    }
}
