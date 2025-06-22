<ul class="space-y-2">
    <li>
        <a href="{{ route('dashboard.alumni') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('dashboard.admin') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-home-alt w-6 text-center"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('alumni.questionnaire.index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.alumni.*') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-file-alt w-6 text-center"></i>
            <span class="ml-3">Kuisioner</span>
        </a>
    </li>
    <li>
        <a href="{{ route('alumni.questionnaire.results') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.company.*') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-history w-6 text-center"></i>
            <span class="ml-3">Riwayat Kuesioner</span>
        </a>
    </li>
    <li>
        <a href="{{ route('alumni.job-history.index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800">
            <i class="fas fa-briefcase w-6 text-center"></i>
            <span class="ml-3">Riwayat Kerja</span>
        </a>
    </li>
    <li>
        <a href="{{ route('alumni.edit') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800">
            <i class="fas fa-user w-6 text-center"></i>
            <span class="ml-3">Edit Profil</span>
        </a>
    </li>
</ul>
