@props(['alumni'])

<div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-6 overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <div class="p-4 sm:p-6 lg:p-8 md:w-2/3">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-2">Halo!</h2>
            <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-black mb-3 sm:mb-4 break-words">{{ $alumni->name }}</h3>
            <p class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-normal text-black mb-4 sm:mb-6 leading-relaxed">
                Silahkan isi kuisioner Tracer Study untuk membantu pengembangan Polibatam!!
            </p>
            <a href="{{ route('alumni.questionnaire.index') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md flex items-center space-x-2 w-fit transition-colors duration-200 text-sm sm:text-base">
                <span>Isi Kuisioner</span>
                <i class="fas fa-file-alt text-sm sm:text-base"></i>
            </a>
        </div>
        <div class="md:w-1/3 flex items-center justify-center p-4 sm:p-6">
            <img src="{{ asset('assets/images/graduation.png') }}" alt="Graduation" class="h-32 w-32 sm:h-40 sm:w-40 md:h-48 md:w-48 lg:h-56 lg:w-56 xl:h-64 xl:w-64 object-cover">
        </div>
    </div>
</div>
