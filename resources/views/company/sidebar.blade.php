<!-- Navigasi Sidebar -->
<nav class="flex flex-col gap-3">
    <a href="{{ route('dashboard.company') }}"
       class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('dashboard.company') ? 'bg-blue-800' : '' }}">
        <i class="fas fa-home w-5 text-center"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('company.questionnaire.index') }}"
       class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('company.questionnaire.index') ? 'bg-blue-800' : '' }}">
        <i class="fas fa-file-alt w-5 text-center"></i>
        <span>Kuisioner</span>
    </a>
    <a href="{{ route('company.questionnaire.results') }}"
       class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('company.questionnaire.results') || request()->routeIs('company.questionnaire.response-detail') ? 'bg-blue-800' : '' }}">
        <i class="fas fa-history w-5 text-center"></i>
        <span>Riwayat Kuesioner</span>
    </a>
    <a href="{{ route('company.edit') }}"
       class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('company.edit') ? 'bg-blue-800' : '' }}">
        <i class="fas fa-user w-5 text-center"></i>
        <span>Edit Profil</span>
    </a>
</nav>
