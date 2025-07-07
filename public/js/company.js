document.addEventListener("DOMContentLoaded", function () {
    // Toggle sidebar
    const toggleSidebar = document.getElementById("toggle-sidebar");
    const closeSidebar = document.getElementById("close-sidebar");
    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("sidebar-backdrop");

    // Pastikan sidebar tertutup di mobile saat load
    if (sidebar) {
        if (window.innerWidth < 1024) {
            sidebar.classList.add("-translate-x-full");
            if (backdrop) backdrop.classList.add("hidden");
        } else {
            sidebar.classList.remove("-translate-x-full");
            if (backdrop) backdrop.classList.add("hidden");
        }
    }

    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener("click", function () {
            const isHidden = sidebar.classList.contains("-translate-x-full");
            
            if (isHidden) {
                sidebar.classList.remove("-translate-x-full");
                if (backdrop) backdrop.classList.remove("hidden");
            } else {
                sidebar.classList.add("-translate-x-full");
                if (backdrop) backdrop.classList.add("hidden");
            }
        });
    }

    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener("click", function () {
            sidebar.classList.add("-translate-x-full");
            if (backdrop) backdrop.classList.add("hidden");
        });
    }

    // Close sidebar when clicking backdrop
    if (backdrop && sidebar) {
        backdrop.addEventListener("click", function () {
            sidebar.classList.add("-translate-x-full");
            backdrop.classList.add("hidden");
        });
    }

    // Handle window resize
    window.addEventListener("resize", function () {
        if (sidebar) {
            if (window.innerWidth >= 1024) {
                // Desktop: show sidebar, hide backdrop
                sidebar.classList.remove("-translate-x-full");
                if (backdrop) backdrop.classList.add("hidden");
            } else {
                // Mobile: hide sidebar and backdrop
                sidebar.classList.add("-translate-x-full");
                if (backdrop) backdrop.classList.add("hidden");
            }
        }
    });

    // Profile dropdown toggle
    const profileToggle = document.getElementById("profile-toggle");
    const profileDropdown = document.getElementById("profile-dropdown");

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener("click", function () {
            profileDropdown.classList.toggle("hidden");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function (event) {
            if (
                !profileDropdown.contains(event.target) &&
                !profileToggle.contains(event.target)
            ) {
                profileDropdown.classList.add("hidden");
            }
        });
    }
    // Form validation helper
    window.validateForm = function (formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                field.classList.add("border-red-500");
                isValid = false;
            } else {
                field.classList.remove("border-red-500");
            }
        });

        return isValid;
    };

    // Show and hide detail modal
    window.showDetail = function (id) {
        const modal = document.getElementById("modal-detail-" + id);
        if (modal) {
            modal.classList.remove("hidden");
        }
    };

    window.closeDetail = function (id) {
        const modal = document.getElementById("modal-detail-" + id);
        if (modal) {
            modal.classList.add("hidden");
        }
    };

    // Notification helper
    window.showNotification = function (message, type = "info") {
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm`;

        const bgColor =
            {
                success: "bg-green-500",
                error: "bg-red-500",
                warning: "bg-yellow-500",
                info: "bg-blue-500",
            }[type] || "bg-blue-500";

        notification.className += ` ${bgColor} text-white`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-sm">${message}</span>
                <button onclick="this.remove()" class="ml-4 text-white hover:text-gray-200 text-lg">
                    &times;
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };
});
