<?php

namespace App\Http\Controllers;

use App\Admin\Services\ImportWordService;
use Illuminate\Http\Request;

class ImportWordController extends Controller
{
    protected $importWordService;

    public function __construct(ImportWordService $importWordService)
    {
        $this->importWordService = $importWordService;
    }

    public function handle()
    {
        return $this->importWordService->handleFileImport(
            public_path('imports/import-word.docx'),
            public_path('export.html')
        );
    }

    public function getFileUploadForm()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:docx,doc|max:5120',
        ], [
            'file.required' => trans('validation.required'),
            'file.mimes' => trans('validation.mimes'),
            'file.max' => trans('validation.max'),
        ]);

        return $this->importWordService->handleFileImport(
            $request->file('file')->path(),
            storage_path('app/public/export.html')
        );
    }
}
