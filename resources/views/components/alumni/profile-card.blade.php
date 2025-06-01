@props(['alumni'])

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Profil Saya</h2>
        <a href="{{ route('alumni.edit') }}" class="bg-blue-900 text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <span>Edit Profile</span>
            <i class="fas fa-edit"></i>
        </a>
    </div>

    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/4 mb-6 md:mb-0 flex justify-center">
            <div class="bg-blue-200 rounded-lg w-40 h-40 overflow-hidden">
                <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Profile Picture" class="w-full h-full object-cover">
            </div>
        </div>
        <div class="md:w-3/4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm text-gray-500 mb-1">NIM</h3>
                <p class="font-semibold">{{ $alumni->nim }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Jenis Kelamin</h3>
                <p class="font-semibold">{{ $alumni->gender }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Nama</h3>
                <p class="font-semibold">{{ $alumni->name }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Email</h3>
                <p class="font-semibold">{{ $alumni->email }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">No Telp</h3>
                <p class="font-semibold">{{ $alumni->phone_number }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Prodi</h3>
                <p class="font-semibold">{{ $alumni->studyProgram->study_program ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Angkatan</h3>
                <p class="font-semibold">{{ $alumni->batch }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Tahun Lulus</h3>
                <p class="font-semibold">{{ $alumni->graduation_year }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">IPK</h3>
                <p class="font-semibold">{{ $alumni->ipk }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500 mb-1">Status Pekerjaan</h3>
                <p class="font-semibold">{{ $alumni->status }}</p>
            </div>
        </div>
    </div>
</div>
