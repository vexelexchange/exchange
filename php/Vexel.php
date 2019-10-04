<?php


class Vexel
{

    public $baseUrl = 'https://api.vexel.exchange/rest';
    public $timeout = 20;

    private $apiKey = 'QZbfQFbgdzPQrnMQYiMlQCJ7XKEvBmnJTLcUC2C3bK9H1onpsd5YcJcM7Y3JO5z9';
    private $secretKey = 'hYlhWInpIZ7VXl5P4n3aCuygLImY3I_jCTiiLEbsq000f39w0tBBOyKMQ4LoC_4z';
    private $version;


    public function __construct($version = 1)
    {
        $this->version = $version;
    }

    public function query($method, $data = null, $httpType = 'GET', $signed = false)
    {
        $url = rtrim($this->baseUrl, '/') . '/v'.$this->version . '/' . trim($method, '/');
        $headers = ['Content-Type' => 'application/json'];

        if ($signed) {
            $timestamp = (time() * 1000);

            $data['timestamp'] = $timestamp;
            $data['signature'] = $this->createSignature($data);

            $headers['X-VEXEL-APIKEY'] = $this->apiKey;
        }
        $query = is_array($data) ? json_encode($data) : $data;

        $opt = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FILETIME       => true,
            CURLOPT_CUSTOMREQUEST  => $httpType,
        ];
        if ($query) {
            $opt += [
                CURLOPT_POSTFIELDS => $query
            ];
        }

        $curlheaders = [];
        foreach ($headers as $name => $value) {
            $curlheaders[] = $name . ': ' . $value;
        }
        $opt += [
            CURLOPT_HTTPHEADER => $curlheaders,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $opt);

        $r = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        $ret = json_decode($r, true);

        if ($ret === false || $info['http_code'] != 200) {
            throw new Exception(__METHOD__ . ':' . $url . ' ' . $query . PHP_EOL . $r);
        }
//        echo $url . ' ' . $query . PHP_EOL;

        return $ret;
    }

    public function createSignature($data)
    {
        $signature = json_encode($data);
        $hash = hash_hmac('sha256', $signature, $this->secretKey, false);

        return $hash;
    }

}
