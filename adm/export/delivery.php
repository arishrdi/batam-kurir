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
    /* Query Data - Show latest delivery attempt per pickup */
        $query_data = "SELECT
            dlv_pickup.id AS pickup_id,
            trx_delivery.id AS delivery_id,
            dlv_pickup.pickup_date,
            trx_delivery.delivery_date,
            dlv_pickup.kurir_id AS kurir_pick_up_id,
            mst_kurir.kurir_name AS kurir_pick_up,
            CASE 
                WHEN trx_delivery.kurir_id != '' THEN trx_delivery.kurir_id
                ELSE ''
            END AS kurir_delivery_id,
            CASE
                WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                ELSE '-'
            END AS kurir_delivery,
            dlv_pickup.resi_code,
            dlv_pickup.cs_name,
            CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
            dlv_pickup.price,
            dlv_pickup.shiping_cost,
            dlv_pickup.status_pickup,
            trx_delivery.status_delivery,
            ROW_NUMBER() OVER (PARTITION BY dlv_pickup.pickup_date ORDER BY dlv_pickup.id ASC) AS daily_sequence_id
        FROM dlv_pickup 
            JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
            LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
        WHERE 1=1";
    /* Query Data */

    /* Jika Pencarian Aktif */
        if ($_GET['kurir'] ?? "" != "") {
            $kurir_id   = $_GET['kurir'];
            $query_data = $query_data . " AND trx_delivery.kurir_id='$kurir_id'";

            $cek_kurir  = mysqli_query($con, "SELECT * FROM mst_kurir WHERE id=$kurir_id");
            $row_kurir  = mysqli_fetch_assoc($cek_kurir);
            $kode_kurir = getInitials($row_kurir['kurir_name']).$row_kurir['id'];
        } else {
            $kurir_id   = '';
            $kode_kurir = '';
        }
        
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND (dlv_pickup.resi_code LIKE '%$pencarian%'
            OR dlv_pickup.cs_name LIKE '%$pencarian%'
            OR dlv_pickup.seller_phone_no LIKE '%$pencarian%'
            OR mst_kurir.kurir_name LIKE '%$pencarian%'
            OR trx_delivery.status_delivery='$pencarian')";
        } else {
            $pencarian  = '';
        }

        if ($_GET['date'] ?? "" != "") {
            $date       = $_GET['date'];
            $query_data = $query_data . " AND trx_delivery.delivery_date='$date'";
        } else {
            $date       = date('Y-m-d');
            $query_data = $query_data . " AND trx_delivery.delivery_date='$date'";
        }
    /* Jika Pencarian Aktif */

    /* Query Data */
        $sql_data       = mysqli_query($con, "$query_data ORDER BY trx_delivery.id ASC");
        $all_data       = mysqli_num_rows($sql_data);

        /* Calculate Main Table Totals */
        $main_total_price = 0;
        $main_total_shipping = 0;
        $pending_summary_total = 0;
        $cancel_summary_total = 0;
        $main_temp_data = [];
        while ($main_row = mysqli_fetch_assoc($sql_data)) {
            $main_temp_data[] = $main_row;
            $main_total_price += $main_row['price'];
            $main_total_shipping += $main_row['shiping_cost'];
            
            // Calculate totals by status for summary
            if ($main_row['status_delivery'] == 'PENDING') {
                $pending_summary_total += $main_row['price'];
            } elseif ($main_row['status_delivery'] == 'CANCEL') {
                $cancel_summary_total += $main_row['price'];
            }
        }
        $sql_data = $main_temp_data;
        $total_delivery_summary = $main_total_price - $pending_summary_total - $cancel_summary_total;

        /* Pending Table Data - Today - Show latest delivery attempt per pickup */
        $today = date('Y-m-d');
        $query_pending = "SELECT
                dlv_pickup.id AS pickup_id,
                trx_delivery.id AS delivery_id,
                dlv_pickup.pickup_date,
                trx_delivery.delivery_date,
                dlv_pickup.kurir_id AS kurir_pick_up_id,
                mst_kurir.kurir_name AS kurir_pick_up,
                CASE 
                    WHEN trx_delivery.kurir_id != '' THEN trx_delivery.kurir_id
                    ELSE ''
                END AS kurir_delivery_id,
                CASE
                    WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                    ELSE '-'
                END AS kurir_delivery,
                dlv_pickup.resi_code,
                dlv_pickup.cs_name,
                CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
                dlv_pickup.price,
                dlv_pickup.shiping_cost,
                trx_delivery.status_delivery,
                ROW_NUMBER() OVER (PARTITION BY dlv_pickup.pickup_date ORDER BY dlv_pickup.id ASC) AS daily_sequence_id
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                    AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
            WHERE trx_delivery.status_delivery='PENDING' 
                AND trx_delivery.delivery_date='$today'";

        $sql_pending_data = mysqli_query($con, "$query_pending ORDER BY trx_delivery.id DESC");
        $pending_data_count = mysqli_num_rows($sql_pending_data);

        /* Calculate Pending Totals */
        $pending_total_price = 0;
        $pending_total_shipping = 0;
        $pending_temp_data = [];
        while ($pending_row = mysqli_fetch_assoc($sql_pending_data)) {
            $pending_temp_data[] = $pending_row;
            $pending_total_price += $pending_row['price'];
            $pending_total_shipping += $pending_row['shiping_cost'];
        }
        $sql_pending_data = $pending_temp_data;

        /* Cancel Table Data - Today - Show latest delivery attempt per pickup */
        $query_cancel = "SELECT
                dlv_pickup.id AS pickup_id,
                trx_delivery.id AS delivery_id,
                dlv_pickup.pickup_date,
                trx_delivery.delivery_date,
                dlv_pickup.kurir_id AS kurir_pick_up_id,
                mst_kurir.kurir_name AS kurir_pick_up,
                CASE 
                    WHEN trx_delivery.kurir_id != '' THEN trx_delivery.kurir_id
                    ELSE ''
                END AS kurir_delivery_id,
                CASE
                    WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                    ELSE '-'
                END AS kurir_delivery,
                dlv_pickup.resi_code,
                dlv_pickup.cs_name,
                CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
                dlv_pickup.price,
                dlv_pickup.shiping_cost,
                trx_delivery.status_delivery,
                ROW_NUMBER() OVER (PARTITION BY dlv_pickup.pickup_date ORDER BY dlv_pickup.id ASC) AS daily_sequence_id
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                    AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
            WHERE trx_delivery.status_delivery='CANCEL' 
                AND trx_delivery.delivery_date='$today'";

        $sql_cancel_data = mysqli_query($con, "$query_cancel ORDER BY trx_delivery.id DESC");
        $cancel_data_count = mysqli_num_rows($sql_cancel_data);

        /* Calculate Cancel Totals */
        $cancel_total_price = 0;
        $cancel_total_shipping = 0;
        $cancel_temp_data = [];
        while ($cancel_row = mysqli_fetch_assoc($sql_cancel_data)) {
            $cancel_temp_data[] = $cancel_row;
            $cancel_total_price += $cancel_row['price'];
            $cancel_total_shipping += $cancel_row['shiping_cost'];
        }
        $sql_cancel_data = $cancel_temp_data;
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
    $sheet->setCellValue('B2', strtoupper('Data Delivery'));
    
    // Format Header Content
        $spreadsheet->getActiveSheet()->mergeCells('B2:K2');
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($style_title);
        $spreadsheet->getActiveSheet()->mergeCells('B3:K3');
    // Format Header Content
