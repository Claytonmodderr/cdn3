<?php
if(isset($_GET['list'])){

$domain = "https://cdnapi-hazel.vercel.app/record/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://service-playplus.ottvs.com.br/v1/android/FindLiveGridByGroup');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"AuthenticationTicket":"playplus@playplus:l2131ht!@#","LiveGroupId":-1,"State":"SP"}');

$headers = [
    'User-Agent: okhttp/4.9.1',
    'Accept: application/json',
    'Content-Type: application/json',
    'Connection: keep-alive'
];

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if(curl_errno($ch)){
    echo "Erro CURL: " . curl_error($ch);
    exit;
}

curl_close($ch);

// DEBUG mostrar retorno da API
// echo $result;

$json = json_decode($result, true);

// DEBUG verificar estrutura
// print_r($json);

if (!isset($json['FindLiveGridByGroupResult']['liveEventGrids'])) {
    echo "❌ Estrutura JSON não encontrada!";
    exit;
}

$output = [];
$i = 0;

foreach ($json['FindLiveGridByGroupResult']['liveEventGrids'] as $item) {
    $output[] = [
        'Name' => $item['Name'] ?? '',
        'urlHLS' => $item['urlHLS'] ?? '',
        'urlHLSBackup' => $item['urlHLSBackup'] ?? '',
        'urlHLSChromecast' => $item['urlHLSChromecast'] ?? '',
        'link' => $domain.'play.php?v='.$i
    ];
    $i++;
}

echo json_encode($output);

}
?>
