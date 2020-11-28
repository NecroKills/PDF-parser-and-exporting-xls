<?php
require __DIR__ . '/vendor/autoload.php';

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser(); 
$pdf = $parser->parseFile("ED_6__2019__DPDF_DEFENSOR_RES_PROVISORIO_OBJETIVA.pdf");
$text = $pdf->getText();

// Clean text
$text = preg_replace('/\r?\n|\r/',' ', $text); //clear line break
$text = preg_replace('/\s[\d]\s/','', $text); //clear pagination
$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $text); //clear unknown character

$pattern = "/(\d{8}[,\s\w\.]*)[^\/]?/";
$result = preg_match_all($pattern, $text, $matches);
$count = 0;

foreach($matches[0] as $candidate) {
  $filtered = preg_split('/\s*,\s*/',$candidate); //break in comma space 
  $provisionalNote = preg_split('/\s|\.\s/',$filtered[3]); //break in comma space 
  $filtered[3] = $provisionalNote[0];

  if ($count < 301) {
    $data['Resultado provisório na prova objetiva'][] = [
      "Número de inscrição" => $filtered[0], 
      "Nome do candidato em ordem alfabética" => $filtered[1], 
      "Número de acertos na prova objetiva" => $filtered[2],
      "Nota provisória na prova objetiva" => $filtered[3]
    ];    
  } else {
    $data['Resultado provisório na prova objetiva para as vagas reservadas às pessoas com deficiência'][] = [
      "Número de inscrição" => $filtered[0], 
      "Nome do candidato em ordem alfabética" => $filtered[1], 
      "Número de acertos na prova objetiva" => $filtered[2],
      "Nota provisória na prova objetiva" => $filtered[3]
    ];
  }

  $count++; 
}

$filename =  'ED_6__2019__DPDF_DEFENSOR_RES_PROVISORIO_OBJETIVA'.'.xls';    
header("Content-Type: application/vnd.ms-excel;");
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=Customers_Export.csv');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

ExportFile($data);
function ExportFile($data) {
  $heading = false;
  if(!empty($data)) {
    foreach($data as $key => $typeVacancy){
      echo $key . PHP_EOL.PHP_EOL;
      foreach($typeVacancy as $row) {
        if(!$heading) {
          // display field/column names as a first row
          echo implode(",", array_keys($row)) . PHP_EOL;
          $heading = true;
        }
        echo implode(",", array_values($row)) . PHP_EOL;
      } 
      echo PHP_EOL;
    }
  }
  exit;
}

