<?php
ini_set('display_errors', 0);

$user_phone_number = (int) $argv[1];
$url_get_id = (string) 'https://manager.sohonet.ua/api/clients/check-number?number=';
$url_get_amount_of_money_by_id  = (string) 'https://manager.sohonet.ua/api/clients/check-number?number=';
$ids = array();
$object = get_data_from_url($url_get_id, $user_phone_number);

if (strpos($object ,'Check number')) {
    echo "Информация отсутствует";
    return;
}
echo $object;

$object = json_decode($object, true);

$ids = get_id_from_array($object);

print_r($ids);

foreach ($object as $key => $value) {
    var_dump($key) ;
    echo '=====' ;
    var_dump($value);
 #   var_dump($object);
}
var_dump($ids);
function get_id_from_array($arra) {
    $result = array();
    foreach ($arra as $key => $value) {

        if (is_array($value)){
            echo "in";
            get_id_from_array($value);
        }elseif ($key == "id") {
            echo "out==============". $value . "======================================";
            array_push($result, $value);
            var_dump($result);
        }
    }
    return (array) $result;
}
function get_data_from_url($url, $value){
    $curl_req = curl_init();

    curl_setopt($curl_req, CURLOPT_USERAGENT, filter_input(INPUT_SERVER, 'HTTP_USER_AGENT',
        FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
    curl_setopt($curl_req, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl_req, CURLOPT_HEADER, 0);
    curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_req, CURLOPT_URL, $url . $value);
    curl_setopt($curl_req, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($curl_req);

    curl_close($curl_req);

    return $data;
}

echo "плюшка";
?>

