<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Profil Perusahaan</h2>
        <a href="{{ route('company.edit') }}" 
           class="bg-blue-900 hover:bg-blue-800 text-white px-3 py-2 sm:px-4 lg:px-6 lg:py-3 rounded-md flex items-center justify-center space-x-2 transition-colors duration-200 text-sm sm:text-base">
            <span class="hidden sm:inline">Edit Profil</span>
            <span class="sm:hidden">Edit</span>
            <i class="fas fa-edit text-sm sm:text-base"></i>
        </a>
    </div>

    <!-- Content Section -->
    <div class="flex flex-col lg:flex-row gap-4 sm:gap-6 lg:gap-8">
        <!-- Profile Picture Section -->
        <div class="flex justify-center lg:justify-start lg:w-1/4 xl:w-1/5">
            <div class="bg-blue-200 rounded-lg w-32 h-32 sm:w-36 sm:h-36 md:w-40 md:h-40 lg:w-full lg:h-40 xl:h-48 overflow-hidden shadow-sm">
                <img src="{{ asset('assets/images/profilepicture.jpg') }}" 
                     alt="Profile Picture" 
                     class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Information Grid -->
        <div class="lg:w-3/4 xl:w-4/5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                <!-- Company Name -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-1">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Nama Perusahaan</h3>
                    <p class="font-semibold text-sm sm:text-base lg:text-lg text-gray-800 break-words">
                        {{ $company->company_name ?? 'Belum diisi' }}
                    </p>
                </div>

                <!-- Email -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-1">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Email</h3>
                    <p class="font-semibold text-sm sm:text-base lg:text-lg text-gray-800 break-words">
                        {{ $company->company_email ?? 'Belum diisi' }}
                    </p>
                </div>

                <!-- Phone Number -->
                <div class="col-span-1">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Nomor Telepon</h3>
                    <p class="font-semibold text-sm sm:text-base lg:text-lg text-gray-800">
                        {{ $company->company_phone_number ?? 'Belum diisi' }}
                    </p>
                </div>

                <!-- Website (if exists) -->
                @if(isset($company->company_website) && $company->company_website)
                <div class="col-span-1">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Website</h3>
                    <a href="{{ $company->company_website }}" 
                       target="_blank" 
                       class="font-semibold text-sm sm:text-base lg:text-lg text-blue-600 hover:text-blue-800 underline break-words">
                        {{ $company->company_website }}
                    </a>
                </div>
                @endif

                <!-- Address -->
                <div class="col-span-1 sm:col-span-2">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Alamat</h3>
                    <p class="font-semibold text-sm sm:text-base lg:text-lg text-gray-800 break-words">
                        {{ $company->company_address ?? 'Belum diisi' }}
                    </p>
                </div>

                <!-- Company Description (if exists) -->
                @if(isset($company->company_description) && $company->company_description)
                <div class="col-span-1 sm:col-span-2">
                    <h3 class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 uppercase tracking-wide">Deskripsi Perusahaan</h3>
                    <p class="font-semibold text-sm sm:text-base lg:text-lg text-gray-800 break-words leading-relaxed">
                        {{ $company->company_description }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Info Section (if needed) -->
    @if(isset($company->company_founded) || isset($company->company_size))
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @if(isset($company->company_founded))
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1 uppercase tracking-wide">Tahun Berdiri</h3>
                <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $company->company_founded }}</p>
            </div>
            @endif

            @if(isset($company->company_size))
            <div>
                <h3 class="text-xs sm:text-sm text-gray-500 mb-1 uppercase tracking-wide">Ukuran Perusahaan</h3>
                <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $company->company_size }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
