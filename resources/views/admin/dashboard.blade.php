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

    <!-- Container utama dengan responsive padding dan max-width -->
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 max-w-7xl">
        <!-- Welcome header dengan responsive spacing -->
        <div class="mb-4 sm:mb-6">
            <x-admin.welcome-header :role="'Administrator'" />
        </div>

        <!-- Statistic cards dengan responsive spacing -->
        <div class="mb-4 sm:mb-6">
            <x-admin.statistic-cards 
                :alumniCount="$alumniCount" 
                :companyCount="$companyCount" 
                :answerCount="$answerCount" 
            />
        </div>

        <!-- Statistic chart dengan responsive grid layout -->
        <div class="mb-4 sm:mb-6">
            <div class="grid grid-cols-1 xl:grid-cols-1 gap-4 sm:gap-6">
                <x-admin.statistic-chart 
                    :statisticData="$statisticData"
                    :graduationYearStatisticData="$graduationYearStatisticData"
                    :studyPrograms="$studyPrograms"
                    :respondedPerStudy="$respondedPerStudy"
                    :salaryPerStudy="$salaryPerStudy"
                    :allGraduationYears="$allGraduationYears"
                    :filterGraduationYear="$filterGraduationYear"
                />
            </div>
        </div>

        <!-- Questionnaire Statistics dengan responsive layout -->
        <div class="mb-4 sm:mb-6">
            <div class="grid grid-cols-1 xl:grid-cols-1 gap-4 sm:gap-6">
                <x-admin.questionnaire-statistic-chart 
                    :availablePeriodes="$availablePeriodes"
                    :availableCategories="$availableCategories"
                    :availableQuestions="$availableQuestions"
                    :availableStudyPrograms="$availableStudyPrograms"
                    :selectedPeriode="$selectedPeriode"
                    :selectedUserType="$selectedUserType"
                    :selectedCategory="$selectedCategory"
                    :selectedQuestion="$selectedQuestion"
                    :selectedStudyProgram="$selectedStudyProgram"
                    :questionnaireChartData="$questionnaireChartData"
                    :questionnaireLabels="$questionnaireLabels"
                    :questionnaireValues="$questionnaireValues"
                />
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
