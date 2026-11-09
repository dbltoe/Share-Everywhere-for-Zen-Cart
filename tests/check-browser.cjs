'use strict';

const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const path = require('node:path');
const { chromium } = require('playwright');

async function main() {
    const html = execFileSync('php', [path.join(__dirname, 'render-fixture.php')], {encoding: 'utf8'});
    const plugin = path.join(__dirname, '..', 'files', 'zc_plugins', 'ShareEverywhere', 'v1.0.1');
    const browser = await chromium.launch();
    try {
        const page = await browser.newPage({viewport: {width: 1100, height: 800}});
        const remoteRequests = [];
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.route('**/*', async route => {
            const url = new URL(route.request().url());
            if (url.origin !== 'https://shop.example') {
                remoteRequests.push(url.href);
                await route.abort();
            } else if (url.pathname === '/duck') {
                await route.fulfill({contentType: 'text/html', body: html});
            } else if (url.pathname.startsWith('/zc_plugins/ShareEverywhere/v1.0.1/assets/')) {
                await route.fulfill({path: path.join(plugin, url.pathname.split('/v1.0.1/')[1])});
            } else {
                await route.fulfill({status: 404});
            }
        });
        await page.goto('https://shop.example/duck');
        const widget = page.locator('#share-everywhere');
        const toggle = widget.getByRole('button', {name: 'More ways to share'});
        const panel = widget.locator('.se-panel');
        const facebook = widget.getByRole('link', {name: 'Facebook', exact: true});
        assert.equal(await widget.evaluate(el => el.previousElementSibling.id), 'cartAdd');
        assert.equal(await widget.getAttribute('data-se-layout'), 'compact');
        assert.equal(await facebook.isVisible(), true);
        assert.equal(await panel.isVisible(), false);
        await toggle.hover();
        await panel.waitFor({state: 'visible'});
        await widget.getByRole('button', {name: 'Product QR code'}).click();
        assert.ok(await widget.locator('canvas').evaluate(canvas => canvas.width > 0 && canvas.height > 0));
        await widget.getByRole('button', {name: 'Close QR code'}).click();
        await toggle.focus();
        await page.keyboard.press('Escape');
        assert.equal(await panel.isVisible(), false);
        await page.keyboard.press('Space');
        assert.equal(await panel.isVisible(), true);
        await page.keyboard.press('Space');
        assert.equal(await panel.isVisible(), false);

        await page.setViewportSize({width: 400, height: 760});
        await widget.getByRole('button', {name: 'Share this product', exact: true}).waitFor();
        assert.equal(await widget.getAttribute('data-se-layout'), 'single');
        assert.equal(await facebook.isVisible(), false);
        await widget.getByRole('button', {name: 'Share this product', exact: true}).click();
        assert.equal(await facebook.isVisible(), true);
        assert.equal(await widget.getByRole('link', {name: 'Follow on Instagram'}).getAttribute('href'),
            'https://www.instagram.com/example/');
        assert.deepEqual(remoteRequests, []);
        assert.deepEqual(errors, []);
    } finally {
        await browser.close();
    }
    console.log('Placement, compact/mobile layouts, hover, keyboard, QR, and local loading checks passed.');
}

main().catch(error => {
    console.error(error);
    process.exitCode = 1;
});
