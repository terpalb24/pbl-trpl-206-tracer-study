@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Test Pertanyaan Lokasi</h2>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium mb-4">Dimana lokasi tempat Anda bekerja?</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Provinsi:</label>
                <select id="province-select" class="w-full px-3 py-2 border rounded-md">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Kota/Kabupaten:</label>
                <select id="city-select" class="w-full px-3 py-2 border rounded-md" disabled>
                    <option value="">-- Pilih Kota/Kabupaten --</option>
                </select>
            </div>
        </div>
        
        <div id="selected-location" class="mt-4 p-3 bg-gray-50 rounded hidden">
            <p class="text-sm text-gray-600">Lokasi yang dipilih: <span id="location-text" class="font-medium"></span></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province-select');
    const citySelect = document.getElementById('city-select');
    const selectedLocation = document.getElementById('selected-location');
    const locationText = document.getElementById('location-text');
    
    // Load provinces on page load
    loadProvinces();
    
    // Province change handler
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        if (provinceId) {
            loadCities(provinceId);
            citySelect.disabled = false;
        } else {
            citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
            citySelect.disabled = true;
            selectedLocation.classList.add('hidden');
        }
    });
    
    // City change handler
    citySelect.addEventListener('change', function() {
        updateSelectedLocation();
    });
    
    function loadProvinces() {
        fetch('{{ route("admin.questionnaire.provinces") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.id;
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });
                } else {
                    console.error('Failed to load provinces:', data.message);
                    alert('Gagal memuat data provinsi');
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
                alert('Error saat memuat data provinsi');
            });
    }
    
    function loadCities(provinceId) {
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;
        
        fetch(`{{ url('/admin/questionnaire/cities') }}/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    data.data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                } else {
                    console.error('Failed to load cities:', data.message);
                    citySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading data</option>';
            });
    }
    
    function updateSelectedLocation() {
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text;
        const cityName = citySelect.options[citySelect.selectedIndex]?.text;
        
        if (provinceName && cityName && provinceName !== '-- Pilih Provinsi --' && cityName !== '-- Pilih Kota/Kabupaten --') {
            locationText.textContent = `${cityName}, ${provinceName}`;
            selectedLocation.classList.remove('hidden');
        } else {
            selectedLocation.classList.add('hidden');
        }
    }
});
</script>
@endsection