import { Controller } from "stimulus";
import * as d3 from "d3";

export default class extends Controller {
    static values = {
        url: String,
    };

    connect() {
        this.render();
    }

    async render() {
        const response = await fetch(this.urlValue);
        const data = await response.json();

        let pie = d3
            .pie()
            .value((d) => d.count)
            .padAngle(0.025)(data);

        let arcMkr = d3.arc().innerRadius(50).outerRadius(100);

        let scC = d3
            .scaleOrdinal(d3.schemeTableau10)
            .domain(pie.map((d) => d.index));

        let g = d3
            .select("#graph")
            .append("g")
            .attr("transform", "translate(100, 100)");

        g.selectAll("path")
            .data(pie)
            .enter()
            .append("path")
            .attr("d", arcMkr)
            .attr("fill", (d) => scC(d.index))
            .attr("stoke", "black");

        g.selectAll("text")
            .data(pie)
            .enter()
            .append("text")
            .text((d) => d.data.label)
            .attr("x", (d) => arcMkr.innerRadius(50).centroid(d)[0])
            .attr("y", (d) => arcMkr.innerRadius(50).centroid(d)[1])
            .attr("font-family", "sans-serif")
            .attr("font-size", 14)
            .attr("text-anchor", "middle");

        var tr = d3
            .select(".objecttable tbody")
            .selectAll("tr")
            .data(data)
            .enter()
            .append("tr");

        var td = tr
            .selectAll("td")
            .data(function (d, i) {
                return Object.values(d);
            })
            .enter()
            .append("td")
            .text(function (d) {
                return d;
            });
    }
}
