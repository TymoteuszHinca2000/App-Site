import domReady from '@wordpress/dom-ready';
import { initializeSeoAnalysis } from './editor/seo-analysis';

domReady(() => {
    initializeSeoAnalysis();
});
