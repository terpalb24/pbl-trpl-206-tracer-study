document.addEventListener('DOMContentLoaded', () => {
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');
    const logoutBtn = document.getElementById('logout-btn');

    // Toggle sidebar (mobile)
    toggleSidebar?.addEventListener('click', () => {
        sidebar?.classList.toggle('-translate-x-full');
    });

    closeSidebar?.addEventListener('click', () => {
        sidebar?.classList.add('-translate-x-full');
    });

    // Toggle profile dropdown
    profileToggle?.addEventListener('click', () => {
        profileDropdown?.classList.toggle('hidden');
    });

    // Logout button (POST ke route logout Laravel)
    logoutBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('Yakin ingin logout?')) {
            const logoutUrl = logoutBtn.getAttribute('data-logout-url');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!logoutUrl || !csrfToken) {
                alert('Logout URL atau CSRF token tidak ditemukan.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = logoutUrl;

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;

            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Responsive sidebar saat halaman pertama kali dimuat
    if (window.innerWidth < 1024 && sidebar) {
        sidebar.classList.add('-translate-x-full');
    }
});
