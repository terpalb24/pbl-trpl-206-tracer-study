<?php

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\AdminController;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Admin\QuestionnaireImportController;
use App\Http\Controllers\Admin\EksportJobhistory;
use App\Http\Controllers\Alumni\JobHistoryController; // Tambahkan ini di atas
use App\Http\Controllers\Alumni\QuestionnaireController as AlumniQuestionnaireController;
use App\Http\Controllers\Company\QuestionnaireController as CompanyQuestionnaireController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/about', fn () => view('about'))->name('about');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout'); 

// Password Change (for all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'ChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePasswordAll'])->name('password.update');
});

// Forgot Password
Route::middleware('guest')->group(function() {
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkCustom'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password-confirmation', [AuthController::class, 'resetPasswordCustom'])->name('password.reset.update');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes 
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', CheckRole::class . ':1'])->get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.admin');
Route::middleware(['auth:web', CheckRole::class . ':2'])->get('/alumni/dashboard', [AlumniController::class, 'dashboard'])->name('dashboard.alumni');
Route::middleware(['auth:web', CheckRole::class . ':3'])->get('/company/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard.company');


/*
|--------------------------------------------------------------------------
| Alumni Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', CheckRole::class . ':2'])->group(function () {
    // Profile management
    Route::get('/alumni/profil', [AlumniController::class, 'edit'])->name('alumni.edit');
    Route::put('/alumni/profil', [AlumniController::class, 'update'])->name('alumni.update');
    
    // JobHistory routes 
    Route::get('/alumni/jobhistory', [JobHistoryController::class, 'index'])->name('alumni.job-history.index');
    Route::get('/alumni/jobhistory/create', [JobHistoryController::class, 'create'])->name('alumni.job-history.create');
    Route::post('/alumni/jobhistory', [JobHistoryController::class, 'store'])->name('alumni.job-history.store');
    Route::get('/alumni/jobhistory/{jobHistory}/edit', [JobHistoryController::class, 'edit'])->name('alumni.job-history.edit');
    Route::put('/alumni/jobhistory/{jobHistory}', [JobHistoryController::class, 'update'])->name('alumni.job-history.update');
    Route::delete('/alumni/jobhistory/{jobHistory}', [JobHistoryController::class, 'destroy'])->name('alumni.job-history.destroy');

    // Questionnaire routes
    Route::get('/alumni/questionnaire', [AlumniQuestionnaireController::class, 'index'])->name('alumni.questionnaire.index');
    Route::get('/alumni/questionnaire/fill/{id_periode}/{category?}', [AlumniQuestionnaireController::class, 'fill'])->name('alumni.questionnaire.fill');
    Route::post('/alumni/questionnaire/submit/{id_periode}', [AlumniQuestionnaireController::class, 'submit'])->name('alumni.questionnaire.submit');
    Route::get('/alumni/questionnaire/thank-you', [AlumniQuestionnaireController::class, 'thankYou'])->name('alumni.questionnaire.thank-you');
    Route::get('/alumni/questionnaire/results', [AlumniQuestionnaireController::class, 'results'])->name('alumni.questionnaire.results');
    Route::get('/alumni/questionnaire/{id_periode}/response/{id_user_answer}', [AlumniQuestionnaireController::class, 'responseDetail'])->name('alumni.questionnaire.response-detail');
    
    // Location API routes for alumni questionnaire
    Route::get('/alumni/questionnaire/provinces', [AlumniQuestionnaireController::class, 'getProvinces'])->name('alumni.questionnaire.provinces');
    Route::get('/alumni/questionnaire/cities/{provinceId}', [AlumniQuestionnaireController::class, 'getCities'])->name('alumni.questionnaire.cities');

    // Email verification & password update for alumni
    Route::get('/alumni/verify-email', [AlumniController::class, 'showEmailForm'])->name('alumni.email.form');
    Route::post('/alumni/verify-email', [AlumniController::class, 'sendEmailVerification'])->name('alumni.verify.email');
    Route::get('/alumni/verify-email/{token}', [AlumniController::class, 'showChangePasswordForm'])->name('alumni.change_password');
    Route::post('/alumni/update-password', [AlumniController::class, 'updatePassword'])->name('alumni.password.update');
});

/*
|--------------------------------------------------------------------------
| Company Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', CheckRole::class . ':3'])->group(function () {
    // Profile management
    Route::get('/company/profil', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/profil', [CompanyController::class, 'update'])->name('company.update');
    
    // Questionnaire routes - DIUBAH: Tambah route baru untuk pemilihan alumni
    Route::get('/company/questionnaire', [CompanyQuestionnaireController::class, 'index'])->name('company.questionnaire.index');
    Route::get('/company/questionnaire/{id_periode}/select-alumni', [CompanyQuestionnaireController::class, 'selectAlumni'])->name('company.questionnaire.select-alumni');
    Route::get('/company/questionnaire/fill/{id_periode}/{nim}/{category?}', [CompanyQuestionnaireController::class, 'fill'])->name('company.questionnaire.fill');
    Route::post('/company/questionnaire/submit/{id_periode}/{nim}', [CompanyQuestionnaireController::class, 'submit'])->name('company.questionnaire.submit');
    Route::get('/company/questionnaire/thank-you', [CompanyQuestionnaireController::class, 'thankYou'])->name('company.questionnaire.thank-you');
    Route::get('/company/questionnaire/results', [CompanyQuestionnaireController::class, 'results'])->name('company.questionnaire.results');
    Route::get('/company/questionnaire/{id_periode}/response/{id_user_answer}', [CompanyQuestionnaireController::class, 'responseDetail'])->name('company.questionnaire.response-detail');
    
    // Location API routes for company questionnaire
    Route::get('/company/questionnaire/provinces', [CompanyQuestionnaireController::class, 'getProvinces'])->name('company.questionnaire.provinces');
    Route::get('/company/questionnaire/cities/{provinceId}', [CompanyQuestionnaireController::class, 'getCities'])->name('company.questionnaire.cities');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', CheckRole::class . ':1'])->group(function () {
    // Alumni Management
    Route::get('/admin/alumni', [AdminController::class, 'alumniIndex'])->name('admin.alumni.index');
    Route::get('/admin/alumni/create', [AdminController::class, 'alumniCreate'])->name('admin.alumni.create');
    Route::post('/admin/alumni/store', [AdminController::class, 'alumniStore'])->name('admin.alumni.store');
    Route::get('/admin/alumni/{nim}/edit', [AdminController::class, 'alumniEdit'])->name('admin.alumni.edit');
    Route::put('/admin/alumni/{nim}/update', [AdminController::class, 'alumniUpdate'])->name('admin.alumni.update');
    // Bulk delete harus didefinisikan SEBELUM route dengan parameter {id_user}
    Route::delete('/admin/alumni/bulk-delete', [AdminController::class, 'bulkDeleteAlumni'])->name('admin.alumni.bulk-delete');
    Route::post('/admin/alumni/bulk-delete', [AdminController::class, 'bulkDeleteAlumni'])->name('admin.alumni.bulk-delete.post');
    Route::delete('/admin/alumni/{id_user}', [AdminController::class, 'alumniDestroy'])->name('admin.alumni.destroy');
    Route::post('/admin/alumni/import', [AdminController::class, 'import'])->name('admin.alumni.import');
    Route::get('/admin/alumni/export', [AdminController::class, 'export'])->name('admin.alumni.export');
    Route::get('/admin/alumni/template', [AdminController::class, 'alumniTemplate'])->name('admin.alumni.template');
    
    // Company Management
    Route::get('/admin/company', [AdminController::class, 'companyIndex'])->name('admin.company.index');
    Route::get('/admin/company/create', [AdminController::class, 'companyCreate'])->name('admin.company.create');
    Route::post('/admin/company', [AdminController::class, 'companyStore'])->name('admin.company.store');
    Route::get('/admin/company/{id_company}/edit', [AdminController::class, 'companyEdit'])->name('admin.company.edit');
    Route::put('/admin/company/{id_company}', [AdminController::class, 'companyUpdate'])->name('admin.company.update');
    // Bulk delete harus didefinisikan SEBELUM route dengan parameter {id_user}
    Route::delete('/admin/company/bulk-delete', [AdminController::class, 'bulkDeleteCompany'])->name('admin.company.bulk-delete');
    Route::post('/admin/company/bulk-delete', [AdminController::class, 'bulkDeleteCompany'])->name('admin.company.bulk-delete.post');
    Route::delete('/admin/company/delete-all', [AdminController::class, 'bulkDeleteCompany'])->name('admin.company.delete-all');
    Route::post('/admin/company/delete-all', [AdminController::class, 'bulkDeleteCompany'])->name('admin.company.delete-all.post');
    Route::delete('/admin/company/{id_user}', [AdminController::class, 'companyDestroy'])->name('admin.company.destroy');
    Route::post('/admin/company/import', [AdminController::class, 'companyImport'])->name('admin.company.import');
    Route::get('/admin/company/export', [AdminController::class, 'companyExport'])->name('admin.company.export');
    Route::get('/admin/company/template', [AdminController::class, 'companyTemplate'])->name('admin.company.template');
    
    // Questionnaire Management
    Route::get('/admin/questionnaire', [QuestionnaireController::class, 'index'])->name('admin.questionnaire.index');
    Route::get('/admin/questionnaire/create', [QuestionnaireController::class, 'create'])->name('admin.questionnaire.create');
    Route::post('/admin/questionnaire', [QuestionnaireController::class, 'store'])->name('admin.questionnaire.store');
    Route::get('/admin/questionnaire/{id_periode}', [QuestionnaireController::class, 'show'])->name('admin.questionnaire.show');
    Route::get('/admin/questionnaire/{id_periode}/edit', [QuestionnaireController::class, 'edit'])->name('admin.questionnaire.edit');
    Route::put('/admin/questionnaire/{id_periode}', [QuestionnaireController::class, 'update'])->name('admin.questionnaire.update');
    Route::delete('/admin/questionnaire/{id_periode}', [QuestionnaireController::class, 'destroy'])->name('admin.questionnaire.destroy');
    Route::patch('/admin/questionnaire/{id_periode}/question/{id_question}/toggle-status', 
        [QuestionnaireController::class, 'toggleQuestionStatus'])
        ->name('admin.questionnaire.question.toggle-status');

    // Category Management
    Route::get('/admin/questionnaire/{id_periode}/category/create', [QuestionnaireController::class, 'createCategory'])->name('admin.questionnaire.category.create');
    Route::post('/admin/questionnaire/{id_periode}/category', [QuestionnaireController::class, 'storeCategory'])->name('admin.questionnaire.category.store');
    Route::get('/admin/questionnaire/{id_periode}/category/{id_category}/edit', [QuestionnaireController::class, 'editCategory'])->name('admin.questionnaire.category.edit');
    Route::put('/admin/questionnaire/{id_periode}/category/{id_category}', [QuestionnaireController::class, 'updateCategory'])->name('admin.questionnaire.category.update');
    Route::delete('/admin/questionnaire/{id_periode}/category/{id_category}', [QuestionnaireController::class, 'destroyCategory'])->name('admin.questionnaire.category.destroy');
    
    // Question Management
    Route::get('/admin/questionnaire/{id_periode}/category/{id_category}/question/create', [QuestionnaireController::class, 'createQuestion'])->name('admin.questionnaire.question.create');
    Route::post('/admin/questionnaire/{id_periode}/category/{id_category}/question', [QuestionnaireController::class, 'storeQuestion'])->name('admin.questionnaire.question.store');
    Route::get('/admin/questionnaire/{id_periode}/category/{id_category}/question/{id_question}/edit', [QuestionnaireController::class, 'editQuestion'])->name('admin.questionnaire.question.edit');
    Route::put('/admin/questionnaire/{id_periode}/category/{id_category}/question/{id_question}', [QuestionnaireController::class, 'updateQuestion'])->name('admin.questionnaire.question.update');
    Route::delete('/admin/questionnaire/{id_periode}/question/{id_question}', [QuestionnaireController::class, 'destroyQuestion'])->name('admin.questionnaire.question.destroy');
    
    // Question Options
    Route::get('/admin/questionnaire/question/{id}/options', [QuestionnaireController::class, 'getQuestionOptions'])->name('admin.questionnaire.question.options');
    
    // Responses
    Route::get('/admin/questionnaire/{id_periode}/responses', [QuestionnaireController::class, 'responses'])->name('admin.questionnaire.responses');
    Route::get('/admin/questionnaire/{id_periode}/responses/{id_user_answer}', [QuestionnaireController::class, 'responseDetail'])->name('admin.questionnaire.response-detail');
    Route::post('/admin/questionnaire/{id_periode}/remind-all', [QuestionnaireController::class, 'remindAllUsers'])->name('admin.questionnaire.remind-all');
    
    // Export
    Route::get('/admin/questionnaire/{id_periode}/export', [QuestionnaireController::class, 'export'])->name('admin.questionnaire.export');
    // Import
    Route::get('/admin/questionnaire/import', [QuestionnaireImportController::class, 'showImportForm'])->name('admin.questionnaire.import.form');
    
    // Location API
    Route::get('/admin/questionnaire/provinces', [QuestionnaireController::class, 'getProvinces'])->name('admin.questionnaire.provinces');
    Route::get('/admin/questionnaire/cities/{provinceId}', [QuestionnaireController::class, 'getCities'])->name('admin.questionnaire.cities');

    // Questionnaire Import/Export (DYNAMIC)
    Route::get('/admin/questionnaires/import-export', [\App\Http\Controllers\Admin\QuestionnaireImportExportController::class, 'index'])->name('admin.questionnaires.import-export');
    Route::post('/admin/questionnaires/import', [\App\Http\Controllers\Admin\QuestionnaireImportExportController::class, 'import'])->name('admin.questionnaires.import');
    Route::get('/admin/questionnaires/export', [\App\Http\Controllers\Admin\QuestionnaireImportExportController::class, 'export'])->name('admin.questionnaires.export');
    Route::get('/admin/questionnaires/download-template', [\App\Http\Controllers\Admin\QuestionnaireImportExportController::class, 'downloadTemplate'])->name('admin.questionnaires.download-template');
    Route::post('/admin/questionnaire/{id_periode}/complete-drafts', [QuestionnaireController::class, 'completeDraftAnswers'])
    ->name('admin.questionnaire.complete-drafts');
    Route::get('/admin/questionnaire/{id_periode}/export-responden', [\App\Http\Controllers\Admin\EksportRespondenController::class, 'export'])->name('admin.export-responden');

    // Job History Export
    Route::get('/export/job-history', [EksportJobhistory::class, 'exportJobHistory'])->name('admin.export.job-history');
});
//ROUTE UNTUK ADMIN NAMBAHIN study Program\
Route::post('/admin/study-program', [AdminController::class, 'storeStudyProgram'])->name('admin.study-program.store');
//route untuk menghapus study program
Route::delete('/admin/study-program/delete-by-select', [AdminController::class, 'deleteStudyProgramBySelect'])->name('admin.study-program.deleteBySelect');

Route::put('/admin/study-program/update', [AdminController::class, 'updateStudyProgram'])
    ->name('admin.study-program.update');
// Tambahkan route ini di routes/web.php untuk debugging

Route::get('/admin/debug/company-answers', [AdminController::class, 'debugCompanyAnswers'])
    ->middleware(['auth']) // Hapus 'admin' middleware jika tidak ada
    ->name('admin.debug.company-answers');

// Atau jika Anda memiliki middleware custom untuk admin, gunakan yang benar
// Route::get('/admin/debug/company-answers', [AdminController::class, 'debugCompanyAnswers'])
//     ->middleware(['auth', 'admin.check']) // Ganti dengan nama middleware yang benar
//     ->name('admin.debug.company-answers');

// Route untuk halaman Lupa NIM
Route::get('/forgot-nim', [AuthController::class, 'forgotNim'])->name('forgot-nim');
// End of routes