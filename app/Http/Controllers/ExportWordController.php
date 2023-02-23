<?php

namespace App\Http\Controllers;

use App\Admin\Services\ExportWordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ExportWordController extends Controller
{
    protected $exportWordService;

    public function __construct(ExportWordService $exportWordService)
    {
        $this->exportWordService = $exportWordService;
    }

    public function exportUseDocx()
    {
        $datas = [
          'value' => [
              'first_name' => 'Kevin',
              'last_name' => 'Vu',
              'content' => 'content test',
              'line1' => 'line 111111',
              'line3' => 'line 333333'
          ],
          'values' => [
              'row' => [
                  ['row' => 1, 'content1' => 'content 11', 'content2' => 'content 12', 'content3' => 'content 13', 'content4' => 'content 14', 'content5' => 'content 15', 'content6' => 'content 16', 'content7' => 'content 17', 'content8' => 'content 18', 'content9' => 'content 19'],
                  ['row' => 2, 'content1' => 'content 21', 'content2' => 'content 22', 'content3' => 'content 23', 'content4' => 'content 24', 'content5' => 'content 25', 'content6' => 'content 26', 'content7' => 'content 27', 'content8' => 'content 28', 'content9' => 'content 29'],
                  ['row' => 3, 'content1' => 'content 31', 'content2' => 'content 32', 'content3' => 'content 33', 'content4' => 'content 34', 'content5' => 'content 35', 'content6' => 'content 36', 'content7' => 'content 37', 'content8' => 'content 38', 'content9' => 'content 39'],
                  ['row' => 4, 'content1' => 'content 41', 'content2' => 'content 42', 'content3' => 'content 44', 'content4' => 'content 44', 'content5' => 'content 45', 'content6' => 'content 46', 'content7' => 'content 47', 'content8' => 'content 48', 'content9' => 'content 49'],
              ],
          ],
        ];

        return $this->exportWordService->handleExportWordUseTemplate($datas, public_path('exports/temp/temp1.docx'), public_path('reslut1.docx'));
    }

    public function exportUseHTML()
    {
        return $this->exportWordService->handleExportWordUseTemplateHTML();
    }

    public function getTempHTML()
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

        return View::make('templates.exports.words.temp', [
            'datas' => $datas
        ]);
    }
}
