document.addEventListener("DOMContentLoaded", function () {
    // Toggle sidebar
    const toggleSidebar = document.getElementById("toggle-sidebar");
    const closeSidebar = document.getElementById("close-sidebar");
    const sidebar = document.getElementById("sidebar");
    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener("click", function () {
            sidebar.classList.toggle("-translate-x-full");
        });
    }
    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener("click", function () {
            sidebar.classList.add("-translate-x-full");
        });
    }

    // Profile dropdown toggle
    const profileToggle = document.getElementById("profile-toggle");
    const profileDropdown = document.getElementById("profile-dropdown");
    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener("click", function () {
            profileDropdown.classList.toggle("hidden");
        });
        document.addEventListener("click", function (event) {
            if (
                !profileDropdown.contains(event.target) &&
                !profileToggle.contains(event.target)
            ) {
                profileDropdown.classList.add("hidden");
            }
        });
    }

    // Format salary
    const salaryInput = document.getElementById("salary");
    if (salaryInput) {
        salaryInput.addEventListener("input", function () {
            let value = this.value.replace(/\D/g, "");
            this.value = value
                ? parseInt(value, 10).toLocaleString("id-ID")
                : "";
        });
        if (salaryInput.form) {
            salaryInput.form.addEventListener("submit", function () {
                salaryInput.value = salaryInput.value.replace(/\D/g, "");
            });
        }
    }

    // Combobox Prodi
    const select = document.getElementById("id_study");
    const input = document.getElementById("prodi-combobox");
    const list = document.getElementById("prodi-list");
    function renderList(filter = "") {
        const filterLower = filter.toLowerCase();
        const options = Array.from(select.options).filter(
            (opt) =>
                opt.value !== "" && opt.text.toLowerCase().includes(filterLower)
        );
        list.innerHTML =
            options.length === 0
                ? '<p class="p-2 text-gray-500">Tidak ada Prodi ditemukan</p>'
                : options
                      .map(
                          (opt) =>
                              `<div class="p-2 cursor-pointer hover:bg-blue-500 hover:text-white" data-value="${opt.value}">${opt.text}</div>`
                      )
                      .join("");
        list.classList.remove("hidden");
    }
    if (select && input && list) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value !== "") {
            input.value = selectedOption.text;
        }
        input.addEventListener("focus", () => renderList(input.value));
        input.addEventListener("input", () => renderList(input.value));
        list.addEventListener("click", (e) => {
            if (e.target && e.target.dataset.value) {
                select.value = e.target.dataset.value;
                input.value = e.target.textContent;
                list.classList.add("hidden");
            }
        });
        document.addEventListener("click", (e) => {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.classList.add("hidden");
            }
        });
    }
    // jQuery Select2
    if (window.$ && $("#id_company").length) {
        $("#id_company").select2({
            placeholder: "Cari atau tambah perusahaan...",
            allowClear: true,
        });
    }

    // --- FORM SUBMISSION HANDLERS ---
    const form = document.getElementById("questionnaireForm");
    if (form) {
        // Pastikan ada input hidden bernama 'action'
        let actionInput = form.querySelector('input[name="action"]');
        if (!actionInput) {
            actionInput = document.createElement("input");
            actionInput.type = "hidden";
            actionInput.name = "action";
            form.appendChild(actionInput);
        }
        // Save draft
        const saveDraftBtn = document.getElementById("save-draft-btn");
        if (saveDraftBtn) {
            saveDraftBtn.addEventListener("click", function (e) {
                e.preventDefault();
                actionInput.value = "save_draft";
                form.submit();
            });
        }
        // Previous category
        const prevCategoryBtn = document.getElementById("prev-category-btn");
        if (prevCategoryBtn) {
            prevCategoryBtn.addEventListener("click", function (e) {
                e.preventDefault();
                actionInput.value = "prev_category";
                form.submit();
            });
        }
        // Next category
        const nextCategoryBtn = document.getElementById("next-category-btn");
        if (nextCategoryBtn) {
            nextCategoryBtn.addEventListener("click", function (e) {
                e.preventDefault();
                actionInput.value = "next_category";
                form.submit();
            });
        }
        // Final submit
        const submitFinalBtn = document.getElementById("submit-final-btn");
        if (submitFinalBtn) {
            submitFinalBtn.addEventListener("click", function (e) {
                e.preventDefault();
                actionInput.value = "submit_final";
                form.submit();
            });
        }
    }

    // Show and hide detail modal (moved to individual components)
    // window.showDetail and window.closeDetail are now handled in components
});
