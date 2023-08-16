/* eslint-disable no-undef */
import { startStimulusApp } from '@symfony/stimulus-bridge';

import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';

// eslint-disable-next-line import/prefer-default-export
export const app = startStimulusApp(
  require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!../controllers',
    true,
    /\.(j|t)sx?$/,
  ),
);

app.register('live', LiveController);
