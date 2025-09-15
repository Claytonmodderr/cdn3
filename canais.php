<?php
if(isset($_GET['list'])){
$domain = "https://cdnapimega.lat/record/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://service-playplus.ottvs.com.br/v1/android/FindLiveGridByGroup');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"AuthenticationTicket\":\"playplus@playplus:l2131ht!@#\",\"LiveGroupId\":-1,\"State\":\"SP\"}");
$headers = array();
//$headers[] = 'User-Agent: okhttp/4.9.1';
$headers[] = 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
//echo $result;
curl_close($ch);

$json = json_decode($result, true);
$output = array();

$i = 0;
foreach ($json['FindLiveGridByGroupResult']['liveEventGrids'] as $item) {
    $entry = array(
        'Name' => $item['Name'],
        'urlHLS' => $item['urlHLS'],
        'urlHLSBackup' => $item['urlHLSBackup'],
        'urlHLSChromecast' => $item['urlHLSChromecast'],
        'link' => $domain.'play.php?v='.$i,
    );
    $output[] = $entry;
$i++;
}

echo json_encode($output);

}