document.addEventListener("DOMContentLoaded", () => {
    const toggleSidebar = document.getElementById("toggle-sidebar");
    const closeSidebar = document.getElementById("close-sidebar");
    const sidebar = document.getElementById("sidebar");
    const profileToggle = document.getElementById("profile-toggle");
    const profileDropdown = document.getElementById("profile-dropdown");

    // Toggle sidebar (mobile)
    toggleSidebar?.addEventListener("click", () => {
        sidebar?.classList.toggle("-translate-x-full");
    });

    closeSidebar?.addEventListener("click", () => {
        sidebar?.classList.add("-translate-x-full");
    });

    // Toggle profile dropdown
    profileToggle?.addEventListener("click", () => {
        profileDropdown?.classList.toggle("hidden");
    });

    // Responsive sidebar saat halaman pertama kali dimuat
    if (window.innerWidth < 1024 && sidebar) {
        sidebar.classList.add("-translate-x-full");
    }
});

// Delete modal scripts
function openDeleteModal(id) {
    document.getElementById("modal-delete-" + id).classList.remove("hidden");
    document.body.style.overflow = "hidden";
}
function closeDeleteModal(id) {
    document.getElementById("modal-delete-" + id).classList.add("hidden");
    document.body.style.overflow = "auto";
}
// Tutup modal jika klik backdrop
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("bg-black/50")) {
        document.querySelectorAll('[id^="modal-delete-"]').forEach((modal) => {
            modal.classList.add("hidden");
        });
        document.body.style.overflow = "auto";
    }
});
// Tutup modal dengan Escape
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        document.querySelectorAll('[id^="modal-delete-"]').forEach((modal) => {
            modal.classList.add("hidden");
        });
        document.body.style.overflow = "auto";
    }
});
