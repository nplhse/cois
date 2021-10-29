import { Controller } from "stimulus";

export default class extends Controller {
    static values = {
        url: String,
        target: String,
    };

    connect() {
        console.log("it works");
    }

    async switch(event) {
        const params = new URLSearchParams({
            target: this.targetValue,
        });

        await fetch(`${this.urlValue}?${params.toString()}`);
    }
}
