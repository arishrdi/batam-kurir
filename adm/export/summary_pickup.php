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
    /* Query Data */
        $query_data = "SELECT   
            dlv_pickup.id,
            dlv_pickup.pickup_date,
            mst_kurir.kurir_name AS kurir_pick_up,
            CASE
                WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                ELSE '-'
            END AS kurir_delivery,
            dlv_pickup.resi_code,
            dlv_pickup.cs_name,
            CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
            dlv_pickup.price,
            dlv_pickup.shiping_cost,
            dlv_pickup.status_pickup
        FROM dlv_pickup 
            JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
            LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
        WHERE 1=1 ";
    /* Query Data */

    /* Jika Pencarian Aktif */
        if ($_GET['status'] ?? "" != "") {
            $status     = $_GET['status'];
            $query_data = $query_data . " AND dlv_pickup.status_pickup='$status'";
        } else {
            $status     = '';
        }

        if ($_GET['kurir'] ?? "" != "") {
            $kurir_id   = $_GET['kurir'];
            $query_data = $query_data . " AND dlv_pickup.kurir_id='$kurir_id'";
        } else {
            $kurir_id   = '';
        }
        
        $date_from      = ($_GET['from'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
        $date_to        = ($_GET['to'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
        $date_now       = date('Y-m-d');
        $query_data     .= ($_GET['from'] ?? "") != "" && ($_GET['to'] ?? "") != "" ? " AND dlv_pickup.pickup_date BETWEEN '$date_from' AND '$date_to'" : " AND dlv_pickup.pickup_date='$date_now'";
    /* Jika Pencarian Aktif */

    /* Query Data */
        $sql_data       = mysqli_query($con, "$query_data ORDER BY dlv_pickup.id ASC");
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
    $sheet->setCellValue('B2', strtoupper('Data Summary Pick Up'));
    
    // Format Header Content
        $spreadsheet->getActiveSheet()->mergeCells('B2:M2');
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($style_title);
        $spreadsheet->getActiveSheet()->mergeCells('B3:M3');
    // Format Header Content
/* Header Content */

/* Body Content */
    $sheet->setCellValue('B4', strtoupper('No'));
    $sheet->setCellValue('C4', strtoupper('Tanggal'));
    $sheet->setCellValue('D4', strtoupper('Kurir Pick Up'));
    $sheet->setCellValue('E4', strtoupper('Kurir Delivery'));
    $sheet->setCellValue('F4', strtoupper('Kode Resi'));
    $sheet->setCellValue('G4', strtoupper('Nama CS'));
    $sheet->setCellValue('H4', strtoupper('No Hp Seller'));
    $sheet->setCellValue('J4', strtoupper('Harga'));
    $sheet->setCellValue('K4', strtoupper('Ongkir'));
    $sheet->setCellValue('L4', strtoupper('Total'));
    $sheet->setCellValue('M4', strtoupper('Keterangan'));
    
    /* Thead Set Style */
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($style_thead);
        $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('H4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('I4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('J4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($style_thead_name);
    /* Thead Set Style */
    
    /* Tbody */
        if ($all_data > 0) {
            $row_data       = 5;
            $col_first      = 'B';
            $urut           = 1;
            foreach($sql_data as $val_data){
                $index                  = $urut++;
                $price                  = $val_data['price'];
                $shiping_cost           = $val_data['shiping_cost'];
                // Simplified total calculation: always price + shipping cost
                $total_price            = $val_data['price'] + $val_data['shiping_cost'];
                
                // Simplified array summations
                $array_sum_price[]      = $val_data['price'];
                $array_sum_cost[]       = $val_data['shiping_cost'];
                $array_sum_total_price[]= $total_price;

                $sheet->setCellValue($col_first.$row_data, strtoupper($index));
                $sheet->setCellValue(get_col($col_first, 1).$row_data, strtoupper($val_data['pickup_date']));
                $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($val_data['kurir_pick_up']));
                $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($val_data['kurir_delivery']));
                $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($val_data['resi_code']));
                $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($val_data['cs_name']));
                $sheet->setCellValueExplicit(get_col($col_first, 6).$row_data, strtoupper($val_data['seller_phone_no']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue(get_col($col_first, 7).$row_data, strtoupper($price));
                $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper($shiping_cost));
                $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper($total_price));
                $sheet->setCellValue(get_col($col_first, 10).$row_data, strtoupper($val_data['status_pickup']));

                $spreadsheet->getActiveSheet()->getStyle($col_first.$row_data)->applyFromArray($style_tbody);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 1).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 2).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 3).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 4).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 5).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 6).$row_data)->applyFromArray($style_tbody_name);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 7).$row_data)->applyFromArray($style_tbody);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 8).$row_data)->applyFromArray($style_tbody);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 9).$row_data)->applyFromArray($style_tbody);
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 10).$row_data)->applyFromArray($style_tbody_name);

                $row_data++;
            }
            $jumlah_price   = ($array_sum_price == '') ? 0 : array_sum($array_sum_price);
            $jumlah_cost    = ($array_sum_cost == '') ? 0 : array_sum($array_sum_cost);
            $jumlah_total   = ($array_sum_total_price == '') ? 0 : array_sum($array_sum_total_price);

            $sheet->setCellValue($col_first.$row_data, strtoupper('JUMLAH'));
            $sheet->setCellValue(get_col($col_first, 7).$row_data, strtoupper($jumlah_price));
            $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper($jumlah_cost));
            $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper($jumlah_total));

            $spreadsheet->getActiveSheet()->mergeCells($col_first.$row_data.':'.get_col($col_first, 6).$row_data);
            $spreadsheet->getActiveSheet()->getStyle($col_first.$row_data.':'.get_col($col_first, 6).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 7).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 8).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 9).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->mergeCells(get_col($col_first, 10).$row_data.':'.get_col($col_first, 11).$row_data);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 10).$row_data.':'.get_col($col_first, 11).$row_data)->applyFromArray($style_thead);
        }else{
            $row_data       = 5;
            $col_first      = 'B';
            $urut           = 1;
            $sheet->setCellValue($col_first.$row_data, strtoupper('JUMLAH'));
            $sheet->setCellValue(get_col($col_first, 7).$row_data, strtoupper('0 K'));
            $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper('0 K'));
            $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper('0 K'));

            $spreadsheet->getActiveSheet()->mergeCells($col_first.$row_data.':'.get_col($col_first, 6).$row_data);
            $spreadsheet->getActiveSheet()->getStyle($col_first.$row_data.':'.get_col($col_first, 6).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 7).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 8).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 9).$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->mergeCells(get_col($col_first, 10).$row_data.':'.get_col($col_first, 11).$row_data);
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, 10).$row_data.':'.get_col($col_first, 11).$row_data)->applyFromArray($style_thead);
        }
    /* Tbody */ 
/* Body Content */

/* Configuration Save File To Excel */
    $writer = new Xlsx($spreadsheet);
    $writer->save('Data Summary Pick Up.xlsx');
    header('Content-type: application/xlsx');

    header('Content-Disposition: attachment; filename="Data Summary Pick Up.xlsx"'); // It will be called downloaded
    readfile('Data Summary Pick Up.xlsx'); // The PDF source is in original
/* Configuration Save File To Excel */
?>