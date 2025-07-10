document.addEventListener("DOMContentLoaded", () => {
    const toggleSidebar = document.getElementById("toggle-sidebar");
    const closeSidebar = document.getElementById("close-sidebar");
    const sidebar = document.getElementById("sidebar");
    const profileToggle = document.getElementById("profile-toggle");
    const profileDropdown = document.getElementById("profile-dropdown");
    const navbar = document.getElementById("navbar");

    // Hamburgers NPM
    navbar.addEventListener("click", () => {
        if (navbar.classList.contains("is-active")) {
            navbar.classList.remove("is-active");
        } else {
            navbar.classList.add("is-active");
        }
    });

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

// SweetAlert2 default configuration
document.addEventListener("DOMContentLoaded", function () {
    // Set default configuration for all SweetAlert2 modals
    const defaultConfig = {
        customClass: {
            popup: "swal2-show",
            title: "text-lg font-semibold mb-2",
            confirmButton:
                "px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2",
            cancelButton:
                "px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2",
        },
        buttonsStyling: false,
        reverseButtons: true,
        focusConfirm: false,
        allowEnterKey: false,
    };

    // Apply default configuration
    const Toast = Swal.mixin(defaultConfig);
    window.Toast = Toast;
});

// Function to validate Excel file
function validateExcelFile(input) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = [
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // .xlsx
        "application/vnd.ms-excel", // .xls
    ];
    const file = input.files[0];

    if (!file) {
        Swal.fire({
            title: "Error",
            text: "Pilih file Excel terlebih dahulu",
            icon: "error",
            confirmButtonText: "OK",
        });
        input.value = "";
        return false;
    }

    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            title: "Error",
            text: "File harus berupa Excel (.xlsx atau .xls)",
            icon: "error",
            confirmButtonText: "OK",
        });
        input.value = "";
        return false;
    }

    if (file.size > maxSize) {
        Swal.fire({
            title: "Error",
            text: "Ukuran file tidak boleh lebih dari 5MB",
            icon: "error",
            confirmButtonText: "OK",
        });
        input.value = "";
        return false;
    }

    return true;
}

// Handle form submission for company import
document.addEventListener("DOMContentLoaded", function () {
    const importForm = document.getElementById("importForm");
    if (importForm) {
        importForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const fileInput = this.querySelector('input[type="file"]');
            if (!validateExcelFile(fileInput)) return;

            const formData = new FormData(this);

            const result = await Swal.fire({
                title: "Konfirmasi Import",
                text: "Apakah Anda yakin ingin mengimport data perusahaan?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Import",
                cancelButtonText: "Batal",
            });

            if (result.isConfirmed) {
                try {
                    // Show loading state
                    Swal.fire({
                        title: "Mengimport Data",
                        text: "Mohon tunggu...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    const response = await fetch(importForm.action, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                    });

                    let data;
                    const contentType = response.headers.get("content-type");
                    if (
                        contentType &&
                        contentType.includes("application/json")
                    ) {
                        data = await response.json();
                    } else {
                        throw new Error(
                            "Server tidak mengembalikan response JSON yang valid"
                        );
                    }

                    if (response.ok && data.success) {
                        await Swal.fire({
                            title: "Berhasil!",
                            text:
                                data.message ||
                                "Data perusahaan berhasil diimport.",
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                        window.location.reload();
                    } else {
                        throw new Error(
                            data.message ||
                                "Terjadi kesalahan saat mengimport data"
                        );
                    }
                } catch (error) {
                    console.error("Error importing companies:", error);
                    await Swal.fire({
                        title: "Error!",
                        text:
                            error.message ||
                            "Terjadi kesalahan saat mengimport data",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            }
        });
    }
});
