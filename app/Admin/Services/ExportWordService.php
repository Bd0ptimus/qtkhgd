<?php

namespace App\Admin\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Exception;

class ExportWordService
{
    public function handleExportWordUseTemplate($data, $templateName, $exportFileName)
    {
        // set config phpword
        \PhpOffice\PhpWord\Settings::setZipClass(\PhpOffice\PhpWord\Settings::PCLZIP);

        $templateProcessor = new TemplateProcessor($templateName);

        // set value data array ex: ['firstname' => 'John', 'lastname' => 'Doe']
        $templateProcessor->setValues($data['value']);

        // clone row with values
        if(isset($data['values']) && count($data['values']) > 0) {
            foreach ($data['values'] as $key => $value) {
                $templateProcessor->cloneRowAndSetValues($key, $value);
            }
        }

        // set value image if have
        if(isset($data['images']) && count($data['images']) > 0) {
            foreach ($data['images'] as $key => $path) {
                $templateProcessor->setImageValue($key, $path);
            }
        }

        try {
            // save as file to storage/public
            // storage_path('app/public/template.docx')
            // public_path('template.docx')
            $templateProcessor->saveAs($exportFileName);
        } catch (Exception $ex) {
            Log::error($ex->getMessage(), [
                'process' => '[export word]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }

        // download file docx
        return response()->download($exportFileName)->deleteFileAfterSend(true);
    }

    public function handleExportWordUseTemplateHTML()
    {
        $sang = [
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
        ];

        $chieu = [
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
            ['thu 2', 'thu 3', 'thu 4', 'thu 5', 'thu 6', 'thu 7'],
        ];

        $datas = [
            'week' => 33,
            'sang' => $sang,
            'chieu' => $chieu,
            'row_num' => count($sang) + count($chieu),
            'total' => [
                ['name' => 'Tiếng việt', 'lesson' => 5, 'note' => 'ghi chu 1'],
                ['name' => 'Toán', 'lesson' => 6, 'note' => 'ghi chu 2'],
            ],
        ];

        $headers = array(
            "Content-type"        => "text/html",
            "Content-Disposition" => "attachment;Filename=report.docx"
        );

        $content =  View::make('templates.exports.words.temp', [
            'datas' => $datas,
        ])->render();

        return Response::make($content,200, $headers);
    }
}