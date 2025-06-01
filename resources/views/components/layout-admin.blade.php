<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside {{ $attributes->merge(['class' => 'sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300', 'id' => 'sidebar']) }}>
        {{ $sidebar }}
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        {{ $header }}
        {{ $slot }}
    </main>
</div>
