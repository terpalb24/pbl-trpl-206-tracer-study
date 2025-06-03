<div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-6 overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <div class="p-6 md:w-2/3">
            <h2 class="text-5xl font-bold text-black mb-2">Halo!</h2>
            <h3 class="text-4xl font-semibold text-black mb-4">{{ $company->company_name }}</h3>
            <p class="text-2xl font-normal text-black mb-6">
                Terima kasih telah berpartisipasi dalam Tracer Study Polibatam!
            </p>
            <a href="{{ route('company.questionnaire.index') }}" class="bg-blue-900 text-white px-6 py-2 rounded-md flex items-center space-x-2 w-fit">
                <span>Isi Kuisioner</span>
                <i class="fas fa-file-alt"></i>
            </a>
        </div>
        <div class="md:w-1/3 flex items-center justify-center p-4">
            <img src="{{ asset('assets/images/graduation.png') }}" alt="Graduation" class="h-64 w-64 object-cover">
        </div>
    </div>
</div>
