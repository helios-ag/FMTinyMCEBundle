import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: 'tests/Browser',
  use: { browserName: 'chromium', headless: true },
});
