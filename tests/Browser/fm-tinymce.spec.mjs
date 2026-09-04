import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';

const runtimePath = resolve('src/Resources/public/fm-tinymce.js');

test('initializes normal and inline fields in Chromium', async ({ page }) => {
  await page.setContent(`
    <textarea id="normal" data-fm-tinymce-options='{"license_key":"gpl"}' data-fm-tinymce-script="/tinymce.js"></textarea>
    <textarea id="inline-value" hidden></textarea>
    <div id="inline" data-fm-tinymce-inline-target="inline-value" data-fm-tinymce-options='{"inline":true}' data-fm-tinymce-script="/tinymce.js"></div>
  `);
  await page.addScriptTag({ content: 'window.calls=[]; window.tinymce={init: config => window.calls.push(config)};' });
  await page.addScriptTag({ path: runtimePath });
  await page.evaluate(async () => {
    window.FMTinyMCE.loadScript = async () => {};
    await window.FMTinyMCE.initialize();
  });

  await expect.poll(() => page.evaluate(() => window.calls.length)).toBe(2);
  await expect(page.locator('#normal')).toHaveAttribute('data-fm-tinymce-initialized', 'true');
  await expect(page.locator('#inline')).toHaveAttribute('data-fm-tinymce-initialized', 'true');
});
