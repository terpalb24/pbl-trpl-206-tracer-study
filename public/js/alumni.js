document.addEventListener('DOMContentLoaded', function () {
    // Toggle sidebar
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.add('hidden');
    });

    // Profile dropdown toggle
    document.getElementById('profile-toggle').addEventListener('click', function () {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Format salary
    const salaryInput = document.getElementById('salary');
    salaryInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        this.value = value ? parseInt(value, 10).toLocaleString('id-ID') : '';
    });
    salaryInput.form.addEventListener('submit', function () {
        salaryInput.value = salaryInput.value.replace(/\D/g, '');
    });

    // Combobox Prodi
    const select = document.getElementById('id_study');
    const input = document.getElementById('prodi-combobox');
    const list = document.getElementById('prodi-list');

    function renderList(filter = '') {
        const filterLower = filter.toLowerCase();
        const options = Array.from(select.options).filter(opt =>
            opt.value !== '' && opt.text.toLowerCase().includes(filterLower)
        );
        list.innerHTML = options.length === 0
            ? '<p class="p-2 text-gray-500">Tidak ada Prodi ditemukan</p>'
            : options.map(opt => `<div class="p-2 cursor-pointer hover:bg-blue-500 hover:text-white" data-value="${opt.value}">${opt.text}</div>`).join('');
        list.classList.remove('hidden');
    }

    if (select && input && list) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value !== '') {
            input.value = selectedOption.text;
        }

        input.addEventListener('focus', () => renderList(input.value));
        input.addEventListener('input', () => renderList(input.value));

        list.addEventListener('click', (e) => {
            if (e.target && e.target.dataset.value) {
                select.value = e.target.dataset.value;
                input.value = e.target.textContent;
                list.classList.add('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.classList.add('hidden');
            }
        });
    }
// jQuery Select2
$(document).ready(function() {
    $('#id_company').select2({
        placeholder: 'Cari atau tambah perusahaan...',
        allowClear: true
    });
})})


    
    document.addEventListener('DOMContentLoaded', function () {
        const salaryInput = document.getElementById('salary');
        salaryInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });

        // Saat submit, hilangkan format ribuan agar value yang dikirim hanya angka
        salaryInput.form.addEventListener('submit', function () {
            salaryInput.value = salaryInput.value.replace(/\D/g, '');
        });
    });
    // Show and hide detail modal
    function showDetail(id) {
        document.getElementById('modal-detail-' + id).classList.remove('hidden');
    }

    function closeDetail(id) {
        document.getElementById('modal-detail-' + id).classList.add('hidden');
    }

    