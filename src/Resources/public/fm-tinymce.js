(function (window, document) {
    'use strict';

    const scriptLoads = new Map();

    function loadScript(scriptUrl) {
        if (scriptLoads.has(scriptUrl)) {
            return scriptLoads.get(scriptUrl);
        }

        const promise = new Promise((resolve, reject) => {
            const existing = Array.from(document.querySelectorAll('script[data-fm-tinymce-script]'))
                .find((script) => script.dataset.fmTinymceScript === scriptUrl);
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = scriptUrl;
            script.dataset.fmTinymceScript = scriptUrl;
            script.addEventListener('load', resolve, { once: true });
            script.addEventListener('error', () => reject(new Error(`Unable to load TinyMCE: ${scriptUrl}`)), { once: true });
            document.head.append(script);
        });

        scriptLoads.set(scriptUrl, promise);
        return promise;
    }

    async function initialize(root = document) {
        const fields = root.querySelectorAll('[data-fm-tinymce-options]:not([data-fm-tinymce-initialized="true"])');

        for (const field of fields) {
            field.dataset.fmTinymceInitialized = 'true';

            let configuration;
            try {
                configuration = JSON.parse(field.dataset.fmTinymceOptions);
            } catch (error) {
                console.error('Unable to parse TinyMCE configuration.', error);
                continue;
            }

            const scriptUrl = field.dataset.fmTinymceScript;
            if (!scriptUrl) {
                console.error('TinyMCE script path is missing.');
                continue;
            }

            await namespace.loadScript(scriptUrl);
            if (!window.tinymce || typeof window.tinymce.init !== 'function') {
                console.error('TinyMCE did not become available after loading its script.');
                continue;
            }

            const hiddenTargetId = field.dataset.fmTinymceInlineTarget;
            const hiddenTarget = hiddenTargetId ? document.getElementById(hiddenTargetId) : null;
            const previousSetup = configuration.setup;
            configuration.target = field;
            delete configuration.selector;

            if (hiddenTarget) {
                configuration.setup = (editor) => {
                    if (typeof previousSetup === 'function') {
                        previousSetup(editor);
                    }

                    editor.on('change input', () => {
                        hiddenTarget.value = editor.getContent();
                    });
                };
            }

            if (configuration.fm_elfinder_url) {
                const pickerUrl = configuration.fm_elfinder_url;
                delete configuration.fm_elfinder_url;
                configuration.file_picker_callback = (callback) => {
                    window.open(pickerUrl, 'fm_elfinder', 'width=900,height=600');
                    window.FMTinyMCEFilePickerCallback = callback;
                };
            }

            window.tinymce.init(configuration);
        }
    }

    const namespace = window.FMTinyMCE = window.FMTinyMCE || {};
    namespace.loadScript = namespace.loadScript || loadScript;
    namespace.initialize = initialize;

    document.addEventListener('DOMContentLoaded', () => {
        namespace.initialize();
    });
}(window, document));
