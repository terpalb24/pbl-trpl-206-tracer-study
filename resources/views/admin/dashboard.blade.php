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

    <x-admin.welcome-header :role="'Administrator'" />

    <x-admin.statistic-cards :alumniCount="$alumniCount" :companyCount="$companyCount" />

    <x-admin.statistic-chart />

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
