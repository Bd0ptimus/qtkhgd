<?php

namespace App\Admin\Models\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait ExportTrait
{
    protected function getCellValidation(Worksheet $sheet, $dropColumn, $options, $startAt = 2)
    {
        $validation = $sheet->getCell("{$dropColumn}{$startAt}")->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Lỗi nhập vào');
        $validation->setError('Giá trị không nằm trong danh sahcs');
        $validation->setPromptTitle('Chọn từ danh sách');
        $validation->setPrompt('Xin hãy chọn giá trị trong danh sách');
        $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

        return $validation;
    }

    protected function getCellValidationFormula(Worksheet $sheet, $dropColumn, $line, $startAt = 2)
    {
        $validation = $sheet->getCell("{$dropColumn}{$startAt}")->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Lỗi nhập vào');
        $validation->setError('Giá trị không nằm trong danh sahcs');
        $validation->setPromptTitle('Chọn từ danh sách');
        $validation->setPrompt('Xin hãy chọn giá trị trong danh sách');
        $validation->setFormula1('\'Data\'!$B$' . $line . ':$' . $sheet->getHighestDataColumn() . '$' . $line);

        return $validation;
    }
}