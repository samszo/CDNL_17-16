<?php
header('Content-Type: text/html; charset=utf-8');
$arrUrl = array(
	"THYP1516data"=>"https://docs.google.com/spreadsheets/d/1ED680az81kja4nkUi89PhZldzkdwVrJVF21AwHDf6j8/pub?gid=2078932914&single=true&output=csv"
	,"THYP1617data"=>"https://docs.google.com/spreadsheets/d/1FP1ZqWonLsnLJBX4XY3KUtOJe-v4bY4Se-ZrImGUwos/pub?gid=840118204&single=true&output=csv"
	//,"THYP1617photo"=>"http://picasaweb.google.com/data/feed/base/user/107069028374380439764/albumid/6340716155566922545?alt=rss&kind=photo&hl=en_US"
    ,"THYP1617photo"=>"http://gapai.univ-paris8.fr/jdc/public/flux/google?type=album&userId=107069028374380439764&albumId=6340716155566922545"
    ,"CFI1617data"=>"https://docs.google.com/spreadsheets/d/1tKXAG8mCk7Y-piLDi68DhZzuiQuo8eVyBPr_zMz1Zl8/pub?gid=293749862&single=true&output=csv"
	,"CDNL1617data"=>"https://docs.google.com/spreadsheets/d/1ayDcSA03grBSsh7Wcu65iVNPIUUWFoEGdL_UPa32RXI/pub?gid=76288419&single=true&output=csv"
	,"CDNL1617photo"=>"http://gapai.univ-paris8.fr/jdc/public/flux/google?type=album&userId=117590660096025980525&albumId=6345316040299888033"
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