<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/admin">
            <div class="brand">
                <div class="brand-text">Harris Hotel Seminyak</div>
            </div>
        </a>
        
        <div class="menu-toggle" id="admin-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <p class="sidebar-desc"></p>
    
    <nav class="nav" id="admin-nav-links">
        <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="/admin/orders" class="{{ request()->is('admin/orders') ? 'active' : '' }}">
            <span class="nav-label">Work Orders</span>
        </a>
        <a href="/admin/report" class="{{ request()->is('admin/report') ? 'active' : '' }}">
            <span class="nav-label">Report</span>
        </a>
        <div style="border-top: 1px solid #e5e7eb; margin: 0.5rem 0;"></div>
        <div style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">Settings</div>
        <a href="/admin/settings/departments" class="{{ request()->is('admin/settings/departments') ? 'active' : '' }}" style="padding-left: 2rem;">
            <span class="nav-label">Departments</span>
        </a>
        <a href="/admin/settings/issue-types" class="{{ request()->is('admin/settings/issue-types') ? 'active' : '' }}" style="padding-left: 2rem;">
            <span class="nav-label">Issue Types</span>
        </a>
        <a href="/admin/settings/users" class="{{ request()->is('admin/settings/users') ? 'active' : '' }}" style="padding-left: 2rem;">
            <span class="nav-label">Pengaturan Admin WA</span>
        </a>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('admin-menu-toggle');
        const navLinks = document.getElementById('admin-nav-links');
        
        if (toggleBtn && navLinks) {
            toggleBtn.addEventListener('click', function() {
                // Tambah/hapus class 'active' saat diklik
                toggleBtn.classList.toggle('active');
                navLinks.classList.toggle('active');
            });
        }
    });
</script>