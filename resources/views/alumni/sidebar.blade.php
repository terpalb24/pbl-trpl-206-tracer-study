<a href="{{ route('dashboard.alumni') }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('dashboard.alumni') ? 'bg-blue-800' : '' }}">
                <i class="fas fa-home w-5 text-center"></i> <span>Beranda</span>
            </a>
            <a href="{{ route('alumni.questionnaire.index') }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('alumni.questionnaire.*') && !request()->routeIs('alumni.questionnaire.results') ? 'bg-blue-800' : '' }}">
                <i class="fas fa-file-alt w-5 text-center"></i> <span>Kuisioner</span>
            </a>
            <a href="{{ route('alumni.questionnaire.results') }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('alumni.questionnaire.results') ? 'bg-blue-800' : '' }}">
                <i class="fas fa-history w-5 text-center"></i> <span>Riwayat Kuesioner</span>
            </a>
            <a href="{{ route('alumni.job-history.index') }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('alumni.job-history.index') ? 'bg-blue-800' : '' }}">
                <i class="fas fa-briefcase w-5 text-center"></i> <span>Riwayat Kerja</span>
            </a>
            <a href="{{ route('alumni.edit') }}" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition {{ request()->routeIs('alumni.edit') ? 'bg-blue-800' : '' }}">
                <i class="fas fa-user w-5 text-center"></i> <span>Edit Profil</span>
            </a>
        </nav>