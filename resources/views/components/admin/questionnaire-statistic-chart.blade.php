<div class="bg-orange-200 p-6 rounded-2xl shadow">
    <div class="font-bold mb-6 text-xl text-orange-900">Statistik Kuesioner</div>
    <!-- Mobile Detection Notice -->
    <div class="block md:hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Mode Desktop Diperlukan
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Jika ingin melihat statistik kuesioner, silahkan menggunakan mode desktop untuk pengalaman yang lebih baik.</p>
                    <p class="mt-1 text-xs">Fitur statistik memerlukan layar yang lebih lebar untuk menampilkan grafik dan data dengan optimal.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter Form -->
    <div class="hidden md:block">
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-orange-800 mb-4">Filter Statistik</h3>
        <form method="GET" id="questionnaire-filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Preserve other filters -->
            @if(request('graduation_year_filter'))
                <input type="hidden" name="graduation_year_filter" value="{{ request('graduation_year_filter') }}">
            @endif
            @if(request('study_program'))
                <input type="hidden" name="study_program" value="{{ request('study_program') }}">
            @endif
            @if(request('study_program_salary'))
                <input type="hidden" name="study_program_salary" value="{{ request('study_program_salary') }}">
            @endif
            
            <!-- ✅ PERUBAHAN: Tahun Filter (menggantikan Periode) -->
            <div>
                <label for="questionnaire_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun:</label>
                <select name="questionnaire_year" id="questionnaire_year" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleYearChange()">
                    <option value="">Pilih Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" 
                                {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                @if(isset($periodsInYear) && $periodsInYear->count() > 1)
                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $periodsInYear->count() }} periode dalam tahun ini
                    </div>
                @elseif(isset($periodsInYear) && $periodsInYear->count() == 1)
                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $periodsInYear->first()->periode }}
                    </div>
                @endif
            </div>
            
            <!-- User Type Filter -->
            <div>
                <label for="questionnaire_user_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pengguna:</label>
                <select name="questionnaire_user_type" id="questionnaire_user_type" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleUserTypeChange()">
                    <option value="all" {{ $selectedUserType == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="alumni" {{ $selectedUserType == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    <option value="company" {{ $selectedUserType == 'company' ? 'selected' : '' }}>Perusahaan</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label for="questionnaire_category" class="block text-sm font-medium text-gray-700 mb-1">Kategori:</label>
                <select name="questionnaire_category" id="questionnaire_category" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleCategoryChange()"
                        {{ !$selectedYear ? 'disabled' : '' }}>
                    <option value="">Pilih Kategori</option>
                    <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>
                        📊 Semua Kategori
                    </option>
                    @foreach($availableCategories as $category)
                        <option value="{{ $category->id_category }}" 
                                {{ $selectedCategory == $category->id_category ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Question Filter -->
            <div class="hidden">
                <label for="questionnaire_question" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan:</label>
                <select name="questionnaire_question" id="questionnaire_question" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleQuestionChange()"
                        {{ !$selectedCategory ? 'disabled' : '' }}>
                    <option value="">Pilih Pertanyaan</option>
                    @if($selectedCategory)
                        <option value="all" {{ $selectedQuestion === 'all' ? 'selected' : '' }}>
                            📊 Semua Pertanyaan
                        </option>
                        @foreach($availableQuestions as $question)
                            <option value="{{ $question->id_question }}" 
                                    {{ $selectedQuestion == $question->id_question ? 'selected' : '' }}>
                                {{ Str::limit($question->question, 50) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Study Program Filter -->
            <div>
                <label for="questionnaire_study_program" class="block text-sm font-medium text-gray-700 mb-1">Program Studi:</label>
                <select name="questionnaire_study_program" id="questionnaire_study_program" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleStudyProgramChangeOnly()">
                    <option value="">Semua Program Studi</option>
                    @if(isset($availableStudyPrograms) && $availableStudyPrograms->count() > 0)
                        @foreach($availableStudyPrograms as $studyProgram)
                            <option value="{{ $studyProgram->id_study }}" 
                                    {{ (isset($selectedStudyProgram) && $selectedStudyProgram == $studyProgram->id_study) ? 'selected' : '' }}>
                                {{ $studyProgram->study_program }}
                            </option>
                        @endforeach
                    @else
                        <option disabled>Tidak ada program studi tersedia</option>
                    @endif
                </select>
            </div>

            <!-- Graduation Year Filter -->
            <div>
                <label for="questionnaire_graduation_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus:</label>
                <select name="questionnaire_graduation_year" id="questionnaire_graduation_year" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                        onchange="handleGraduationYearChange()"
                        {{ !$selectedYear ? 'disabled' : '' }}>
                    <option value="">Semua Tahun Lulus</option>
                    @if(isset($availableGraduationYears) && count($availableGraduationYears) > 0)
                        @foreach($availableGraduationYears as $year)
                            <option value="{{ $year }}" 
                                    {{ (isset($selectedGraduationYear) && $selectedGraduationYear == $year) ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    @else
                        <option disabled>Tidak ada tahun lulus tersedia</option>
                    @endif
                </select>
            </div>
        </form>
    </div>
    </div>
    
    <div class="hidden md:block">
    <!-- Overall Summary -->
                <div class="mt-8 mb-8 bg-orange-50 rounded-lg p-6">
                    <h4 class="font-semibold text-orange-800 mb-4">
                        📈 Ringkasan Keseluruhan
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-900">
                                @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'all_questions_all_categories')
                                    {{ $questionnaireChartData['total_categories'] }}
                                @elseif(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
                                    1
                                @else
                                    {{ isset($questionnaireChartData['total_categories']) ? $questionnaireChartData['total_categories'] : 1 }}
                                @endif
                            </div>
                            <div class="text-xs text-orange-600">Total Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-900">
                                @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'all_questions_all_categories')
                                    {{ $questionnaireChartData['total_questions'] }}
                                @elseif(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
                                    {{ $questionnaireChartData['total_questions'] }}
                                @elseif(isset($questionnaireChartData['question']))
                                    1
                                @else
                                    {{ isset($questionnaireChartData['total_questions']) ? $questionnaireChartData['total_questions'] : 0 }}
                                @endif
                            </div>
                            <div class="text-xs text-orange-600">Total Pertanyaan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-900">
                                @php
                                    $totalResponders = 0;
                                    
                                    if (isset($questionnaireChartData['total_responders'])) {
                                        $totalResponders = $questionnaireChartData['total_responders'];
                                    } elseif (isset($questionnaireChartData['total_responses'])) {
                                        $totalResponders = $questionnaireChartData['total_responses'];
                                    } else {
                                        // Fallback: hitung berdasarkan filter yang dipilih
                                        if ($selectedYear) {
                                            if ($selectedUserType === 'alumni') {
                                                $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                    ->whereNull('nim')
                                                    ->whereYear('created_at', $selectedYear)
                                                    ->distinct('id_user')
                                                    ->count('id_user');
                                            } elseif ($selectedUserType === 'company') {
                                                $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                    ->whereNotNull('nim')
                                                    ->whereYear('created_at', $selectedYear)
                                                    ->distinct('id_user')
                                                    ->count('id_user');
                                            } else {
                                                $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                    ->whereYear('created_at', $selectedYear)
                                                    ->distinct('id_user')
                                                    ->count('id_user');
                                            }
                                            
                                            // Apply study program filter if selected
                                            if ($selectedStudyProgram) {
                                                if ($selectedUserType === 'alumni') {
                                                    $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('user.alumni', function($q) use ($selectedStudyProgram) {
                                                            $q->where('id_study', $selectedStudyProgram);
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                } elseif ($selectedUserType === 'company') {
                                                    $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNotNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('alumniByNim', function($q) use ($selectedStudyProgram) {
                                                            $q->where('id_study', $selectedStudyProgram);
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                } else {
                                                    // For 'all' user type with study program filter
                                                    $alumniCount = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('user.alumni', function($q) use ($selectedStudyProgram) {
                                                            $q->where('id_study', $selectedStudyProgram);
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                    
                                                    $companyCount = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNotNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('alumniByNim', function($q) use ($selectedStudyProgram) {
                                                            $q->where('id_study', $selectedStudyProgram);
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                    
                                                    $totalResponders = $alumniCount + $companyCount;
                                                }
                                            }
                                            
                                            // Apply graduation year filter if selected
                                            if ($selectedGraduationYear) {
                                                if ($selectedUserType === 'alumni') {
                                                    $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('user.alumni', function($q) use ($selectedGraduationYear, $selectedStudyProgram) {
                                                            $q->where('graduation_year', $selectedGraduationYear);
                                                            if ($selectedStudyProgram) {
                                                                $q->where('id_study', $selectedStudyProgram);
                                                            }
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                } elseif ($selectedUserType === 'company') {
                                                    $totalResponders = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNotNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('alumniByNim', function($q) use ($selectedGraduationYear, $selectedStudyProgram) {
                                                            $q->where('graduation_year', $selectedGraduationYear);
                                                            if ($selectedStudyProgram) {
                                                                $q->where('id_study', $selectedStudyProgram);
                                                            }
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                } else {
                                                    // For 'all' user type with graduation year filter
                                                    $alumniCount = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('user.alumni', function($q) use ($selectedGraduationYear, $selectedStudyProgram) {
                                                            $q->where('graduation_year', $selectedGraduationYear);
                                                            if ($selectedStudyProgram) {
                                                                $q->where('id_study', $selectedStudyProgram);
                                                            }
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                    
                                                    $companyCount = \App\Models\Tb_User_Answers::where('status', 'completed')
                                                        ->whereNotNull('nim')
                                                        ->whereYear('created_at', $selectedYear)
                                                        ->whereHas('alumniByNim', function($q) use ($selectedGraduationYear, $selectedStudyProgram) {
                                                            $q->where('graduation_year', $selectedGraduationYear);
                                                            if ($selectedStudyProgram) {
                                                                $q->where('id_study', $selectedStudyProgram);
                                                            }
                                                        })
                                                        ->distinct('id_user')
                                                        ->count('id_user');
                                                    
                                                    $totalResponders = $alumniCount + $companyCount;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                {{ $totalResponders }}
                            </div>
                            <div class="text-xs text-orange-600">Total Responden</div>
                        </div>
                    </div>
                </div>
    <!-- Chart Display -->
    @if(!empty($questionnaireChartData))
        @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'all_questions_all_categories')
            <!-- ✅ Display untuk semua pertanyaan dari semua kategori -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="mb-6">
                    <h3 class="font-semibold text-orange-800 mb-2">
                        📊 Semua Pertanyaan dari Semua Kategori - {{ $questionnaireChartData['period_name'] }}
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-layer-group mr-1"></i>
                            {{ $questionnaireChartData['total_categories'] }} kategori
                        </span>
                        <span>
                            <i class="fas fa-list mr-1"></i>
                            Total: {{ $questionnaireChartData['total_questions'] }} pertanyaan
                        </span>
                        <span>
                            <i class="fas fa-filter mr-1"></i>
                            {{ ucfirst($selectedUserType === 'all' ? 'Semua Pengguna' : $selectedUserType) }}
                        </span>
                    </div>
                </div>
                
                <!-- ✅ GROUPING: Group questions by category -->
                @php
                    $groupedQuestions = collect($questionnaireChartData['questions_data'])->groupBy('category.id_category');
                @endphp
                
                @foreach($groupedQuestions as $categoryId => $categoryQuestions)
                    @php
                        $categoryInfo = $categoryQuestions->first()['category'];
                        
                        $questionIds = $categoryQuestions->pluck('question.id_question')->toArray();
                        
                        $categoryTotalResponses = 0;
                        if (!empty($questionIds)) {
                            $answersQuery = "
                                SELECT COUNT(DISTINCT tua.id_user) as total_responders
                                FROM tb_user_answers tua
                                INNER JOIN tb_user_answer_item tai ON tua.id_user_answer = tai.id_user_answer
                                INNER JOIN tb_user u ON tua.id_user = u.id_user
                                WHERE tai.id_question IN (" . implode(',', array_fill(0, count($questionIds), '?')) . ")
                                AND tua.status = 'completed'
                            ";
                            
                            $queryParams = $questionIds;
                            
                            if ($selectedUserType === 'alumni') {
                                $answersQuery .= " AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user
                                ) AND tua.nim IS NULL";
                            } elseif ($selectedUserType === 'company') {
                                $answersQuery .= " AND EXISTS (
                                    SELECT 1 FROM tb_company 
                                    WHERE tb_company.id_user = u.id_user
                                ) AND tua.nim IS NOT NULL";
                            }
                            
                            $result = DB::select($answersQuery, $queryParams);
                            $categoryTotalResponses = $result[0]->total_responders ?? 0;
                        }
                        
                        $categoryAnsweredQuestions = $categoryQuestions->where('total_responses', '>', 0)->count();
                    @endphp
                    
                    <div class="mb-8 border border-orange-200 rounded-lg p-6">
                        <!-- Category Header -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-100">
                            <div>
                                <h4 class="font-semibold text-orange-800 text-lg">
                                    📋 {{ $categoryInfo->category_name }}
                                </h4>
                                <div class="flex items-center gap-3 text-xs text-gray-600 mt-1">
                                    <span class="bg-orange-100 px-2 py-1 rounded">
                                        {{ $categoryQuestions->count() }} pertanyaan
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $categoryTotalResponses }} total responden
                                    </span>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                        {{ $categoryQuestions->count() > 0 ? round(($categoryAnsweredQuestions / $categoryQuestions->count()) * 100, 1) : 0 }}% terjawab
                                    </span>
                                </div>
                            </div>
                            <button onclick="viewCategoryDetail('{{ $categoryInfo->id_category }}')" 
                                    class="text-xs bg-orange-100 hover:bg-orange-200 text-orange-800 px-3 py-2 rounded-lg transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Lihat Detail Kategori
                            </button>
                        </div>
                        
                        <!-- Questions Grid dalam Kategori -->
                        <div class="grid grid-cols-1 
                            @if($categoryQuestions->count() < 2)
                                lg:grid-cols-1 xl:grid-cols-1
                            @else
                                lg:grid-cols-2 xl:grid-cols-2
                            @endif
                            gap-4">
                            @foreach($categoryQuestions as $index => $qData)
                                @php
                                    $globalIndex = $groupedQuestions->flatten(1)->search($qData);
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 {{ $qData['total_responses'] > 0 ? 'bg-orange-25' : 'bg-gray-50' }}">
                                    <div class="mb-3">
                                        <h5 class="font-medium text-gray-800 mb-2 text-sm">
                                            {{ $loop->iteration }}. {{ Str::limit($qData['question']->question, 60) }}
                                        </h5> 
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <span class="bg-orange-100 px-2 py-1 rounded">
                                                {{ ucfirst($qData['question']->type) }}
                                            </span>
                                            <span>
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $qData['total_responses'] }}
                                            </span>
                                            @if(isset($qData['other_answers']) && array_sum(array_map('count', $qData['other_answers'])) > 0)
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    {{ array_sum(array_map('count', $qData['other_answers'])) }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($qData['total_responses'] > 0)
                                        <!-- Mini Chart -->
                                        <div class="mb-3">
                                            @if(isset($qData['question']->type) && in_array($qData['question']->type, ['multiple']))
                                                <canvas id="miniBarChart{{ $globalIndex }}" width="250" height="540"></canvas>
                                            @else
                                            <canvas id="miniBarChart{{ $globalIndex }}" width="250" height="150"></canvas>
                                            @endif
                                        </div>
                                        
                                        <!-- Top Answer -->
                                        @php
                                            $topAnswer = collect($qData['answer_counts'])->sortByDesc('count')->first();
                                        @endphp
                                        @if($topAnswer && $topAnswer['count'] > 0)
                                            <div class="text-xs text-gray-600 mb-2">
                                                <strong>Terpopuler:</strong> 
                                                <span class="text-orange-800">{{ Str::limit($topAnswer['option_text'], 30) }}</span> 
                                                <span class="bg-orange-100 text-orange-800 px-1 rounded">({{ $topAnswer['count'] }})</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Other Answers Summary -->
                                        @if(isset($qData['other_answers']) && count($qData['other_answers']) > 0)
                                            <div class="mt-2">
                                                <details class="text-xs">
                                                    <summary class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium">
                                                        Jawaban lainnya ({{ array_sum(array_map('count', $qData['other_answers'])) }})
                                                    </summary>
                                                    <div class="mt-2 max-h-24 overflow-y-auto bg-blue-50 rounded p-2 space-y-1">
                                                        @foreach($qData['other_answers'] as $optionId => $answers)
                                                            @if(count($answers) > 0)
                                                                @php
                                                                    $option = $qData['question']->options->where('id_questions_options', $optionId)->first();
                                                                @endphp
                                                                
                                                                <div class="text-blue-800 font-medium">
                                                                    {{ $option->option ?? 'Unknown' }}:
                                                                </div>
                                                                
                                                                @foreach($answers as $answer)
                                                                    <div class="ml-2 text-blue-700">
                                                                        @if($option && !empty($option->other_before_text))
                                                                            <span class="text-gray-600 text-xs">{{ $option->other_before_text }}</span>
                                                                        @endif
                                                                        
                                                                        <span class="font-medium">{{ $answer }}</span>
                                                                        
                                                                        @if($option && !empty($option->other_after_text))
                                                                            <span class="text-gray-600 text-xs">{{ $option->other_after_text }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                        @endif
                                    @else
                                        <!-- No Responses State -->
                                        <div class="text-center py-4">
                                            <div class="text-gray-400 text-lg mb-1">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Belum ada responden
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                
            </div>
        @elseif(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
            <!-- ✅ Display untuk multiple questions dalam satu kategori -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="mb-6">
                    <h3 class="font-semibold text-orange-800 mb-2">
                        📊 Statistik Semua Pertanyaan - {{ $questionnaireChartData['category_name'] }}
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-list mr-1"></i>
                            Total: {{ $questionnaireChartData['total_questions'] }} pertanyaan
                        </span>
                        <span>
                            <i class="fas fa-filter mr-1"></i>
                            {{ ucfirst($selectedUserType === 'all' ? 'Semua Pengguna' : $selectedUserType) }}
                        </span>
                    </div>
                </div>
                
                <!-- Questions Grid -->
                @php
                    $questionsCount = count($questionnaireChartData['questions_data']);
                @endphp
                <div class="grid grid-cols-1 
                            @if($questionsCount < 2)
                                lg:grid-cols-1 xl:grid-cols-1
                            @else
                                lg:grid-cols-2 xl:grid-cols-2
                            @endif
                            gap-4">
                    @foreach($questionnaireChartData['questions_data'] as $index => $qData)
                        <div class="border border-orange-200 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-orange-800 mb-2">
                                    {{ $index + 1 }}. {{ Str::limit($qData['question']->question, 80) }}
                                </h4>
                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                    <span class="bg-orange-100 px-2 py-1 rounded">
                                        {{ ucfirst($qData['question']->type) }}
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $qData['total_responses'] }} responden
                                    </span>
                                    @if(isset($qData['other_answers']) && count($qData['other_answers']) > 0)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            <i class="fas fa-edit mr-1"></i>
                                            {{ array_sum(array_map('count', $qData['other_answers'])) }} jawaban lainnya
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($qData['total_responses'] > 0)
                                <!-- Mini Chart -->
                                <div class="mb-4">
                                    @if(isset($qData['question']->type) && in_array($qData['question']->type, ['multiple']))
                                        <canvas id="miniBarChart{{ $index }}" width="300" height="550"></canvas>
                                    @else
                                    <canvas id="miniBarChart{{ $index }}" width="300" height="200"></canvas>
                                    @endif
                                </div>
                                
                                <!-- Detailed Options Table -->
                                <div class="mt-4">
                                    <h5 class="font-medium text-orange-700 mb-2 text-xs">Detail Jawaban:</h5>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-xs">
                                            <thead class="bg-orange-50">
                                                <tr>
                                                    <th class="px-2 py-1 text-left text-xs font-medium text-orange-900 w-1/2">Pilihan</th>
                                                    <th class="px-2 py-1 text-center text-xs font-medium text-orange-900 w-1/6">Jumlah</th>
                                                    <th class="px-2 py-1 text-center text-xs font-medium text-orange-900 w-1/6">Lainnya</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @if(isset($qData['answer_counts']))
                                                    @foreach($qData['answer_counts'] as $optionId => $data)
                                                        @php
                                                            $count = $data['count'];
                                                            $percentage = $qData['total_responses'] > 0 
                                                                ? round(($count / $qData['total_responses']) * 100, 1) 
                                                                : 0;
                                                            $hasOtherAnswers = isset($qData['other_answers'][$optionId]) && 
                                                                               count($qData['other_answers'][$optionId]) > 0;
                                                        @endphp
                                                        <tr class="{{ $count > 0 ? 'bg-orange-25' : 'bg-gray-50' }}">
                                                            <td class="px-2 py-2 text-xs align-top">
                                                                <div class="break-words">
                                                                    <span class="block leading-relaxed" title="{{ $data['option_text'] }}">
                                                                        {{ $data['option_text'] }}
                                                                    </span>
                                                                    @if($data['is_other'])
                                                                        <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-700 px-1 py-0.5 rounded">Lainnya</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="px-2 py-2 text-xs text-center font-medium {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }} align-top">
                                                                {{ $count }}
                                                            </td>
                                                            <td class="px-2 py-2 text-xs align-top">
                                                                @if($hasOtherAnswers)
                                                                    <details class="cursor-pointer">
                                                                        <summary class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                                                            {{ count($qData['other_answers'][$optionId]) }}
                                                                        </summary>
                                                                        <div class="mt-1 max-h-20 overflow-y-auto bg-gray-50 rounded p-1 text-xs">
                                                                            @php
                                                                                $option = $qData['question']->options->where('id_questions_options', $optionId)->first();
                                                                            @endphp
                                                                            
                                                                            @foreach($qData['other_answers'][$optionId] as $idx => $otherAnswer)
                                                                                <div class="mb-1 p-1 bg-white rounded border text-xs">
                                                                                    <span class="text-gray-600">{{ $idx + 1 }}.</span>
                                                                                    
                                                                                    @if($option && !empty($option->other_before_text))
                                                                                        <span class="text-gray-500 italic">{{ $option->other_before_text }}</span>
                                                                                    @endif
                                                                                    
                                                                                    <span class="text-gray-800 font-medium break-words">{{ $otherAnswer }}</span>
                                                                                    
                                                                                    @if($option && !empty($option->other_after_text))
                                                                                        <span class="text-gray-500 italic">{{ $option->other_after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </details>
                                                                @else
                                                                    @if($data['is_other'])
                                                                        <span class="text-gray-400 text-xs">-</span>
                                                                    @else
                                                                        <span class="text-gray-300">-</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <!-- Fallback jika tidak ada answer_counts -->
                                                    @foreach($qData['labels'] as $idx => $label)
                                                        @php
                                                            $count = $qData['values'][$idx] ?? 0;
                                                            $percentage = $qData['total_responses'] > 0 
                                                                ? round(($count / $qData['total_responses']) * 100, 1) 
                                                                : 0;
                                                        @endphp
                                                        <tr class="{{ $count > 0 ? 'bg-orange-25' : 'bg-gray-50' }}">
                                                            <td class="px-2 py-2 text-xs align-top">
                                                                <div class="break-words">
                                                                    <span class="block leading-relaxed" title="{{ $label }}">
                                                                        {{ $label }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="px-2 py-2 text-xs text-center font-medium {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }} align-top">
                                                                {{ $count }}
                                                            </td>
                                                            <td class="px-2 py-2 text-xs text-gray-300 align-top">-</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-gray-400 text-lg mb-1">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Belum ada responden
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif(isset($questionnaireChartData['question']))
            <!-- ✅ Display untuk single question -->
            @if(isset($questionnaireChartData['error']))
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="text-red-400 mb-4">
                        <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-red-600 mb-2">Error</h3>
                    <p class="text-red-500">{{ $questionnaireChartData['error'] }}</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="mb-4">
                        <h3 class="font-semibold text-orange-800 mb-2">
                            {{ $questionnaireChartData['question']->question }}
                        </h3>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>
                                <i class="fas fa-chart-bar mr-1"></i>
                                Tipe: {{ ucfirst($questionnaireChartData['question']->type) }}
                            </span>
                            <span>
                                <i class="fas fa-users mr-1"></i>
                                Total Responden: {{ $questionnaireChartData['total_responses'] }}
                            </span>
                            <span>
                                <i class="fas fa-filter mr-1"></i>
                                {{ ucfirst($selectedUserType === 'all' ? 'Semua Pengguna' : $selectedUserType) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex justify-center">
                        <canvas id="questionnaireBarChart" height="400" width="600" style="max-width:800px;max-height:400px;"></canvas>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-orange-800 mb-3">Detail Jawaban:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-orange-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-orange-900">Pilihan</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-orange-900">Jumlah</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-orange-900">Persentase</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-orange-900">Jawaban Lainnya</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @if(isset($questionnaireChartData['answer_counts']))
                                        @foreach($questionnaireChartData['answer_counts'] as $optionId => $data)
                                            @php
                                                $count = $data['count'];
                                                $percentage = $questionnaireChartData['total_responses'] > 0 
                                                    ? round(($count / $questionnaireChartData['total_responses']) * 100, 1) 
                                                    : 0;
                                                $hasOtherAnswers = isset($questionnaireChartData['other_answers'][$optionId]) && 
                                                                   count($questionnaireChartData['other_answers'][$optionId]) > 0;
                                            @endphp
                                            <tr class="{{ $count > 0 ? 'bg-orange-25' : 'bg-gray-50' }}">
                                                <td class="px-4 py-2 text-sm">
                                                    {{ $data['option_text'] }}
                                                    @if($data['is_other'])
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Lainnya</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center font-medium {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }}">
                                                    {{ $count }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }}">
                                                    {{ $percentage }}%
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($hasOtherAnswers)
                                                        <details class="cursor-pointer">
                                                            <summary class="text-blue-600 hover:text-blue-800 font-medium">
                                                                Lihat {{ count($questionnaireChartData['other_answers'][$optionId]) }} jawaban
                                                            </summary>
                                                            <div class="mt-2 max-h-32 overflow-y-auto bg-gray-50 rounded p-2 text-xs">
                                                                @php
                                                                    $option = $questionnaireChartData['question']->options->where('id_questions_options', $optionId)->first();
                                                                @endphp
                                                                
                                                                @foreach($questionnaireChartData['other_answers'][$optionId] as $index => $otherAnswer)
                                                                    <div class="mb-1 p-2 bg-white rounded border">
                                                                        <span class="text-gray-600">{{ $index + 1 }}.</span>
                                                                        
                                                                        @if($option && !empty($option->other_before_text))
                                                                            <span class="text-gray-500 italic">{{ $option->other_before_text }}</span>
                                                                        @endif
                                                                        
                                                                        <span class="text-gray-800 font-medium">{{ $otherAnswer }}</span>
                                                                        
                                                                        @if($option && !empty($option->other_after_text))
                                                                            <span class="text-gray-500 italic">{{ $option->other_after_text }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </details>
                                                    @else
                                                        @if($data['is_other'])
                                                            <span class="text-gray-400 text-xs">Tidak ada jawaban</span>
                                                        @else
                                                            <span class="text-gray-300">-</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($questionnaireChartData['labels'] as $index => $label)
                                            @php
                                                $count = $questionnaireChartData['values'][$index];
                                                $percentage = $questionnaireChartData['total_responses'] > 0 
                                                    ? round(($count / $questionnaireChartData['total_responses']) * 100, 1) 
                                                    : 0;
                                            @endphp
                                            <tr class="{{ $count > 0 ? 'bg-orange-25' : 'bg-gray-50' }}">
                                                <td class="px-4 py-2 text-sm">{{ $label }}</td>
                                                <td class="px-4 py-2 text-sm text-center font-medium {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }}">
                                                    {{ $count }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center {{ $count > 0 ? 'text-orange-900' : 'text-gray-500' }}">
                                                    {{ $percentage }}%
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @elseif($selectedQuestion)
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-chart-line text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak Ada Data</h3>
            <p class="text-gray-500">Belum ada responden yang menjawab pertanyaan ini.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-orange-400 mb-4">
                <i class="fas fa-filter text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-orange-600 mb-2">Pilih Filter</h3>
            <p class="text-orange-500">Silakan pilih periode, kategori, dan pertanyaan untuk melihat statistik.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($selectedCategory && !request('questionnaire_question'))
        setTimeout(() => {
            const questionSelect = document.getElementById('questionnaire_question');
            if (questionSelect && questionSelect.value === 'all') {
                document.getElementById('questionnaire-filter-form').submit();
            }
        }, 100);
    @endif

    @if(!empty($questionnaireChartData))
        @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'all_questions_all_categories')
            const allQuestionsData = @json($questionnaireChartData['questions_data']);
            
            const colors = [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981'
            ];
            
            allQuestionsData.forEach((qData, index) => {
                if (qData.total_responses > 0) {
                    const questionType = qData.question.type;
                    const chartLabels = qData.labels;
                    const chartValues = qData.values;
                    
                    // Changed: Use bar chart for all types including scale and rating
                    const chartCtx = document.getElementById(`miniBarChart${index}`);
                    if (chartCtx) {
                        new Chart(chartCtx.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: chartLabels,
                                datasets: [{
                                    label: 'Responden',
                                    data: chartValues,
                                    backgroundColor: colors.slice(0, chartLabels.length).map(color => color + '80'),
                                    borderColor: colors.slice(0, chartLabels.length),
                                    borderWidth: 1,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { 
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                                return context.parsed.y + ' responden (' + percentage + '%)';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { beginAtZero: true, ticks: { precision: 0 } },
                                    x: { ticks: { maxRotation: 45, minRotation: 0 } }
                                }
                            }
                        });
                    }
                }
            });
        @elseif(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
            const questionsData = @json($questionnaireChartData['questions_data']);
            
            const colors = [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981'
            ];
            
            questionsData.forEach((qData, index) => {
                const questionType = qData.question.type;
                const chartLabels = qData.labels;
                const chartValues = qData.values;
                
                // Changed: Use bar chart for all types including scale and rating
                const chartCtx = document.getElementById(`miniBarChart${index}`);
                if (chartCtx) {
                    new Chart(chartCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Responden',
                                data: chartValues,
                                backgroundColor: colors.slice(0, chartLabels.length).map(color => color + '80'),
                                borderColor: colors.slice(0, chartLabels.length),
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                            return context.parsed.y + ' responden (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { ticks: { maxRotation: 45, minRotation: 0 } }
                            }
                        }
                    });
                }
            });
        @elseif(isset($questionnaireChartData['question']))
            const questionType = '{{ $questionnaireChartData['question']->type }}';
            const chartLabels = @json($questionnaireChartData['labels']);
            const chartValues = @json($questionnaireChartData['values']);
            
            const colors = [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981'
            ];
            
            // Changed: Use bar chart for all types including scale and rating
            const barCtx = document.getElementById('questionnaireBarChart');
            if (barCtx) {
                new Chart(barCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Responden',
                            data: chartValues,
                            backgroundColor: colors.slice(0, chartLabels.length).map(color => color + '80'),
                            borderColor: colors.slice(0, chartLabels.length),
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                        return context.parsed.y + ' responden (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { precision: 0 },
                                title: {
                                    display: true,
                                    text: 'Jumlah Responden'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Pilihan Jawaban'
                                }
                            }
                        }
                    });
                }
            }
        @endif
    @endif
});

function handleYearChange() {
    document.getElementById('questionnaire_category').value = '';
    document.getElementById('questionnaire_question').value = '';
    document.getElementById('questionnaire_graduation_year').value = '';
    document.getElementById('questionnaire-filter-form').submit();
}

function handleUserTypeChange() {
    document.getElementById('questionnaire_category').value = '';
    document.getElementById('questionnaire_question').value = '';
    document.getElementById('questionnaire_graduation_year').value = '';
    document.getElementById('questionnaire-filter-form').submit();
}

function handleCategoryChange() {
    const questionSelect = document.getElementById('questionnaire_question');
    const questionOptions = questionSelect.querySelectorAll('option[value="all"]');
    
    if (questionOptions.length > 0) {
        questionSelect.value = 'all';
    } else {
        questionSelect.value = '';
    }
    
    document.getElementById('questionnaire-filter-form').submit();
}
function handleStudyProgramChange() {
    document.getElementById('questionnaire_category').value = '';
    document.getElementById('questionnaire_question').value = '';
    
    document.getElementById('questionnaire-filter-form').submit();
}
function handleGraduationYearChange() {
    // Hanya submit tanpa reset apapun - untuk perubahan graduation year saja
    document.getElementById('questionnaire-filter-form').submit();
}
function handleStudyProgramChangeOnly() {
    // Hanya submit tanpa reset apapun - untuk perubahan program studi saja
    document.getElementById('questionnaire-filter-form').submit();
}
function viewQuestionDetail(questionId) {
    document.getElementById('questionnaire_question').value = questionId;
    document.getElementById('questionnaire-filter-form').submit();
}

function viewCategoryDetail(categoryId) {
    document.getElementById('questionnaire_category').value = categoryId;
    document.getElementById('questionnaire_question').value = 'all';
    document.getElementById('questionnaire-filter-form').submit();
}
</script>
@endpush