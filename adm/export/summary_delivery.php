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
        $query_data     = "SELECT
            dlv_pickup.id AS pickup_id,
            trx_delivery.id AS delivery_id,
            dlv_pickup.pickup_date,
            trx_delivery.delivery_date,
            dlv_pickup.kurir_id AS kurir_pick_up_id,
            mst_kurir.kurir_name AS kurir_pick_up,
            CASE
                WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                ELSE '-'
            END AS kurir_delivery,
            trx_delivery.kurir_id AS kurir_delivery_id,
            dlv_pickup.resi_code,
            dlv_pickup.cs_name,
            CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
            dlv_pickup.price,
            dlv_pickup.shiping_cost,
            dlv_pickup.status_pickup,
            trx_delivery.status_delivery
        FROM dlv_pickup 
            JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
            LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
        WHERE 1=1";
    /* Query Data */

    /* Jika Pencarian Aktif */
        $kurir_id           = ($_GET['kurir'] ?? "") != "" ? $_GET['kurir'] : '';
        $status             = ($_GET['status'] ?? "") != "" ? $_GET['status'] : '';
        $date_from          = ($_GET['from'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
        $date_to            = ($_GET['to'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
        $date_now           = date('Y-m-d');

        $query_data         .= ($_GET['kurir'] ?? "") ? " AND trx_delivery.kurir_id='$kurir_id'" : "";
        $query_data         .= ($_GET['status'] ?? "") ? " AND trx_delivery.status_delivery='$status'" : "";
        $query_data         .= ($_GET['from'] ?? "") != "" && ($_GET['to'] ?? "") != "" ? " AND trx_delivery.delivery_date BETWEEN '$date_from' AND '$date_to'" : " AND trx_delivery.delivery_date='$date_now'";
    /* Jika Pencarian Aktif */
    
    /* Menampilkan Data */
        $sql_top            = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='SUKSES' ORDER BY trx_delivery.id ASC");
        $all_data_top       = mysqli_num_rows($sql_top);
        $no_urut_top        = 1;
        $array_sumprice[]       = 0;
        $array_sumcost[]        = 0;
        $array_sumtotal_price[] = 0;
        foreach($sql_top as $row){
            $pricetop              = $row['price'];
            $shipingcost_top       = $row['shiping_cost'];
            $totalprice_top        = $row['price']+$row['shiping_cost'];

            $array_sumprice[]       = $pricetop;
            $array_sumcost[]        = $shipingcost_top;
            $array_sumtotal_price[] = $totalprice_top;
        }
        $sum_price          = (($all_data_top > 0) ? array_sum($array_sumprice) : 0);
        $sum_cost           = (($all_data_top > 0) ? array_sum($array_sumcost) : 0);
        $sum_price_cost     = (($all_data_top > 0) ? array_sum($array_sumtotal_price) : 0);

        $sql_pending        = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='PENDING' ORDER BY trx_delivery.id ASC");
        $all_data_pending   = mysqli_num_rows($sql_pending);
        $no_urut_pending    = 1;
        
        $sql_cancel         = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='CANCEL' ORDER BY trx_delivery.id ASC");
        $all_data_cancel    = mysqli_num_rows($sql_cancel);
        $no_urut_cancel     = 1;

        $query_no_delivery  = "SELECT
            dlv_pickup.id AS pickup_id,
            dlv_pickup.pickup_date,
            dlv_pickup.kurir_id AS kurir_pick_up_id,
            mst_kurir.kurir_name AS kurir_pick_up,
            dlv_pickup.resi_code,
            dlv_pickup.cs_name,
            dlv_pickup.price,
            dlv_pickup.shiping_cost
        FROM dlv_pickup 
            JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
            LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
        WHERE trx_delivery.id IS NULL
            -- AND dlv_pickup.pickup_date='$date_now'
        ORDER BY dlv_pickup.id ASC";
        
        $sql_no_delivery    = mysqli_query($con, $query_no_delivery);
        $all_data_no_delivery = mysqli_num_rows($sql_no_delivery);
        $no_urut_no_delivery = 1;
    /* Menampilkan Data */
/* Get Data */ 

/* STYLE CSS SHEET */
    $style_title    = [
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
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

/* Header Content - Table 1: Hasil Delivery Hari Ini */
    $sheet->setCellValue('B2', strtoupper('Hasil Delivery Hari Ini'));
    // Format Header Content
        $spreadsheet->getActiveSheet()->mergeCells('B2:J2');
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($style_title);
    // Format Header Content
/* Header Content - Table 1 */

/* Body Content - Table 1 */
    $sheet->setCellValue('B3', strtoupper('No'));
    $sheet->setCellValue('C3', strtoupper('ID'));
    $sheet->setCellValue('D3', strtoupper('Kurir Pickup'));
    $sheet->setCellValue('E3', strtoupper('Kurir Delivery'));
    $sheet->setCellValue('F3', strtoupper('Kode Resi'));
    $sheet->setCellValue('G3', strtoupper('Nama CS'));
    $sheet->setCellValue('H3', strtoupper('Harga'));
    $sheet->setCellValue('I3', strtoupper('Ongkir'));
    $sheet->setCellValue('J3', strtoupper('Keterangan'));
    
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

        $spreadsheet->getActiveSheet()->getStyle('B3')->applyFromArray($style_thead);
        $spreadsheet->getActiveSheet()->getStyle('C3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('D3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('E3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('F3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('G3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('H3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('I3')->applyFromArray($style_thead_name);
        $spreadsheet->getActiveSheet()->getStyle('J3')->applyFromArray($style_thead_name);
    /* Thead Set Style */
    
    /* Table 1: Hasil Delivery Hari Ini - SUKSES Status */
        $row_data = 4;
        $col_first = 'B';
        $array_sum_price_top[] = 0;
        $array_sum_cost_top[] = 0;
        
        foreach($sql_top as $row_top){
            $price_top = $row_top['price'];
            $shiping_cost_top = $row_top['shiping_cost'];
            
            $array_sum_price_top[] = $row_top['price'];
            $array_sum_cost_top[] = $row_top['shiping_cost'];

            $sheet->setCellValue($col_first.$row_data, $no_urut_top++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $row_top['pickup_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($row_top['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($row_top['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($row_top['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($row_top['cs_name']));
            $sheet->setCellValue(get_col($col_first, 6).$row_data, $price_top);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $shiping_cost_top);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper($row_top['status_delivery']));

            for($i = 0; $i <= 8; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for Table 1
        if($all_data_top > 0) {
            $jumlah_price_top = array_sum($array_sum_price_top);
            $jumlah_cost_top = array_sum($array_sum_cost_top);
            $jumlah_difference_top = $jumlah_price_top - $jumlah_cost_top;
            
            $sheet->setCellValue('G'.$row_data, 'TOTAL:');
            $sheet->setCellValue('H'.$row_data, $jumlah_price_top);
            $sheet->setCellValue('I'.$row_data, $jumlah_cost_top);
            $sheet->setCellValue('J'.$row_data, $jumlah_difference_top);
            
            $spreadsheet->getActiveSheet()->getStyle('G'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
        }
        $row_data += 3;

        /* Table 2: Tabel Pending Hari Ini */
        $sheet->setCellValue('B'.$row_data, 'TABEL PENDING HARI INI');
        $spreadsheet->getActiveSheet()->mergeCells('B'.$row_data.':J'.$row_data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$row_data)->applyFromArray($style_title);
        $row_data += 2;
        
        // Pending headers
        $sheet->setCellValue('B'.$row_data, 'No');
        $sheet->setCellValue('C'.$row_data, 'ID');
        $sheet->setCellValue('D'.$row_data, 'Kurir Pickup');
        $sheet->setCellValue('E'.$row_data, 'Kurir Delivery');
        $sheet->setCellValue('F'.$row_data, 'Kode Resi');
        $sheet->setCellValue('G'.$row_data, 'Nama CS');
        $sheet->setCellValue('H'.$row_data, 'Harga');
        $sheet->setCellValue('I'.$row_data, 'Ongkir');
        $sheet->setCellValue('J'.$row_data, 'Keterangan');
        
        for($i = 0; $i <= 8; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_thead);
        }
        $row_data++;
        
        $array_sum_price_pending[] = 0;
        $array_sum_cost_pending[] = 0;
        
        foreach($sql_pending as $row_pending){
            $price_pending = $row_pending['price'];
            $shiping_cost_pending = $row_pending['shiping_cost'];
            
            $array_sum_price_pending[] = $row_pending['price'];
            $array_sum_cost_pending[] = $row_pending['shiping_cost'];

            $sheet->setCellValue($col_first.$row_data, $no_urut_pending++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $row_pending['pickup_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($row_pending['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($row_pending['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($row_pending['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($row_pending['cs_name']));
            $sheet->setCellValue(get_col($col_first, 6).$row_data, $price_pending);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $shiping_cost_pending);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper($row_pending['status_delivery']));

            for($i = 0; $i <= 8; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for pending table
        if($all_data_pending > 0) {
            $jumlah_price_pending = array_sum($array_sum_price_pending);
            $jumlah_cost_pending = array_sum($array_sum_cost_pending);
            $jumlah_difference_pending = $jumlah_price_pending - $jumlah_cost_pending;
            
            $sheet->setCellValue('G'.$row_data, 'JUMLAH:');
            $sheet->setCellValue('H'.$row_data, $jumlah_price_pending);
            $sheet->setCellValue('I'.$row_data, $jumlah_cost_pending);
            $sheet->setCellValue('J'.$row_data, $jumlah_difference_pending);
            
            $spreadsheet->getActiveSheet()->getStyle('G'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
        }
        $row_data += 3;

        /* Table 3: Tabel Cancel Hari Ini */
        $sheet->setCellValue('B'.$row_data, 'TABEL CANCEL HARI INI');
        $spreadsheet->getActiveSheet()->mergeCells('B'.$row_data.':J'.$row_data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$row_data)->applyFromArray($style_title);
        $row_data += 2;
        
        // Cancel headers
        $sheet->setCellValue('B'.$row_data, 'No');
        $sheet->setCellValue('C'.$row_data, 'ID');
        $sheet->setCellValue('D'.$row_data, 'Kurir Pickup');
        $sheet->setCellValue('E'.$row_data, 'Kurir Delivery');
        $sheet->setCellValue('F'.$row_data, 'Kode Resi');
        $sheet->setCellValue('G'.$row_data, 'Nama CS');
        $sheet->setCellValue('H'.$row_data, 'Harga');
        $sheet->setCellValue('I'.$row_data, 'Ongkir');
        $sheet->setCellValue('J'.$row_data, 'Keterangan');
        
        for($i = 0; $i <= 8; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_thead);
        }
        $row_data++;
        
        $array_sum_price_cancel[] = 0;
        $array_sum_cost_cancel[] = 0;
        
        foreach($sql_cancel as $row_cancel){
            $price_cancel = $row_cancel['price'];
            $shiping_cost_cancel = $row_cancel['shiping_cost'];
            
            $array_sum_price_cancel[] = $row_cancel['price'];
            $array_sum_cost_cancel[] = $row_cancel['shiping_cost'];

            $sheet->setCellValue($col_first.$row_data, $no_urut_cancel++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $row_cancel['pickup_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($row_cancel['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, strtoupper($row_cancel['kurir_delivery']));
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($row_cancel['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($row_cancel['cs_name']));
            $sheet->setCellValue(get_col($col_first, 6).$row_data, $price_cancel);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $shiping_cost_cancel);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, strtoupper($row_cancel['status_delivery']));

            for($i = 0; $i <= 8; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for cancel table
        if($all_data_cancel > 0) {
            $jumlah_price_cancel = array_sum($array_sum_price_cancel);
            $jumlah_cost_cancel = array_sum($array_sum_cost_cancel);
            $jumlah_difference_cancel = $jumlah_price_cancel - $jumlah_cost_cancel;
            
            $sheet->setCellValue('G'.$row_data, 'JUMLAH:');
            $sheet->setCellValue('H'.$row_data, $jumlah_price_cancel);
            $sheet->setCellValue('I'.$row_data, $jumlah_cost_cancel);
            $sheet->setCellValue('J'.$row_data, $jumlah_difference_cancel);
            
            $spreadsheet->getActiveSheet()->getStyle('G'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
        }
        $row_data += 3;

        /* Table 4: Tabel Belum Input Kurir Delivery */
        $sheet->setCellValue('B'.$row_data, 'TABEL BELUM INPUT KURIR DELIVERY');
        $spreadsheet->getActiveSheet()->mergeCells('B'.$row_data.':J'.$row_data);
        $spreadsheet->getActiveSheet()->getStyle('B'.$row_data)->applyFromArray($style_title);
        $row_data += 2;
        
        // No delivery headers
        $sheet->setCellValue('B'.$row_data, 'No');
        $sheet->setCellValue('C'.$row_data, 'ID');
        $sheet->setCellValue('D'.$row_data, 'Kurir Pickup');
        $sheet->setCellValue('E'.$row_data, 'Kurir Delivery');
        $sheet->setCellValue('F'.$row_data, 'Kode Resi');
        $sheet->setCellValue('G'.$row_data, 'Nama CS');
        $sheet->setCellValue('H'.$row_data, 'Harga');
        $sheet->setCellValue('I'.$row_data, 'Ongkir');
        $sheet->setCellValue('J'.$row_data, 'Keterangan');
        
        for($i = 0; $i <= 8; $i++) {
            $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_thead);
        }
        $row_data++;
        
        $array_sum_price_no_delivery[] = 0;
        $array_sum_cost_no_delivery[] = 0;
        
        foreach($sql_no_delivery as $row_no_delivery){
            $price_no_delivery = $row_no_delivery['price'];
            $shiping_cost_no_delivery = $row_no_delivery['shiping_cost'];
            
            $array_sum_price_no_delivery[] = $row_no_delivery['price'];
            $array_sum_cost_no_delivery[] = $row_no_delivery['shiping_cost'];

            $sheet->setCellValue($col_first.$row_data, $no_urut_no_delivery++);
            $sheet->setCellValue(get_col($col_first, 1).$row_data, $row_no_delivery['pickup_id']);
            $sheet->setCellValue(get_col($col_first, 2).$row_data, strtoupper($row_no_delivery['kurir_pick_up']));
            $sheet->setCellValue(get_col($col_first, 3).$row_data, '-');
            $sheet->setCellValue(get_col($col_first, 4).$row_data, strtoupper($row_no_delivery['resi_code']));
            $sheet->setCellValue(get_col($col_first, 5).$row_data, strtoupper($row_no_delivery['cs_name']));
            $sheet->setCellValue(get_col($col_first, 6).$row_data, $price_no_delivery);
            $sheet->setCellValue(get_col($col_first, 7).$row_data, $shiping_cost_no_delivery);
            $sheet->setCellValue(get_col($col_first, 8).$row_data, 'PROSES');

            for($i = 0; $i <= 8; $i++) {
                $spreadsheet->getActiveSheet()->getStyle(get_col($col_first, $i).$row_data)->applyFromArray($style_tbody);
            }
            $row_data++;
        }
        
        // Add totals for no delivery table
        if($all_data_no_delivery > 0) {
            $jumlah_price_no_delivery = array_sum($array_sum_price_no_delivery);
            $jumlah_cost_no_delivery = array_sum($array_sum_cost_no_delivery);
            $jumlah_difference_no_delivery = $jumlah_price_no_delivery - $jumlah_cost_no_delivery;
            
            $sheet->setCellValue('G'.$row_data, 'JUMLAH:');
            $sheet->setCellValue('H'.$row_data, $jumlah_price_no_delivery);
            $sheet->setCellValue('I'.$row_data, $jumlah_cost_no_delivery);
            $sheet->setCellValue('J'.$row_data, $jumlah_difference_no_delivery);
            
            $spreadsheet->getActiveSheet()->getStyle('G'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('H'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row_data)->applyFromArray($style_thead);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row_data)->applyFromArray($style_thead);
        }
    /* End All Tables */

/* Configuration Save File To Excel */
    $writer = new Xlsx($spreadsheet);
    $writer->save('Data Summary Delivery.xlsx');
    header('Content-type: application/xlsx');

    header('Content-Disposition: attachment; filename="Data Summary Delivery.xlsx"'); // It will be called downloaded
    readfile('Data Summary Delivery.xlsx'); // The PDF source is in original
/* Configuration Save File To Excel */
?>