/* Header Content */

/* Body Content - Main Delivery Table */
    $sheet->setCellValue('B4', strtoupper('No'));
    $sheet->setCellValue('C4', strtoupper('ID'));
    $sheet->setCellValue('D4', strtoupper('Kurir Pick Up'));
    $sheet->setCellValue('E4', strtoupper('Kurir Delivery'));
    $sheet->setCellValue('F4', strtoupper('Kode Resi'));
    $sheet->setCellValue('G4', strtoupper('Nama CS'));
    $sheet->setCellValue('H4', strtoupper('No Hp Seller'));
    $sheet->setCellValue('I4', strtoupper('Harga'));
    $sheet->setCellValue('J4', strtoupper('Ongkir'));
    $sheet->setCellValue('K4', strtoupper('Keterangan'));
    
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
    /* Thead Set Style */
    
    /* Main Delivery Table */
        $row_data       = 5;
        $col_first      = 'B';
        $urut           = 1;
        
        foreach($sql_data as $val_data){
            $index = $urut++;
            $price = $val_data['price'];
            $shiping_cost = $val_data['shiping_cost'];

            $sheet->setCellValue($col_first.$row_data, $index);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $val_data['daily_sequence_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($val_data['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($val_data['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($val_data['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($val_data['cs_name']));
            $sheet->setCellValueExplicit(get_col($col_first, 6).$row_data, $val_data['seller_phone_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $price);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, $shiping_cost);
            $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper($val_data['status_delivery']));

            // Apply styles
            for($i = 0; $i <= 9; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }

            $row_data++;
        }
        
        // Add totals for main table
        if($all_data > 0) {
            $sheet->setCellValue('H'.$row_data, 'TOTAL:');
            $sheet->setCellValue('I'.$row_data, $main_total_price);
            $sheet->setCellValue('J'.$row_data, $main_total_shipping);
            $sheet->setCellValue('K'.$row_data, $main_total_price - $main_total_shipping);
            
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('K'.$row_data)->applyFromArray($style_thead);
            $row_data += 2;
        }

        /* Pending Table */
        $row_data += 2;
        $sheet->setCellValue('B'.$row_data, 'TABEL PENDING HARI INI');
        $spreadsheet->getActiveSheet()->mergeCells('B'.$row_data.':K'.$row_data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$row_data)->applyFromArray($style_title);
        $row_data += 2;
        
        // Pending headers
        $sheet->setCellValue('B'.$row_data, 'No');
        $sheet->setCellValue('C'.$row_data, 'ID');
        $sheet->setCellValue('D'.$row_data, 'Kurir Pickup');
        $sheet->setCellValue('E'.$row_data, 'Kurir Delivery');
        $sheet->setCellValue('F'.$row_data, 'Kode Resi');
        $sheet->setCellValue('G'.$row_data, 'Nama CS');
        $sheet->setCellValue('H'.$row_data, 'No HP Seller');
        $sheet->setCellValue('I'.$row_data, 'Harga');
        $sheet->setCellValue('J'.$row_data, 'Ongkir');
        $sheet->setCellValue('K'.$row_data, 'Keterangan');
        
        for($i = 0; $i <= 9; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_thead);
        }
        $row_data++;
        
        $pending_urut = 1;
        foreach($sql_pending_data as $pending_data){
            $sheet->setCellValue($col_first.$row_data, $pending_urut++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $pending_data['daily_sequence_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($pending_data['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($pending_data['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($pending_data['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($pending_data['cs_name']));
            $sheet->setCellValueExplicit(get_col($col_first, 6).$row_data, '+' . $pending_data['seller_phone_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $pending_data['price']);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, $pending_data['shiping_cost']);
            $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper($pending_data['status_delivery']));

            for($i = 0; $i <= 9; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for pending table
        if($pending_data_count > 0) {
            $sheet->setCellValue('H'.$row_data, 'TOTAL PENDING:');
            $sheet->setCellValue('I'.$row_data, $pending_total_price);
            $sheet->setCellValue('J'.$row_data, $pending_total_shipping);
            $sheet->setCellValue('K'.$row_data, $pending_total_price - $pending_total_shipping);
            
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('K'.$row_data)->applyFromArray($style_thead);
        }

        /* Cancel Table */
        $row_data += 3;
        $sheet->setCellValue('B'.$row_data, 'TABEL CANCEL HARI INI');
        $spreadsheet->getActiveSheet()->mergeCells('B'.$row_data.':K'.$row_data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$row_data)->applyFromArray($style_title);
        $row_data += 2;
        
        // Cancel headers
        $sheet->setCellValue('B'.$row_data, 'No');
        $sheet->setCellValue('C'.$row_data, 'ID');
        $sheet->setCellValue('D'.$row_data, 'Kurir Pickup');
        $sheet->setCellValue('E'.$row_data, 'Kurir Delivery');
        $sheet->setCellValue('F'.$row_data, 'Kode Resi');
        $sheet->setCellValue('G'.$row_data, 'Nama CS');
        $sheet->setCellValue('H'.$row_data, 'No hp seller');
        $sheet->setCellValue('I'.$row_data, 'Harga');
        $sheet->setCellValue('J'.$row_data, 'Ongkir');
        $sheet->setCellValue('K'.$row_data, 'Keterangan');
        
        for($i = 0; $i <= 9; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_thead);
        }
        $row_data++;
        
        $cancel_urut = 1;
        foreach($sql_cancel_data as $cancel_data){
            $sheet->setCellValue($col_first.$row_data, $cancel_urut++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $cancel_data['daily_sequence_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($cancel_data['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($cancel_data['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($cancel_data['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($cancel_data['cs_name']));
            $sheet->setCellValueExplicit(get_col($col_first, 6).$row_data, $cancel_data['seller_phone_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $cancel_data['price']);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, $cancel_data['shiping_cost']);
            $sheet->setCellValue(get_col($col_first, 9).$row_data, strtoupper($cancel_data['status_delivery']));

            for($i = 0; $i <= 9; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for cancel table
        if($cancel_data_count > 0) {
            $sheet->setCellValue('H'.$row_data, 'TOTAL CANCEL:');
            $sheet->setCellValue('I'.$row_data, $cancel_total_price);
            $sheet->setCellValue('J'.$row_data, $cancel_total_shipping);
            $sheet->setCellValue('K'.$row_data, $cancel_total_price - $cancel_total_shipping);
            
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('K'.$row_data)->applyFromArray($style_thead);
        }
    /* End Tables */ 
/* Body Content */

/* Configuration Save File To Excel */
    $filename = 'Data Delivery.xlsx';
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