@props(['alumni'])

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 space-y-2 sm:space-y-0">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Profil Saya</h2>
        <a href="{{ route('alumni.edit') }}" class="bg-blue-900 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-md flex items-center justify-center space-x-2 text-sm sm:text-base">
            <span>Edit Profile</span>
            <i class="fas fa-edit"></i>
        </a>
    </div>

    <div class="flex flex-col lg:flex-row">
        <div class="lg:w-1/4 mb-6 lg:mb-0 flex justify-center">
            <div class="bg-blue-200 rounded-lg w-32 h-32 sm:w-40 sm:h-40 overflow-hidden">
                <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Profile Picture" class="w-full h-full object-cover">
            </div>
        </div>
        <div class="lg:w-3/4 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">NIM</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->nim }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Jenis Kelamin</h3>
                <p class="font-semibold text-sm sm:text-base">{{ $alumni->gender }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Nama</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->name }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Email</h3>
                <p class="font-semibold text-sm sm:text-base break-all">{{ $alumni->email }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">No Telp</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->phone_number }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Prodi</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->studyProgram->study_program ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Alamat</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->address ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Angkatan</h3>
                <p class="font-semibold text-sm sm:text-base">{{ $alumni->batch }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Tahun Lulus</h3>
                <p class="font-semibold text-sm sm:text-base">{{ $alumni->graduation_year }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">IPK</h3>
                <p class="font-semibold text-sm sm:text-base">{{ $alumni->ipk }}</p>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1">Status Pekerjaan</h3>
                <p class="font-semibold text-sm sm:text-base break-words">{{ $alumni->status }}</p>
            </div>
        </div>
    </div>
</div>
