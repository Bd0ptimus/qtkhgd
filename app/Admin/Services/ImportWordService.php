<?php

namespace App\Admin\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Exception;


class ImportWordService
{
    public function getFileContent($file, $fileNameEx, $type = 1) {
        try {
            
            //error_reporting(E_ALL & ~E_NOTICE);

            $path= $file->path();
            $input_file = $file->getClientOriginalName();
            $extension = pathinfo($input_file, PATHINFO_EXTENSION);

            $docType = $extension == 'doc' ? 'MsDoc' : 'Word2007';
            
            $phpWord = IOFactory::createReader($docType)->load($path);
            
           
            $objWriter = IOFactory::createWriter($phpWord, 'HTML');
            $objWriter->save($fileNameEx);

            $myfile = fopen($fileNameEx, "r") or die("Unable to open file!");
            $content = fread($myfile,filesize($fileNameEx));

            fclose($myfile);
            return $this->setStyleForHTMLContent($content);

        } catch (Exception $ex) {
            dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[export word]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
    }

    public function handleFileImport($path, $fileNameEx, $type = 1)
    {
        try {
            $phpWord = IOFactory::createReader('Word2007')->load($path);

            $objWriter = IOFactory::createWriter($phpWord, 'HTML');
            $objWriter->save($fileNameEx);
            return response()->download($fileNameEx)->deleteFileAfterSend(true);
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
    }

    private function setStyleForHTMLContent($content){
        $doc = new \DOMDocument();
        $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $spans = $doc->getElementsByTagName('span');
        foreach($spans as $span){
            $span_style = $span->getAttribute('style');
                if ($span_style ) {
                $span->setAttribute('style',"color:black; font-family:'times new roman'"); //set style here
            }
        }
        return $doc->saveHTML();
    }
}