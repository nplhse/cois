import { startStimulusApp } from "@symfony/stimulus-bridge";

import LiveController from "@symfony/ux-live-component";
import "@symfony/ux-live-component/styles/live.css";

export const app = startStimulusApp(
    require.context(
        "@symfony/stimulus-bridge/lazy-controller-loader!../controllers",
        true,
        /\.(j|t)sx?$/
    )
);

app.register("live", LiveController);
