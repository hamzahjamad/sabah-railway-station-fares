<?php

require_once 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Tightenco\Collect\Support\Collection;


$url = 'http://railway.sabah.gov.my/index.php/info-jkns/perkhidmatanoperasi-keretapi/harga-tambang/';
$html = file_get_contents($url, FILE_USE_INCLUDE_PATH);
$title =  [
        "DARI STESEN",
        "KE STESEN",
        "HARGA TAMBANG DEWASA (RM)",
        "HARGA TAMBANG KANAK-KANAK (RM)"
];
	
$crawler = new Crawler($html);
$data = $crawler->filter('table')
                 ->filter('tr')
                 ->reduce(function (Crawler $tr, $i) {
                 	 $value = $tr->text();
                 	 $value = preg_replace('/\s+/','',$value);
        
                     return strlen($value) > 0;
                 })
                 ->each(function ($tr, $i) {
						    return $tr->filter('td')
						              ->reduce(function (Crawler $td, $i) {
                 	                	 $text = trim($td->text());
                                         return strlen($text) > 0;
                 	                  })
						              ->each(function ($td, $i) {
						                   return trim($td->text());
						              });
				 });

$mainTitle = [];
$count = 0;				 

$data = (new Collection($data))
        ->map(function ($value, $key) use (&$mainTitle, &$count) {
			$title =  [
			        "DARI STESEN",
			        "KE STESEN",
			        "HARGA TAMBANG DEWASA (RM)",
			        "HARGA TAMBANG KANAK-KANAK (RM)"
			];

			$isAddCount = false;
			$newValue = [];

			if ($value == $title) {
				$newValue = [];
			} else if (count($value) == 4 && $value != $title) {
                $mainTitle[$count] = $value[0];
				$isAddCount = true;
				$newValue["from"] = $value[0];
				$newValue["to"] = $value[1];
				$newValue["price"]["adult"] = $value[2];
				$newValue["price"]["children"] = $value[3];
			} else if(count($value) == 3) {
				if (count($mainTitle) > 0) {
					$newValue["from"] = $mainTitle[$count-1];
				}
				
				$newValue["to"] = $value[0];
				$newValue["price"]["adult"] = $value[1];
				$newValue["price"]["children"] = $value[2];
			} else {	
				$newValue = [];
			}

			if($isAddCount) {
				++$count;
			} 
			

	        return $newValue;
		})
		->filter(function ($value, $key) {
	        return count($value) > 0;
		});

//remove first item        
$data->shift();    

//remove last 6 items
for ($i=0; $i < 3; $i++) { 
    	$data->pop();
}    

$data = $data->toJson(JSON_PRETTY_PRINT);

//$remove weird string;
$data = str_replace('\u00a0', '', $data);
$data = str_replace('\n', '', $data);


$file = 'jkns_fares.json';
file_put_contents($file, $data);