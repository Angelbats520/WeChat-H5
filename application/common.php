<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * 获取当前完整URL
 * @return string
 */
function get_url(){
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * C密码加密方法
 * @param string $pwd 要加密的原始密码
 * @param string $str 加密字符串
 * @return string
 */
function password_encode($pwd,$str='')
{
    if(empty($str)){
        $str = 'angel';
    }
    $result = 'cedong'.md5($pwd.$str);
    return $result;
}

/**
 * 密码比较方法,所有涉及密码比较的地方都用这个方法
 * @param string $password 要比较的密码
 * @param string $passwordInDb 数据库保存的已经加密过的密码
 * @return boolean 密码相同，返回true
 */
function compare_password($password, $passwordInDb)
{
    return password_encode($password) == $passwordInDb;
}

function exportExcel(array $datas, $fileName = '', array $options = [])
{
    try {
        if (empty($datas)) {
            return false;
        }
        set_time_limit(0);
        /** @var Spreadsheet $objSpreadsheet */
        $objSpreadsheet = app(Spreadsheet::class);
        /* 设置默认文字居左，上下居中 */
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $objSpreadsheet->getDefaultStyle()->applyFromArray($styleArray);
        /* 设置Excel Sheet */
        $activeSheet = $objSpreadsheet->setActiveSheetIndex(0);

        /* 打印设置 */
        if (isset($options['print']) && $options['print']) {
            /* 设置打印为A4效果 */
            $activeSheet->getPageSetup()->setPaperSize(PageSetup:: PAPERSIZE_A4);
            /* 设置打印时边距 */
            $pValue = 1 / 2.54;
            $activeSheet->getPageMargins()->setTop($pValue / 2);
            $activeSheet->getPageMargins()->setBottom($pValue * 2);
            $activeSheet->getPageMargins()->setLeft($pValue / 2);
            $activeSheet->getPageMargins()->setRight($pValue / 2);
        }

        /* 行数据处理 */
        foreach ($datas as $sKey => $sItem) {
            /* 默认文本格式 */
            $pDataType = DataType::TYPE_STRING;

            /* 设置单元格格式 */
            if (isset($options['format']) && !empty($options['format'])) {
                $colRow = Coordinate::coordinateFromString($sKey);

                /* 存在该列格式并且有特殊格式 */
                if (isset($options['format'][$colRow[0]]) &&
                    NumberFormat::FORMAT_GENERAL != $options['format'][$colRow[0]]) {
                    $activeSheet->getStyle($sKey)->getNumberFormat()
                        ->setFormatCode($options['format'][$colRow[0]]);

                    if (false !== strpos($options['format'][$colRow[0]], '0.00') &&
                        is_numeric(str_replace(['￥', ','], '', $sItem))) {
                        /* 数字格式转换为数字单元格 */
                        $pDataType = DataType::TYPE_NUMERIC;
                        $sItem     = str_replace(['￥', ','], '', $sItem);
                    }
                } elseif (is_int($sItem)) {
                    $pDataType = DataType::TYPE_NUMERIC;
                }
            }

            $activeSheet->setCellValueExplicit($sKey, $sItem, $pDataType);

            /* 存在:形式的合并行列，列入A1:B2，则对应合并 */
            if (false !== strstr($sKey, ":")) {
                $options['mergeCells'][$sKey] = $sKey;
            }
        }

        unset($datas);

        /* 设置锁定行 */
        if (isset($options['freezePane']) && !empty($options['freezePane'])) {
            $activeSheet->freezePane($options['freezePane']);
            unset($options['freezePane']);
        }

        /* 设置宽度 */
        if (isset($options['setWidth']) && !empty($options['setWidth'])) {
            foreach ($options['setWidth'] as $swKey => $swItem) {
                $activeSheet->getColumnDimension($swKey)->setWidth($swItem);
            }

            unset($options['setWidth']);
        }

        /* 设置背景色 */
        if (isset($options['setARGB']) && !empty($options['setARGB'])) {
            foreach ($options['setARGB'] as $sItem) {
                $activeSheet->getStyle($sItem)
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB(Color::COLOR_YELLOW);
            }

            unset($options['setARGB']);
        }

        /* 设置公式 */
        if (isset($options['formula']) && !empty($options['formula'])) {
            foreach ($options['formula'] as $fKey => $fItem) {
                $activeSheet->setCellValue($fKey, $fItem);
            }

            unset($options['formula']);
        }

        /* 合并行列处理 */
        if (isset($options['mergeCells']) && !empty($options['mergeCells'])) {
            $activeSheet->setMergeCells($options['mergeCells']);
            unset($options['mergeCells']);
        }

        /* 设置居中 */
        if (isset($options['alignCenter']) && !empty($options['alignCenter'])) {
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ];

            foreach ($options['alignCenter'] as $acItem) {
                $activeSheet->getStyle($acItem)->applyFromArray($styleArray);
            }

            unset($options['alignCenter']);
        }

        /* 设置加粗 */
        if (isset($options['bold']) && !empty($options['bold'])) {
            foreach ($options['bold'] as $bItem) {
                $activeSheet->getStyle($bItem)->getFont()->setBold(true);
            }

            unset($options['bold']);
        }

        /* 设置单元格边框，整个表格设置即可，必须在数据填充后才可以获取到最大行列 */
        if (isset($options['setBorder']) && $options['setBorder']) {
            $border    = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN, // 设置border样式
                        'color'       => ['argb' => 'FF000000'], // 设置border颜色
                    ],
                ],
            ];
            $setBorder = 'A1:' . $activeSheet->getHighestColumn() . $activeSheet->getHighestRow();
            $activeSheet->getStyle($setBorder)->applyFromArray($border);
            unset($options['setBorder']);
        }

        $fileName = !empty($fileName) ? $fileName : (date('YmdHis') . '.xlsx');

        if (!isset($options['savePath'])) {
            /* 直接导出Excel，无需保存到本地，输出07Excel文件 */
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header(
                "Content-Disposition:attachment;filename=" . iconv(
                    "utf-8", "GB2312//TRANSLIT", $fileName
                )
            );
            header('Cache-Control: max-age=0');//禁止缓存
            $savePath = 'php://output';
        } else {
            $savePath = $options['savePath'];
        }

        ob_clean();
        ob_start();
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $objWriter->save($savePath);
        /* 释放内存 */
        $objSpreadsheet->disconnectWorksheets();
        unset($objSpreadsheet);
        ob_end_flush();

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function error_message($str,$data='')
{
    $arr = [
        'busy_server' => ['message'=>'服务器繁忙，请稍候再试！','code'=>1001,'data'=>$data],
    ];
    return $arr[$str];
}