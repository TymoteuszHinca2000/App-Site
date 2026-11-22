import apiFetch from '@wordpress/api-fetch';
import { select, subscribe, dispatch } from '@wordpress/data';

const pluginName = 'appandsite-rendered-blocks';
const noticeId = 'appandsite-seo-analysis';

export function initializeSeoAnalysis() {
    let initialized = false;

    const initialize = () => {
        const app = window.YoastSEO?.app;

        if (initialized || !app || !select('core/editor')) return;
        initialized = true;

        let previousInput;
        let renderedContent = null;
        let revision = 0;
        let timer;

        app.registerPlugin(pluginName, { status: 'ready' });
        // Replace at the end, so the ACF add-on cannot count the same fields twice.
        app.registerModification('content', (content) => renderedContent ?? content, pluginName, 99);

        const refresh = () => {
            const editor = select('core/editor');
            const postId = editor.getCurrentPostId();
            const content = editor.getEditedPostContent();
            const input = JSON.stringify([postId, content]);

            if (input === previousInput) return;
            previousInput = input;
            const currentRevision = ++revision;
            window.clearTimeout(timer);

            if (!postId || !content.includes('<!-- wp:acf/')) {
                renderedContent = null;
                app.pluginReloaded(pluginName);
                dispatch('core/notices').removeNotice(noticeId);
                return;
            }

            timer = window.setTimeout(async () => {
                try {
                    const result = await apiFetch({
                        path: `/appandsite/v1/seo-content/${postId}`,
                        method: 'POST',
                        data: { content },
                    });

                    if (currentRevision !== revision) return;
                    if (typeof result.content !== 'string') throw new Error('Invalid rendered content');

                    const document = new DOMParser().parseFromString(result.content, 'text/html');
                    document.querySelectorAll('script, style, svg, template').forEach((element) => element.remove());
                    renderedContent = document.body.innerHTML;
                    dispatch('core/notices').removeNotice(noticeId);
                    app.pluginReloaded(pluginName);
                } catch (error) {
                    if (currentRevision !== revision) return;
                    renderedContent = null;
                    app.pluginReloaded(pluginName);
                    dispatch('core/notices').createWarningNotice(
                        'Nie udało się odczytać bloków do analizy Yoast SEO. Odśwież edytor i sprawdź ponownie.',
                        { id: noticeId, isDismissible: true },
                    );
                    console.error('App&Site: Yoast block rendering failed.', error);
                }
            }, 1000);
        };

        subscribe(refresh, 'core/editor');
        refresh();
    };

    initialize();
    window.jQuery?.(window).on('YoastSEO:ready', initialize);
}
