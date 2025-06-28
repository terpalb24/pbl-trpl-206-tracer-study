<ul class="space-y-2">
    <li>
        <a href="{{ route('dashboard.admin') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('dashboard.admin') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-tachometer-alt w-6 text-center"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.alumni.index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.alumni.*') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-user-graduate w-6 text-center"></i>
            <span class="ml-3">Alumni</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.company.index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.company.*') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-building w-6 text-center"></i>
            <span class="ml-3">Perusahaan</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.questionnaire.index') }}" class="flex items-center p-2 text-white rounded-lg hover:bg-blue-800 {{ request()->routeIs('admin.questionnaire.*') ? 'bg-blue-800' : '' }}">
            <i class="fas fa-clipboard-list w-6 text-center"></i>
            <span class="ml-3">Kuisioner</span>
        </a>
    </li>
</ul>
