<div class="w-full min-h-screen bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Fixed Sidebar -->
    <aside
        id="sidebar"
        class="fixed top-0 left-0 w-64 h-screen bg-blue-950 text-white flex flex-col overflow-y-auto z-50 transition-transform duration-300
            -translate-x-full lg:translate-x-0"
        style="will-change: transform;"
    >
        {{ $sidebar }}
    </aside>

    <!-- Fixed Header -->
    <header class="fixed top-0 left-0 right-0 z-40 lg:ml-64" style="height:80px;">
        {{ $header }}
    </header>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">
        {{ $slot }}
    </main>
</div>