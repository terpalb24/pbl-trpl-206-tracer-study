<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="flex items-center p-4 bg-blue-950 text-white rounded-2xl shadow gap-4">
        <div class="bg-white p-3 rounded-2xl shadow ">
            <i class="fas fa-user-graduate text-blue-950 text-2xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold">{{ number_format($alumniCount) }}</div>
            <div class="text-2xl">Alumni</div>
        </div>
    </div>
    <div class="flex items-center p-4 bg-sky-400 text-white rounded-2xl shadow gap-4">
        <div class="bg-white p-3 rounded-2xl shadow">
            <i class="fas fa-building text-sky-400 text-2xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold">{{ number_format($companyCount) }}</div>
            <div class="text-2xl">Perusahaan</div>
        </div>
    </div>
    <div class="flex items-center p-4 bg-orange-500 text-white rounded-2xl shadow gap-4">
        <div class="bg-white p-3 rounded-2xl shadow">
            <i class="fas fa-check-circle text-orange-500 text-2xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold">2.300</div>
            <div class="text-2xl">Mengisi Kuisioner</div>
        </div>
    </div>
</div>
