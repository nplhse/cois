import { Controller } from '@hotwired/stimulus';
import * as d3 from 'd3';

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

    const pxX = 700;
    const pxY = 380;

    // eslint-disable-next-line func-names
    const makeScale = function (accessor, range) {
      return d3
        .scaleLinear()
        .domain(d3.extent(data, accessor))
        .range(range)
        .nice();
    };

    const scX = makeScale((d) => d.time, [0, pxX]);
    const scY = makeScale((d) => d.count, [pxY, 0]);

    // eslint-disable-next-line func-names
    const drawData = function (g, accessor, curve) {
      g.selectAll('circle')
        .data(data)
        .enter()
        .append('circle')
        .attr('r', 2)
        .attr('cx', (d) => scX(d.time))
        .attr('cy', accessor);

      const lnMkr = d3
        .line()
        .curve(curve)
        .x((d) => scX(d.time))
        .y(accessor);

      g.append('path').attr('fill', 'none').attr('d', lnMkr(data));
    };

    const svg = d3
      .select(this.targetValue)
      .append('svg')
      .append('g')
      .attr('transform', 'translate(40, 10)');

    drawData(svg, (d) => scY(d.count), d3.curveNatural);

    svg.selectAll('circle').attr('fill', 'green');
    svg.selectAll('path').attr('stroke', 'black');

    const axMkr = d3.axisLeft(scY);
    axMkr(svg.append('g'));

    svg.append('g')
      .attr('transform', `translate(${pxX},0)`)
      .call(axMkr);

    svg.append('g')
      .call(d3.axisTop(scX))
      .attr('transform', `translate(0,${pxY})`);
  }
}
