<?php

// Sheet Data CSV URL
$dataSpreadsheetUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQbdf2Vk_nYGZutul0IU7f15iNSX7HTgtyg779575Up9QOfHt5Hlkf8pQefsMLr-9n95jwEMC9UPTul/pub?output=csv";


$rowCount = 0;
$features = array();
$error = FALSE;
$output = array();

// attempt to set the socket timeout, if it fails then echo an error
if (!ini_set('default_socket_timeout', 15)) {
    $output = array('error' => 'Unable to Change PHP Socket Timeout');
    $error = TRUE;
} // end if, set socket timeout

// if the opening the CSV file handler does not fail
if (!$error && (($dataHandle = fopen($dataSpreadsheetUrl, "r")) !== FALSE)) {
    // while CSV has data, read up to 10000 rows
    while (($csvRow = fgetcsv($dataHandle, 10000, ",")) !== FALSE) {
        $rowCount++;
        if ($rowCount == 1) {
            continue;
        } // skip the first/header row of the CSV

        $output[] = array(
            'features' => array(
                'nomor' => $csvRow[0],
                'kelurahan_99' => $csvRow[1],
                'kecamatan_99' => $csvRow[2],
                'nib_99' => $csvRow[3],
                'nomor_hak_99' => $csvRow[4],
                'surat_ukur_99' => $csvRow[5],
                'tipe_hak_99' => $csvRow[6],
                'luas_bidang_99' => $csvRow[7],
                'nomor_kib_99' => $csvRow[8],
                'nomor_register_99' => $csvRow[9],
                'penggunaan_tanah_99' => $csvRow[10],
                'rtrw_99' => $csvRow[11],
                'rdtr_99' => $csvRow[12],
                'pemilik_tanah' => $csvRow[13],
                'koordinatx' => $csvRow[14],
                'koordinaty' => $csvRow[15],
            )
        );
    } // end while, loop through CSV data

    fclose($dataHandle); // close the CSV file handler

}  // end if , read file handler opened

// else, file didn't open for reading
else {
    $output = array('error' => 'Problem Reading Google CSV');
}  // end else, file open fail

//var_dump ($output);
//die();


//Read geojson file
$geojsonAdmin = file_get_contents("bidang_aset.geojson");
$polygonAdmin = json_decode($geojsonAdmin, TRUE);

// var_dump ($polygonAdmin);
// die();

foreach ($polygonAdmin['features'] as $key => $first_value) {
    foreach ($output as $second_value) {
        if ($first_value['properties']['ID'] == $second_value['features']['kelurahan_99']) {
            $polygonAdmin['features'][$key]['properties']['kelurahan_99'] = $second_value['features']['kelurahan_99'];
            $polygonAdmin['features'][$key]['properties']['kecamatan_99'] = $second_value['features']['kecamatan_99'];
            $polygonAdmin['features'][$key]['properties']['nib_99'] = $second_value['features']['nib_99'];
            $polygonAdmin['features'][$key]['properties']['nomor_hak_99'] = $second_value['features']['nomor_hak_99'];
            $polygonAdmin['features'][$key]['properties']['surat_ukur_99'] = $second_value['features']['surat_ukur_99'];
            $polygonAdmin['features'][$key]['properties']['tipe_hak_99'] = $second_value['features']['tipe_hak_99'];
            $polygonAdmin['features'][$key]['properties']['luas_bidang_99'] = $second_value['features']['luas_bidang_99'];
            $polygonAdmin['features'][$key]['properties']['nomor_kib_99'] = $second_value['features']['nomor_kib_99'];
            $polygonAdmin['features'][$key]['properties']['nomor_register_99'] = $second_value['features']['riwayat'];
            $polygonAdmin['features'][$key]['properties']['penggunaan_tanah_99'] = $second_value['features']['penggunaan_tanah_99'];
            $polygonAdmin['features'][$key]['properties']['rtrw_99'] = $second_value['features']['rtrw_99'];
            $polygonAdmin['features'][$key]['properties']['rdtr_99'] = $second_value['features']['rdtr_99'];
            $polygonAdmin['features'][$key]['properties']['pemilik_tanah'] = $second_value['features']['pemilik_tanah'];
            $polygonAdmin['features'][$key]['properties']['koordinatx'] = $second_value['features']['koordinatx'];
            $polygonAdmin['features'][$key]['properties']['koordinaty'] = $second_value['features']['koordinaty'];
        } else {
        }
    }
}

$combined_output = json_encode($polygonAdmin, JSON_NUMERIC_CHECK);

header("Access-Control-Allow-Origin: *");
// header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json');
echo $combined_output;
?>
