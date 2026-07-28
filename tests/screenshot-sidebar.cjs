const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

(async () => {
    const screenshotDir = path.join(__dirname, 'screenshots/sidebar');
    if (!fs.existsSync(screenshotDir)) fs.mkdirSync(screenshotDir, { recursive: true });

    const browser = await puppeteer.launch({
        executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
        defaultViewport: { width: 1600, height: 900 },
    });

    try {
        const page = await browser.newPage();
        page.setDefaultNavigationTimeout(30000);

        // Login
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
        await page.type('#email', 'superadmin@psikotes-hr.test');
        await page.type('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForNavigation({ waitUntil: 'networkidle2' });
        console.log('Login sukses, URL:', page.url());

        // Navigasi langsung ke halaman Hasil Tes
        await page.goto('http://127.0.0.1:8000/admin/hasil-tes', { waitUntil: 'networkidle2', timeout: 60000 });
        console.log('Halaman Hasil Tes URL:', page.url());

        // Tunggu konten muat
        await new Promise(resolve => setTimeout(resolve, 1500));

        // Screenshot tab Sesi
        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-sesi.png'),
            fullPage: true,
        });
        console.log('Screenshot Sidebar (Tab Sesi): OK');

        // Pilih sesi di dropdown
        await page.select('#sesiSelect', '1');
        await new Promise(resolve => setTimeout(resolve, 800));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-sesi-dipilih.png'),
            fullPage: true,
        });
        console.log('Screenshot Sidebar (Sesi terpilih): OK');

        // Switch ke Tab Peserta
        await page.evaluate(() => {
            const buttons = document.querySelectorAll('button');
            const btn = Array.from(buttons).find(b => b.textContent.includes('Per Peserta'));
            if (btn) btn.click();
        });
        await new Promise(resolve => setTimeout(resolve, 800));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-tab-peserta-sidebar.png'),
            fullPage: true,
        });
        console.log('Screenshot Sidebar (Tab Peserta): OK');

        // Detail via link Lihat Detail
        await page.goto('http://127.0.0.1:8000/admin/hasil-tes/1/101', { waitUntil: 'networkidle2' });
        await new Promise(resolve => setTimeout(resolve, 1500));

        await page.screenshot({
            path: path.join(screenshotDir, 'hasil-tes-detail-sidebar.png'),
            fullPage: true,
        });
        console.log('Screenshot Sidebar (Detail): OK');

        console.log('\n✓ Semua screenshot sidebar berhasil disimpan.');
    } catch (err) {
        console.error('Error:', err.message);
        console.error(err.stack);
    } finally {
        await browser.close();
    }
})();