const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

(async () => {
    const screenshotDir = path.join(__dirname, 'screenshots');
    if (!fs.existsSync(screenshotDir)) fs.mkdirSync(screenshotDir, { recursive: true });

    const browser = await puppeteer.launch({
        executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
        defaultViewport: { width: 1440, height: 900 },
    });

    try {
        const page = await browser.newPage();

        // Login sebagai super admin
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2', timeout: 30000 });
        await page.type('#email', 'superadmin@psikotes-hr.test');
        await page.type('#password', 'password');
        await Promise.all([
            page.click('button[type="submit"]'),
            page.waitForNavigation({ waitUntil: 'networkidle2' }).catch(() => {}),
        ]);
        await new Promise(resolve => setTimeout(resolve, 1500));
        console.log('After login URL:', page.url());

        // Buka halaman hasil tes
        await page.goto('http://127.0.0.1:8000/admin/hasil-tes', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(resolve => setTimeout(resolve, 1500));

        const finalUrl = page.url();
        console.log('Halaman Hasil Tes URL:', finalUrl);

        // Screenshot Tab A (default)
        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-sesi-default.png'),
            fullPage: true,
        });
        console.log('Screenshot 1 (Tab Sesi - default): OK');

        // Pilih sesi 1
        await page.evaluate(() => {
            const sel = document.getElementById('sesiSelect');
            if (sel) {
                sel.value = '1';
                sel.dispatchEvent(new Event('input', { bubbles: true }));
                sel.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        await new Promise(resolve => setTimeout(resolve, 800));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-sesi-selected.png'),
            fullPage: true,
        });
        console.log('Screenshot 2 (Tab Sesi - dengan peserta): OK');

        // Pindah ke Tab B (Per Peserta)
        await page.evaluate(() => {
            const buttons = [...document.querySelectorAll('button')];
            const btn = buttons.find(b => b.textContent.trim() === 'Per Peserta');
            if (btn) btn.click();
        });
        await new Promise(resolve => setTimeout(resolve, 800));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-peserta.png'),
            fullPage: true,
        });
        console.log('Screenshot 3 (Tab Peserta): OK');

        // Buka halaman detail
        await page.goto('http://127.0.0.1:8000/admin/hasil-tes/1/101', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(resolve => setTimeout(resolve, 1500));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-detail.png'),
            fullPage: true,
        });
        console.log('Screenshot 4 (Detail - Andi Pratama sesi 1): OK');

        // Detail dengan MMPI-2 sensitif
        await page.goto('http://127.0.0.1:8000/admin/hasil-tes/2/201', { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(resolve => setTimeout(resolve, 1500));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-detail-mmpi.png'),
            fullPage: true,
        });
        console.log('Screenshot 5 (Detail - Budi Santoso dengan MMPI-2): OK');

        console.log('\n✓ Semua screenshot berhasil dibuat di tests/screenshots/');
    } catch (err) {
        console.error('Error:', err.message, err.stack);
    } finally {
        await browser.close();
    }
})();