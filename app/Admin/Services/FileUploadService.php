<?php

namespace App\Admin\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;

class FileUploadService
{
    const EXCLUDED = [
            '.php',
            '.exe',
            '.html',
            '.htm',
            '.htaccess',
            '.pl',
            '.cgi',
            '.js',
            '.script',
            '.sh',
            '.asp',
            '.ph3',
            '.php4',
            '.php3',
            '.php5',
            '.phtm',
            '.phtml',
            '.sql',
            '.bak',
            '.config',
            '.fla',
            '.inc',
            '.log',
            '.ini',
            '.dist',
        ];

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function save($request)
    {
        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $filename = $file->getClientOriginalName();

            //validate if file type if allowed
            $extension = $file->getClientOriginalExtension();
            if (is_array(config('settings.disallowed_file_types'))) {
                if (in_array(".$extension", self::EXCLUDED)) {
                    abort(409, __('lang.file_type_not_allowed'));
                }
            }

            //filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //thumb path
            $thumb_name = generateThumbnailName($filename);
            $thumb_path = BASE_DIR . "/storage/temp/$directory/$thumb_name";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            if (is_array(@getimagesize($file_path))) {
                try {
                    $img = Image::make($file_path)->resize(null, 90, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumb_path);
                } catch (NotReadableException $e) {
                    $message = $e->getMessage();
                    Log::error("[Image Library] failed to create uplaoded image thumbnail. Image type is not supported on this server", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $message]);
                    abort(409, __('lang.image_file_type_not_supported'));
                }
            }

            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
                'filepath' => $file_path,
            ]);
        }
        //fail - send back a response
        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function saveWebForm($request)
    {
        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $filename = $file->getClientOriginalName();

            //validate if file type if allowed
            $extension = $file->getClientOriginalExtension();
            if (is_array(config('settings.disallowed_file_types'))) {
                if (in_array(".$extension", self::EXCLUDED)) {
                    abort(409, __('lang.file_type_not_allowed'));
                }
            }

            //filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //thumb path
            $thumb_name = generateThumbnailName($filename);
            $thumb_path = BASE_DIR . "/storage/temp/$directory/$thumb_name";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            if (is_array(@getimagesize($file_path))) {
                try {
                    $img = Image::make($file_path)->resize(null, 90, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumb_path);
                } catch (NotReadableException $e) {
                    $message = $e->getMessage();
                    Log::error("[Image Library] failed to create uplaoded image thumbnail. Image type is not supported on this server", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $message]);
                    abort(409, __('lang.image_file_type_not_supported'));
                }
            }

            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
                'filepath' => $file_path,
            ]);
        }
        //fail - send back a response
        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * save an uploaded file from tinymce editor
     */
    public function saveTinyMCEImage($request)
    {
        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename (make it a safe file name)
            $filename = "img.$extension";

            //new filepath
            $file_path = public_path() . "/storage/files/$directory/$filename";

            //create directory
            Storage::makeDirectory("files/$directory");

            //save file to directory
            Storage::putFileAs("files/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/jp2'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //success - send back a response
            return response()->json([
                'location' => url("/storage/files/$directory/$filename")
                //'location' => public_path() . "/storage/files/$directory/$filename",
            ]);
        }
    }

    /**
     * Upload any file into the temp folder
     * @param object Request instance of the request object
     */
    public function uploadImportFiles($request, $dir = 'temp', $valid_extensions = [] )
    {
        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //original file name & extension
            $extension = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();
            $filesize = $file->getSize();

            //validate
            $valid_extensions = empty($valid_extensions) ? ['csv', 'xls', 'xlsx', 'pdf', 'doc', 'docx', 'ppt', 'pptx'] : $valid_extensions;

            if (!in_array($extension, $valid_extensions)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //save file to directory
            try {
                Storage::putFileAs($dir, $file, $filename);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'extension' => $extension,
                'filepath' => "$dir/$filename",
                //'filesize' => humanFileSize($filesize),
            ]);
        }
        //fail - send back a response
        return response()->json([
            'success' => false,
        ]);
    
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function uploadCoverImage($request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename
            $filename = "cover-image.$extension";

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check dims (min width 100px min height 100px)
            if ($imagedata[0] < 400 || $imagedata[1] < 170) {
                abort(409, __('lang.image_size_wrong_cover_image') . ' (400px X 170px)');
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.file_type_not_allowed') . ' (' . __('lang.jpg_png_only') . ')');
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
        //fail - send back a response
        return response()->json([
            'success' => false,
        ]);
    }
}