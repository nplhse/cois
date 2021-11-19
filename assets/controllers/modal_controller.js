import { Controller } from "stimulus";
import { Modal } from "bootstrap";
import $ from "jquery";

export default class extends Controller {
    static targets = ["modal", "modalBody"];
    static values = {
        formUrl: String,
        targetUrl: String,
    };

    async openModal(event) {
        const modal = new Modal(this.modalTarget);

        const response = await fetch(this.formUrlValue);

        this.modalBodyTarget.innerHTML = await response.text();

        modal.show();
    }

    async submitForm(event) {
        event.preventDefault();
        const $form = $(this.modalBodyTarget).find("form");
        this.modalBodyTarget.innerHTML = await $.ajax({
            url: this.formUrlValue,
            method: $form.prop("method"),
            data: $form.serialize(),
        });

        window.location = this.targetUrlValue;
    }
}
