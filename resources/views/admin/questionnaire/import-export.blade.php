@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import/Export Questionnaires</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title">Import Questionnaire</h4>
                                </div>
                                <div class="card-body">
                                    <p>Upload an Excel file to import questionnaires. Please make sure to follow the template format.</p>
                                    <form action="{{ route('admin.questionnaires.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="file">Excel File</label>
                                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                                            @error('file')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary">Import</button>
                                            <a href="{{ route('admin.questionnaires.download-template') }}" class="btn btn-secondary">Download Template</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h4 class="card-title">Export Questionnaire</h4>
                                </div>
                                <div class="card-body">
                                    <p>Export all questionnaires to an Excel file.</p>
                                    <a href="{{ route('admin.questionnaires.export') }}" class="btn btn-success">Export All Questionnaires</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Instructions</h4>
                        <ol>
                            <li>Download the template to see the required format.</li>
                            <li>Fill in the template with your questionnaire data.</li>
                            <li>Upload the completed file using the import form.</li>
                            <li>Each row represents a question.</li>
                            <li>Questions with the same category name will be grouped together.</li>
                            <li>For multiple-choice questions, separate options with the pipe (|) character.</li>
                            <li>Set "Has Other Option" to "yes" if you want to include an "Other" option with a text field.</li>
                        </ol>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.questionnaires.index') }}" class="btn btn-secondary">Back to Questionnaires</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
