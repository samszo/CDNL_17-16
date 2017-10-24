<?php
header('Content-Type: text/html; charset=utf-8');
$arrUrl = array(
	"CDNL1718photo"=>"http://gapai.univ-paris8.fr/jdc/public/flux/google?type=album&userId=117590660096025980525&albumId=6345316040299888033"
    ,"THYP1718photo"=>"http://gapai.univ-paris8.fr/jdc/public/flux/google?type=album&userId=117590660096025980525&albumId=6472381997322565313"
    ,"THYP1718data"=>"https://docs.google.com/spreadsheets/d/e/2PACX-1vQxmWDytc5hSTaF-V-96gefaJxHJWnLGS7xudeNJChpgpvqWdskujnlt03TkiWRHtW5uoTV8sYAH3HZ/pub?gid=642939185&single=true&output=csv"
    
);
//$_GET['url'] = 	"THYP1617photo";
curl($arrUrl[$_GET['url']]);
function curl($url){
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($handle);
    curl_close($handle);
    
    echo $response;
}