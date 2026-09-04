import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import { JSDOM } from 'jsdom';

const runtime = await readFile(new URL('../../src/Resources/public/fm-tinymce.js', import.meta.url), 'utf8');

function boot(markup) {
  const dom = new JSDOM(markup, { runScripts: 'dangerously' });
  const initializations = [];
  dom.window.tinymce = { init: (configuration) => initializations.push(configuration) };
  dom.window.eval(runtime);

  return { dom, initializations };
}

test('initializes two fields while appending one TinyMCE script', async () => {
  const { dom, initializations } = boot(`
    <textarea id="first" data-fm-tinymce-options='{"license_key":"gpl"}' data-fm-tinymce-script="/tinymce.js"></textarea>
    <textarea id="second" data-fm-tinymce-options='{"license_key":"gpl"}' data-fm-tinymce-script="/tinymce.js"></textarea>
  `);

  dom.window.FMTinyMCE.loadScript = async () => {};
  await new Promise((resolve) => setTimeout(resolve, 0));

  assert.equal(initializations.length, 2);
  assert.equal(initializations[0].target.id, 'first');
  assert.equal(initializations[1].target.id, 'second');
});

test('ignores fields without TinyMCE data and malformed configuration', async () => {
  const { dom, initializations } = boot(`
    <textarea id="disabled"></textarea>
    <textarea id="invalid" data-fm-tinymce-options="not-json" data-fm-tinymce-script="/tinymce.js"></textarea>
  `);
  dom.window.FMTinyMCE.loadScript = async () => {};
  const errors = [];
  dom.window.console.error = (message) => errors.push(message);

  await dom.window.FMTinyMCE.initialize();

  assert.equal(initializations.length, 0);
  assert.equal(errors.length, 1);
});

test('loads each TinyMCE script URL once', async () => {
  const { dom } = boot('');
  const firstLoad = dom.window.FMTinyMCE.loadScript('/tinymce.js');
  const script = dom.window.document.querySelector('script[data-fm-tinymce-script="/tinymce.js"]');

  assert.ok(script);
  assert.equal(dom.window.document.querySelectorAll('script[data-fm-tinymce-script]').length, 1);
  script.dispatchEvent(new dom.window.Event('load'));
  await firstLoad;
  await dom.window.FMTinyMCE.loadScript('/tinymce.js');

  assert.equal(dom.window.document.querySelectorAll('script[data-fm-tinymce-script]').length, 1);
});

test('synchronizes an inline editor into its hidden textarea', async () => {
  const { dom, initializations } = boot(`
    <form><textarea id="body" hidden>&lt;p&gt;initial&lt;/p&gt;</textarea>
    <div id="body_editor" data-fm-tinymce-inline-target="body" data-fm-tinymce-options='{"inline":true}' data-fm-tinymce-script="/tinymce.js">&lt;p&gt;initial&lt;/p&gt;</div></form>
  `);
  dom.window.FMTinyMCE.loadScript = async () => {};
  await new Promise((resolve) => setTimeout(resolve, 0));
  const handlers = {};
  const editor = {
    on: (events, handler) => { handlers[events] = handler; },
    getContent: () => '<p>updated</p>',
    setContent: (content) => { editor.content = content; },
  };
  initializations[0].setup(editor);

  handlers.init();
  handlers['change input']();

  assert.equal(editor.content, '<p>initial</p>');
  assert.equal(dom.window.document.getElementById('body').value, '<p>updated</p>');

  dom.window.document.querySelector('form').dispatchEvent(new dom.window.Event('submit'));

  assert.equal(dom.window.document.getElementById('body').value, '<p>updated</p>');
});

test('adapts the controlled FMElfinder URL into a TinyMCE callback', async () => {
  const { dom, initializations } = boot('<textarea id="body" data-fm-tinymce-options=\'{"fm_elfinder_url":"/elfinder"}\' data-fm-tinymce-script="/tinymce.js"></textarea>');
  dom.window.FMTinyMCE.loadScript = async () => {};
  dom.window.open = () => null;
  await new Promise((resolve) => setTimeout(resolve, 0));
  let selectedUrl = null;

  initializations[0].file_picker_callback((url) => { selectedUrl = url; });
  dom.window.FMTinyMCEFilePickerCallback('/uploads/image.png');

  assert.equal(selectedUrl, '/uploads/image.png');
  assert.equal(initializations[0].fm_elfinder_url, undefined);
});
