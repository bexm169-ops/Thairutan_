/* ==========================================================================
   BKK WELFARE MATCHER - main.js
   หน้า welfare, my-rights, rights-detail ใช้ PHP render โดยตรงแล้ว
   JS นี้ทำหน้าที่: Mobile Menu + หน้าแรก stats
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile Menu ──
    const menuOpenBtn  = document.getElementById('menu-open');
    const menuCloseBtn = document.getElementById('menu-close');
    const navMenu      = document.getElementById('nav-menu');
    if (menuOpenBtn && menuCloseBtn && navMenu) {
        menuOpenBtn.addEventListener('click',  () => navMenu.classList.add('active'));
        menuCloseBtn.addEventListener('click', () => navMenu.classList.remove('active'));
        // ปิดเมนูเมื่อกดนอกพื้นที่
        document.addEventListener('click', e => {
            if (navMenu.classList.contains('active') &&
                !navMenu.contains(e.target) &&
                !menuOpenBtn.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }

    // ── หน้าแรก: ดึง stats ──
    if (document.getElementById('stat-total-welfare')) {
        loadHomeStats();
    }
});

async function loadHomeStats() {
    try {
        // หา base path อัตโนมัติ
        const base = window.location.pathname.replace(/\/[^\/]*$/, '/');
        const res  = await fetch(base + 'api/get_stats.php');
        const json = await res.json();
        if (json.success) {
            document.getElementById('stat-total-welfare').textContent = json.total_welfare;
            document.getElementById('stat-avg-time').textContent      = json.avg_time;
            document.getElementById('stat-avg-benefit').textContent   = json.avg_benefit;
        }
    } catch (e) {
        console.error('loadHomeStats:', e);
    }
}
