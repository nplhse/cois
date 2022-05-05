/* eslint-disable no-unused-vars */
import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["source", "filterable"];

    filter(event) {
        const lowerCaseFilterTerm = this.sourceTarget.value.toLowerCase();

        this.filterableTargets.forEach((el, i) => {
            const filterableKey = el
                .getAttribute("data-filter-key")
                .toLowerCase();

            el.classList.toggle(
                "filter--notFound",
                !filterableKey.includes(lowerCaseFilterTerm)
            );
        });
    }
}
