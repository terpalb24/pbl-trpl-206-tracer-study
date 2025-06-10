@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Beranda</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <x-admin.welcome-header :role="'Administrator'" />

        <div class="mb-6">
            <x-admin.statistic-cards :alumniCount="$alumniCount" :companyCount="$companyCount" />
        </div>

        <div class="mb-6">
            <x-admin.statistic-chart :statisticData="$statisticData" />
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
