<?php

// Function to make a GET request using cURL
function makeApiRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function scrapeData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// API URLs
$api_key = 'KRFEAKW6HS5Z6TMDXRCKIM2QCY98UKI2NN';
$pop_total_url = "https://api.basescan.org/api?module=stats&action=tokensupply&contractaddress=0xe12acF5BB21654195a498c2FBd49fFf801A3A02d&apikey=$api_key";
$pop_burn_url = "https://api.basescan.org/api?module=account&action=tokenbalance&contractaddress=0xe12acF5BB21654195a498c2FBd49fFf801A3A02d&address=0x000000000000000000000000000000000000dead&apikey=$api_key";
$pop_info_url = "https://api.dexscreener.com/latest/dex/tokens/0xe12acF5BB21654195a498c2FBd49fFf801A3A02d";
$pop_holders_url = "https://basescan.org/token/0xe12acF5BB21654195a498c2FBd49fFf801A3A02d";


// Fetch API responses
$pop_total_response = makeApiRequest($pop_total_url);
$pop_burn_response = makeApiRequest($pop_burn_url);
$pop_info_response = makeApiRequest($pop_info_url);
$pop_holders_response = scrapeData($pop_holders_url);


// Decode JSON responses
$pop_total_data = json_decode($pop_total_response);
$pop_burn_data = json_decode($pop_burn_response);
$pop_info_data = json_decode($pop_info_response);

// Calculate Circulating Supply
$pop_circulating_data = $pop_total_data->result / 1e18 - $pop_burn_data->result / 1e18;

// echo $pop_balance_data->pairs[0]->priceUsd; die();

// Convert balances to human-readable format
$pop_total_number = number_format($pop_total_data->result / 1e18, 0, '.');
$pop_burn_number = number_format($pop_burn_data->result / 1e18, 2, '.');
$pop_circulating_number = number_format($pop_circulating_data, 2, '.');
$pop_price = number_format($pop_info_data->pairs[0]->priceUsd, 4);
$pop_market_cap = number_format($pop_info_data->pairs[0]->marketCap);


// Parse the holders count from the HTML
if (preg_match('/<div id="ContentPlaceHolder1_tr_tokenHolders">.*?<div class="d-flex flex-wrap gap-2">\s*<div>\s*(\d+)\s*<\/div>/s', $pop_holders_response, $matches)) {
   $pop_holders = number_format($matches[1]);
} else {
    echo "Unable to scrape holders data.";
}

// Output results
// echo "Total Supply: $pop_total_number\n";
// echo "Circulating Supply: $pop_circulating_number\n";
echo json_encode(["result" => $pop_burn_number], JSON_PRETTY_PRINT);
// echo "Price: $pop_price\n";
// echo "Market Cap: $pop_market_cap\n";
// echo "Holders: $pop_holders\n";


