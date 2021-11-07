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

        let pxX = 700;
        let pxY = 380;

        let makeScale = function (accessor, range, surplus = true) {
            if (surplus) {
                return d3
                    .scaleLinear()
                    .domain([0, d3.max(data, accessor) * 1.1])
                    .range(range)
                    .nice();
            }

            return d3
                .scaleLinear()
                .domain([0, d3.max(data, accessor)])
                .range(range)
                .nice();
        };

        let scX = makeScale((d) => d["age"], [0, pxX], false);
        let scY1 = makeScale((d) => d["male"], [pxY, 0]);
        let scY2 = makeScale((d) => d["male"], [pxY, 0]);

        let drawData = function (g, accessor, curve) {
            g.selectAll("circle")
                .data(data)
                .enter()
                .append("circle")
                .attr("r", 2)
                .attr("cx", (d) => scX(d["age"]))
                .attr("cy", accessor);

            var lnMkr = d3
                .line()
                .curve(curve)
                .x((d) => scX(d["age"]))
                .y(accessor);

            g.append("path").attr("fill", "none").attr("d", lnMkr(data));
        };

        const svg = d3
            .select(this.targetValue)
            .append("svg")
            .attr("transform", `translate(40, 20)`);

        var g1 = svg.append("g");
        var g2 = svg.append("g");

        drawData(g1, (d) => scY1(d["female"]), d3.curveNatural);
        drawData(g2, (d) => scY2(d["male"]), d3.curveNatural);

        g1.selectAll("circle").attr("fill", "red");
        g1.selectAll("path").attr("stroke", "lightsalmon");

        g2.selectAll("circle").attr("fill", "blue");
        g2.selectAll("path").attr("stroke", "lightblue");

        let axMkr = d3.axisLeft(scY1);
        axMkr(svg.append("g"));

        axMkr = d3.axisRight(scY2);
        axMkr(svg.append("g"));

        svg.append("g")
            .attr("transform", "translate(" + pxX + ",0)")
            .call(axMkr);

        svg.append("g")
            .call(d3.axisTop(scX))
            .attr("transform", "translate(0," + pxY + ")");

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
