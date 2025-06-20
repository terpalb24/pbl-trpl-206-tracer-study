<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
    <!-- Alumni Card -->
    <div class="flex items-center p-3 sm:p-4 lg:p-5 bg-blue-950 text-white rounded-xl sm:rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 gap-3 sm:gap-4">
        <div class="bg-white p-2 sm:p-3 rounded-xl sm:rounded-2xl shadow flex-shrink-0">
            <i class="fas fa-user-graduate text-blue-950 text-lg sm:text-xl lg:text-2xl"></i>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-lg sm:text-xl lg:text-2xl font-bold truncate">{{ number_format($alumniCount) }}</div>
            <div class="text-sm sm:text-base lg:text-lg text-blue-100">Alumni</div>
        </div>
    </div>

    <!-- Company Card -->
    <div class="flex items-center p-3 sm:p-4 lg:p-5 bg-sky-400 text-white rounded-xl sm:rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 gap-3 sm:gap-4">
        <div class="bg-white p-2 sm:p-3 rounded-xl sm:rounded-2xl shadow flex-shrink-0">
            <i class="fas fa-building text-sky-400 text-lg sm:text-xl lg:text-2xl"></i>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-lg sm:text-xl lg:text-2xl font-bold truncate">{{ number_format($companyCount) }}</div>
            <div class="text-sm sm:text-base lg:text-lg text-sky-100">Perusahaan</div>
        </div>
    </div>

    <!-- Answer Card -->
    <div class="flex items-center p-3 sm:p-4 lg:p-5 bg-orange-500 text-white rounded-xl sm:rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 gap-3 sm:gap-4 sm:col-span-2 lg:col-span-1">
        <div class="bg-white p-2 sm:p-3 rounded-xl sm:rounded-2xl shadow flex-shrink-0">
            <i class="fas fa-check-circle text-orange-500 text-lg sm:text-xl lg:text-2xl"></i>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-lg sm:text-xl lg:text-2xl font-bold truncate">{{ number_format($answerCount) }}</div>
            <div class="text-sm sm:text-base lg:text-lg text-orange-100">Mengisi Kuisioner</div>
        </div>
    </div>
</div>
