<div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-4 sm:mb-6 overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8 md:w-2/3">
            <!-- Welcome Text -->
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-1 sm:mb-2">
                Hello!
            </h2>
            
            <!-- Company Name -->
            <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-semibold text-black mb-2 sm:mb-3 lg:mb-4 break-words">
                {{ $company->company_name ?? 'Perusahaan' }}
            </h3>
            
            <!-- Description -->
            <p class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal text-black mb-4 sm:mb-5 lg:mb-6 leading-relaxed">
                Terima kasih telah berpartisipasi dalam Tracer Study Polibatam!
            </p>
            
            <!-- CTA Button -->
            <a href="{{ route('company.questionnaire.index') }}" 
               class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 sm:px-5 sm:py-2.5 lg:px-6 lg:py-3 rounded-md flex items-center justify-center sm:justify-start space-x-2 w-full sm:w-fit transition-colors duration-200 text-sm sm:text-base lg:text-lg">
                <span>Isi Kuisioner</span>
                <i class="fas fa-file-alt text-sm sm:text-base"></i>
            </a>
        </div>
        
        <!-- Image Section -->
        <div class="md:w-1/3 flex items-center justify-center p-4 sm:p-6 lg:p-8">
            <div class="relative">
                <img src="{{ asset('assets/images/graduation.png') }}" 
                     alt="Graduation" 
                     class="h-32 w-32 sm:h-40 sm:w-40 md:h-48 md:w-48 lg:h-56 lg:w-56 xl:h-64 xl:w-64 object-cover drop-shadow-lg">
            </div>
        </div>
    </div>
</div>
