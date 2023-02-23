<?php

namespace App\Admin\Controllers;

use App\Admin\Services\FileUploadService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileUploadController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        //parent
        //parent::__construct();
        $this->fileUploadService = $fileUploadService;
        //authenticated
        //$this->middleware('auth');
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function save(Request $request)
    {
        return $this->fileUploadService->save($request);
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function saveWebForm(Request $request)
    {
        return $this->fileUploadService->saveWebForm($request);
    }

    /**
     * save an uploaded file from tinymce editor
     */
    public function saveTinyMCEImage(Request $request)
    {
        return $this->fileUploadService->saveTinyMCEImage($request);
    }

    /**
     * Upload any file into the temp folder
     * @param object Request instance of the request object
     */
    public function uploadImportFiles(Request $request)
    {
        return $this->fileUploadService->uploadImportFiles($request);
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function uploadCoverImage(Request $request)
    {
        return $this->fileUploadService->uploadCoverImage($request);
    }
}