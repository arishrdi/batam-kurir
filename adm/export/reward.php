<?php
include '../../theme/vendor/autoload.php'; // Load Dependency PHP Spreadsheet
include '../../config/db.php'; // Load Database Koneksi
include '../../config/local_date.php'; // Load Database Koneksi

/*  PHP Spreedsheet Basic Configuration */ 
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet    = new Spreadsheet();
    $sheet          = $spreadsheet->getActiveSheet();
/*  PHP Spreedsheet Basic Configuration */ 

/* Function Get Colom Name */ 
    function get_col($input, $increment) {
        $letters = strtoupper($input); 
        $lettersArray = str_split($letters);

        for ($i = count($lettersArray) - 1; $i >= 0; $i--) {
            $lettersArray[$i] = chr(ord($lettersArray[$i]) + $increment);
            if ($lettersArray[$i] > 'Z') {
                $lettersArray[$i] = 'A';
                $increment = 1;
            } else {
                break;
            }
        }
        return implode('', $lettersArray);
    }
/* Function Get Colom Name */ 

/* Get Data */ 
    /* Query Reward */
        $point_reward   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM mst_config"))['poin_reward'];
    /* Query Data */
        $query_data     = "SELECT * FROM trx_reward WHERE counting >= $point_reward AND 1=1";

    /* Jika Pencarian Aktif */
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND seller_phone_no='$pencarian'";
        } else {
            $pencarian  = '';
        }

        if ($_GET['date'] ?? "" != "") {
            $date       = $_GET['date'];
            $query_data = $query_data . " AND milestone_date='$date'";
        } else {
            $date       = '';
        }
    /* Jika Pencarian Aktif */

    /* Query Data */
        $sql_data       = mysqli_query($con, "$query_data ORDER BY id DESC");
        $all_data       = mysqli_num_rows($sql_data);
    /* Query Data */
/* Get Data */ 

/* STYLE CSS SHEET */
    $style_title    = [
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];
    
    $style_thead = [
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            'wrapText' => false, 
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ], 
    ];

    $style_thead_name = [
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            'wrapText' => false, 
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ], 
    ];

    $style_tbody = [
        'font' => [
            'bold' => false,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            'wrapText' => false, 
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ], 
    ];

    $style_tbody_name = [
        'font' => [
            'bold' => false,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            'wrapText' => false, 
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ], 
    ];
/* STYLE CSS SHEET */

/* Header Content */
    $sheet->setCellValue('B2', strtoupper('Data Reward'));
    
    // Format Header Content
        $spreadsheet->getActiveSheet()->mergeCells('B2:F2');
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($style_title);
        $spreadsheet->getActiveSheet()->mergeCells('B3:F3');
    // Format Header Content
/* Header Content */

/* Body Content */
    $sheet->setCellValue('B4', strtoupper('No'));
    $sheet->setCellValue('C4', strtoupper('Tanggal Pencapaian'));
    $sheet->setCellValue('D4', strtoupper('No Hp Seller'));
    $sheet->setCellValue('E4', strtoupper('Total Poin'));
    $sheet->setCellValue('F4', strtoupper('Status'));
    
    /* Thead Set Style */
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($style_thead);
        $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($style_thead_name);
    /* Thead Set Style */
    
    /* Tbody */
        $row_data       = 5;
        $col_first      = 'B';
        $urut           = 1;
        foreach($sql_data as $val_data){
            $index      = $urut++;

            $sheet->setCellValue($col_first.$row_data, strtoupper($index));
            $sheet->setCellValue(get_col($col_first, 1).$row_data, strtoupper($val_data['milestone_date']));
            $sheet->setCellValueExplicit(get_col($col_first, 2).$row_data, strtoupper('+'.$val_data['seller_phone_no']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($val_data['counting']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($val_data['status_claim']));

            $spreadsheet->getActiveSheet()->getStyle($col_first.$row_data)->applyFromArray($style_tbody);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 1).$row_data)->applyFromArray($style_tbody_name);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 2).$row_data)->applyFromArray($style_tbody_name);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 3).$row_data)->applyFromArray($style_tbody_name);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 4).$row_data)->applyFromArray($style_tbody_name);

            $row_data++;
        }
    /* Tbody */ 
/* Body Content */

/* Configuration Save File To Excel */
    $filename = 'Data Reward.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
    
    // Clear any previous output
    if (ob_get_contents()) ob_clean();
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    readfile($filename);
    unlink($filename); // Clean up temporary file
/* Configuration Save File To Excel */
?>