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
