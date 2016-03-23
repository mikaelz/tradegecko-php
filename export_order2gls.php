<?php
/**
 * Output CSV file for GLS software import
 **/

require __DIR__.'/config.php';

$header = array(
    'Content-type: application/json',
    'Authorization: Bearer '.TG_PRIVILIGED_CODE,
);

$get_days = isset($_GET['days']) ? (int) $_GET['days'] : 1;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, TG_API_URL.'/orders?created_at_min='.date('Y-m-d', strtotime('-'.$get_days.' days')));
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = json_decode(curl_exec($ch));
curl_close($ch);

$csv = array(
   'dobirka',
   'jmeno prijemce',
   'ulice prijemce',
   'mìsto prijemce',
   'psc prijemce',
   'stat prijemce',
   'telefon prijemce',
   'jmeno odesilatele',
   'ulice odesilatele',
   'mìsto odesilatele',
   'psc odesilatele',
   'stat odesilatele',
   'kontakt na odesilatele',
   'var symbol',
   'ref cislo',
   'pocet baliku',
   'sms cislo',
   'email prijemce',
   'sluzba',
   'obsah',
   'vaha',
);

$csv_file = 'gls-'.date('YmdHis').'.csv';
$fcsv = fopen($csv_file, 'w');
fputcsv($fcsv, $csv, ';');
foreach ($response->orders as $order) {
    if ('packed' != $order->packed_status) {
        continue;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TG_API_URL.'/addresses/'.$order->billing_address_id);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $billing_address = json_decode(curl_exec($ch));
    $billing_address = $billing_address->address;
    curl_close($ch);

    if (empty($billing_address->country)) {
        $billing_address->country = 'SK';
    }

    $csv = array(
       $order->total, // dobirka
       $billing_address->first_name.' '.$billing_address->last_name, // jmeno prijemce
       $billing_address->address1, // ulice prijemce
       $billing_address->city, // mìsto prijemce
       $billing_address->zip_code, // psc prijemce
       $billing_address->country, // stat prijemce
       $order->phone_number, // telefon prijemce
       $GLOBALS['sender']['name'], // jmeno odesilatele
       $GLOBALS['sender']['street'], // ulice odesilatele
       $GLOBALS['sender']['city'], // mìsto odesilatele
       $GLOBALS['sender']['zip'], // psc odesilatele
       $GLOBALS['sender']['country'], // stat odesilatele
       "'".str_replace(' ', '', $GLOBALS['sender']['phone'])."'", // kontakt na odesilatele
       $order->order_number, // var symbol
       $GLOBALS['sender']['ref_number'], // ref cislo
       1, // pocet baliku
       "'".str_replace(' ', '', $order->phone_number)."'", // sms cislo
       $order->email, // email prijemce
       '', // sluzba
       'Order', // obsah
       '', // vaha
    );
    fputcsv($fcsv, $csv, ';');
}
fclose($fcsv);

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$csv_file);
header('Pragma: no-cache');
readfile(__DIR__.'/'.$csv_file);
