<div class="bg-purple-100 p-6 rounded-2xl shadow">
    <div class="font-bold mb-6 text-xl text-purple-900">Statistik Kuesioner</div>
    
    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-purple-800 mb-4">Filter Statistik</h3>
        <form method="GET" id="questionnaire-filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
            
            <!-- Periode Filter -->
            <div>
                <label for="questionnaire_periode" class="block text-sm font-medium text-gray-700 mb-1">Periode:</label>
                <select name="questionnaire_periode" id="questionnaire_periode" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-purple-500 focus:border-purple-500"
                        onchange="handlePeriodeChange()">
                    <option value="">Pilih Periode</option>
                    @foreach($availablePeriodes as $periode)
                        <option value="{{ $periode->id_periode }}" 
                                {{ $selectedPeriode == $periode->id_periode ? 'selected' : '' }}>
                            {{ $periode->start_date->format('Y') }} - {{ $periode->end_date->format('Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- User Type Filter -->
            <div>
                <label for="questionnaire_user_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pengguna:</label>
                <select name="questionnaire_user_type" id="questionnaire_user_type" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-purple-500 focus:border-purple-500"
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
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-purple-500 focus:border-purple-500"
                        onchange="handleCategoryChange()"
                        {{ !$selectedPeriode ? 'disabled' : '' }}>
                    <option value="">Pilih Kategori</option>
                    @foreach($availableCategories as $category)
                        <option value="{{ $category->id_category }}" 
                                {{ $selectedCategory == $category->id_category ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Question Filter -->
            <div>
                <label for="questionnaire_question" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan:</label>
                <select name="questionnaire_question" id="questionnaire_question" 
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()"
                        {{ !$selectedCategory ? 'disabled' : '' }}>
                    <option value="">Pilih Pertanyaan</option>
                    <!-- ✅ MODIFIKASI: Option "Semua Pertanyaan" sebagai default -->
                    @if($availableQuestions->count() > 0)
                        <option value="all" {{ $selectedQuestion == 'all' || ($selectedCategory && !$selectedQuestion) ? 'selected' : '' }}>
                            📊 Semua Pertanyaan ({{ $availableQuestions->count() }})
                        </option>
                        @if($availableQuestions->count() > 1)
                            <option disabled>─────────────────</option>
                        @endif
                    @endif
                </select>
            </div>
        </form>
    </div>
    
    <!-- Chart Display -->
    @if(!empty($questionnaireChartData))
        @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
            <!-- ✅ TAMBAHAN: Display untuk multiple questions -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="mb-6">
                    <h3 class="font-semibold text-purple-800 mb-2">
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($questionnaireChartData['questions_data'] as $index => $qData)
                        <div class="border border-purple-200 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-purple-800 mb-2">
                                    {{ $index + 1 }}. {{ Str::limit($qData['question']->question, 80) }}
                                </h4>
                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                    <span class="bg-purple-100 px-2 py-1 rounded">
                                        {{ ucfirst($qData['question']->type) }}
                                    </span>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $qData['total_responses'] }} responden
                                    </span>
                                    <!-- ✅ TAMBAHAN: Show other answers count -->
                                    @if(isset($qData['other_answers']) && count($qData['other_answers']) > 0)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            <i class="fas fa-edit mr-1"></i>
                                            {{ array_sum(array_map('count', $qData['other_answers'])) }} jawaban lainnya
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Mini Chart -->
                            <div class="mb-4">
                                @if($qData['question']->type === 'scale' || $qData['question']->type === 'rating')
                                    <canvas id="miniLineChart{{ $index }}" width="300" height="200"></canvas>
                                @else
                                    <canvas id="miniBarChart{{ $index }}" width="300" height="200"></canvas>
                                @endif
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="space-y-1">
                                @php
                                    $topAnswer = collect($qData['answer_counts'])->sortByDesc('count')->first();
                                @endphp
                                @if($topAnswer && $topAnswer['count'] > 0)
                                    <div class="text-xs text-gray-600">
                                        <strong>Terpopuler:</strong> {{ $topAnswer['option_text'] }} ({{ $topAnswer['count'] }})
                                    </div>
                                @endif
                                
                                <!-- ✅ TAMBAHAN: Show other answers for this question -->
                                @if(isset($qData['other_answers']) && count($qData['other_answers']) > 0)
                                    <div class="mt-2">
                                        <details class="text-xs">
                                            <summary class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium">
                                                Lihat jawaban lainnya ({{ array_sum(array_map('count', $qData['other_answers'])) }})
                                            </summary>
                                            <div class="mt-2 max-h-24 overflow-y-auto bg-blue-50 rounded p-2 space-y-1">
                                                @foreach($qData['other_answers'] as $optionId => $answers)
                                                    @if(count($answers) > 0)
                                                        @php
                                                            $option = $qData['question']->options->where('id_questions_options', $optionId)->first();
                                                        @endphp
                                                        <div class="text-blue-800 font-medium">{{ $option->option ?? 'Unknown' }}:</div>
                                                        @foreach($answers as $answer)
                                                            <div class="ml-2 text-blue-700">• {{ $answer }}</div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </div>
                                        </details>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Summary Table -->
                <div class="mt-8">
                    <h4 class="font-semibold text-purple-800 mb-4">📋 Ringkasan Data</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg text-sm">
                            <thead class="bg-purple-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-purple-900">No.</th>
                                    <th class="px-4 py-2 text-left text-purple-900">Pertanyaan</th>
                                    <th class="px-4 py-2 text-center text-purple-900">Tipe</th>
                                    <th class="px-4 py-2 text-center text-purple-900">Responden</th>
                                    <th class="px-4 py-2 text-center text-purple-900">Jawaban Terpopuler</th>
                                    <!-- ✅ TAMBAHAN: Kolom other answers -->
                                    <th class="px-4 py-2 text-center text-purple-900">Jawaban Lainnya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($questionnaireChartData['questions_data'] as $index => $qData)
                                    @php
                                        $topAnswer = collect($qData['answer_counts'])->sortByDesc('count')->first();
                                        $totalOtherAnswers = isset($qData['other_answers']) 
                                            ? array_sum(array_map('count', $qData['other_answers'])) 
                                            : 0;
                                    @endphp
                                    <tr class="hover:bg-purple-25">
                                        <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2">
                                            <div class="font-medium">{{ Str::limit($qData['question']->question, 60) }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                                {{ ucfirst($qData['question']->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center font-medium">{{ $qData['total_responses'] }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($topAnswer && $topAnswer['count'] > 0)
                                                <div class="text-purple-900 font-medium">{{ $topAnswer['option_text'] }}</div>
                                                <div class="text-xs text-gray-600">({{ $topAnswer['count'] }} pilihan)</div>
                                            @else
                                                <span class="text-gray-400">Tidak ada jawaban</span>
                                            @endif
                                        </td>
                                        <!-- ✅ TAMBAHAN: Kolom other answers -->
                                        <td class="px-4 py-2 text-center">
                                            @if($totalOtherAnswers > 0)
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                    {{ $totalOtherAnswers }} jawaban
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif(isset($questionnaireChartData['question']))
            <!-- ✅ EXISTING: Display untuk single question -->
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
                        <h3 class="font-semibold text-purple-800 mb-2">
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
                        @if($questionnaireChartData['question']->type === 'scale' || $questionnaireChartData['question']->type === 'rating')
                            <!-- Line Chart for Scale/Rating -->
                            <canvas id="questionnaireLineChart" height="400" width="600" style="max-width:800px;max-height:400px;"></canvas>
                        @else
                            <!-- Bar Chart for Option/Multiple -->
                            <canvas id="questionnaireBarChart" height="400" width="600" style="max-width:800px;max-height:400px;"></canvas>
                        @endif
                    </div>
                    
                    <!-- Data Table -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-purple-800 mb-3">Detail Jawaban:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-purple-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-purple-900">Pilihan</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-purple-900">Jumlah</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-purple-900">Persentase</th>
                                        <!-- ✅ TAMBAHAN: Kolom untuk other answers -->
                                        <th class="px-4 py-2 text-center text-sm font-medium text-purple-900">Jawaban Lainnya</th>
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
                                            <tr class="{{ $count > 0 ? 'bg-purple-25' : 'bg-gray-50' }}">
                                                <td class="px-4 py-2 text-sm">
                                                    {{ $data['option_text'] }}
                                                    @if($data['is_other'])
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Lainnya</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center font-medium {{ $count > 0 ? 'text-purple-900' : 'text-gray-500' }}">
                                                    {{ $count }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center {{ $count > 0 ? 'text-purple-900' : 'text-gray-500' }}">
                                                    {{ $percentage }}%
                                                </td>
                                                <!-- ✅ TAMBAHAN: Kolom other answers -->
                                                <td class="px-4 py-2 text-sm">
                                                    @if($hasOtherAnswers)
                                                        <details class="cursor-pointer">
                                                            <summary class="text-blue-600 hover:text-blue-800 font-medium">
                                                                Lihat {{ count($questionnaireChartData['other_answers'][$optionId]) }} jawaban
                                                            </summary>
                                                            <div class="mt-2 max-h-32 overflow-y-auto bg-gray-50 rounded p-2 text-xs">
                                                                @foreach($questionnaireChartData['other_answers'][$optionId] as $index => $otherAnswer)
                                                                    <div class="mb-1 p-1 bg-white rounded border">
                                                                        <span class="text-gray-600">{{ $index + 1 }}.</span>
                                                                        <span class="text-gray-800">{{ $otherAnswer }}</span>
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
                                        <!-- Fallback untuk format lama -->
                                        @foreach($questionnaireChartData['labels'] as $index => $label)
                                            @php
                                                $count = $questionnaireChartData['values'][$index];
                                                $percentage = $questionnaireChartData['total_responses'] > 0 
                                                    ? round(($count / $questionnaireChartData['total_responses']) * 100, 1) 
                                                    : 0;
                                            @endphp
                                            <tr class="{{ $count > 0 ? 'bg-purple-25' : 'bg-gray-50' }}">
                                                <td class="px-4 py-2 text-sm">{{ $label }}</td>
                                                <td class="px-4 py-2 text-sm text-center font-medium {{ $count > 0 ? 'text-purple-900' : 'text-gray-500' }}">
                                                    {{ $count }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-center {{ $count > 0 ? 'text-purple-900' : 'text-gray-500' }}">
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
            <div class="text-purple-400 mb-4">
                <i class="fas fa-filter text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-purple-600 mb-2">Pilih Filter</h3>
            <p class="text-purple-500">Silakan pilih periode, kategori, dan pertanyaan untuk melihat statistik.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ TAMBAHAN: Auto-submit form saat kategori berubah untuk load default "all"
    @if($selectedCategory && !request('questionnaire_question'))
        // Auto submit form untuk load default "all" questions
        setTimeout(() => {
            const questionSelect = document.getElementById('questionnaire_question');
            if (questionSelect && questionSelect.value === 'all') {
                document.getElementById('questionnaire-filter-form').submit();
            }
        }, 100);
    @endif

    @if(!empty($questionnaireChartData))
        @if(isset($questionnaireChartData['type']) && $questionnaireChartData['type'] === 'multiple')
            // Handle multiple questions charts
            const questionsData = @json($questionnaireChartData['questions_data']);
            
            // Generate colors for charts
            const colors = [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981'
            ];
            
            questionsData.forEach((qData, index) => {
                const questionType = qData.question.type;
                const chartLabels = qData.labels;
                const chartValues = qData.values;
                
                if (questionType === 'scale' || questionType === 'rating') {
                    // Line Chart for Scale/Rating
                    const lineCtx = document.getElementById(`miniLineChart${index}`);
                    if (lineCtx) {
                        new Chart(lineCtx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: chartLabels,
                                datasets: [{
                                    label: 'Responden',
                                    data: chartValues,
                                    borderColor: colors[index % colors.length],
                                    backgroundColor: colors[index % colors.length] + '20',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: colors[index % colors.length],
                                    pointRadius: 3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { precision: 0 } }
                                }
                            }
                        });
                    }
                } else {
                    // Bar Chart for Option/Multiple
                    const barCtx = document.getElementById(`miniBarChart${index}`);
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
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { precision: 0 } },
                                    x: { ticks: { maxRotation: 45, minRotation: 0 } }
                                }
                            }
                        });
                    }
                }
            });
        @elseif(isset($questionnaireChartData['question']))
            // Handle single question chart (existing code)
            const questionType = '{{ $questionnaireChartData['question']->type }}';
            const chartLabels = @json($questionnaireChartData['labels']);
            const chartValues = @json($questionnaireChartData['values']);
            
            // Generate colors for chart
            const colors = [
                '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b',
                '#ef4444', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981'
            ];
            
            if (questionType === 'scale' || questionType === 'rating') {
                // Line Chart for Scale/Rating
                const lineCtx = document.getElementById('questionnaireLineChart');
                if (lineCtx) {
                    new Chart(lineCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Jumlah Responden',
                                data: chartValues,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#8b5cf6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { 
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + ' responden';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    ticks: { precision: 0 }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Pilihan Jawaban'
                                    }
                                }
                            }
                        }
                    });
                }
            } else {
                // Bar Chart for Option/Multiple
                const barCtx = document.getElementById('questionnaireBarChart');
                if (barCtx) {
                    new Chart(barCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Jumlah Responden',
                                data: chartValues,
                                backgroundColor: colors.slice(0, chartLabels.length),
                                borderColor: colors.slice(0, chartLabels.length).map(color => color + 'dd'),
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
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
                                    ticks: { precision: 0 }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                }
                            }
                        });
                    }
                }
            }
        @endif
    @endif
});

// ✅ MODIFIKASI: Filter change handlers dengan auto-select "all"
function handlePeriodeChange() {
    // Reset dependent filters
    document.getElementById('questionnaire_category').value = '';
    document.getElementById('questionnaire_question').value = '';
    
    // Submit form to reload categories
    document.getElementById('questionnaire-filter-form').submit();
}

function handleUserTypeChange() {
    // Reset dependent filters
    document.getElementById('questionnaire_category').value = '';
    document.getElementById('questionnaire_question').value = '';
    
    // Submit form to reload categories
    document.getElementById('questionnaire-filter-form').submit();
}

function handleCategoryChange() {
    // Reset question filter ke "all" sebagai default
    const questionSelect = document.getElementById('questionnaire_question');
    const questionOptions = questionSelect.querySelectorAll('option[value="all"]');
    
    if (questionOptions.length > 0) {
        questionSelect.value = 'all';
    } else {
        questionSelect.value = '';
    }
    
    // Submit form to reload questions
    document.getElementById('questionnaire-filter-form').submit();
}
</script>
@endpush