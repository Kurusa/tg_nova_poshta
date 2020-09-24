<?php

namespace App\TgHelpers;

use Dotenv\Dotenv;

/**
 * @package App\TgHelpers
 */
class NovaPoshtaApi
{

    public $result;
    public $curl;

    public $API = 'https://api.novaposhta.ua/v2.0/json';

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->curl = curl_init();
    }

    public function api(string $model_name, string $called_method, array $params)
    {
        $url = $this->API . '/' . $model_name . '/' . $called_method;
        $params['modelName'] = $model_name;
        $params['calledMethod'] = $called_method;
        $params['apiKey'] = env('NOVA_POSHTA_TOKEN');
        return $this->do($url, $params);
    }

    private function do($url, array $params = []): ?array
    {
        $params = json_encode($params);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', 'Content-Length: ' . strlen($params),
        ]);

        $this->result = json_decode(curl_exec($this->curl), TRUE);
        return $this->result;
    }

    public function getCities(string $search_string)
    {
        return $this->api('Address', 'getCities', [
            'methodProperties' => [
                'FindByString' => $search_string,
            ]
        ]);
    }

    public function getCityByRef(string $city_ref)
    {
        return $this->api('Address', 'getCities', [
            'methodProperties' => [
                'Ref' => $city_ref,
            ]
        ]);
    }

    public function getWarehouses(string $city_id)
    {
        return $this->api('AddressGeneral', 'getWarehouses', [
            'methodProperties' => [
                'CityRef' => $city_id,
            ]
        ]);
    }

    public function getWarehouseByRef(string $city_id, string $post_id)
    {
        $settlements_list = $this->getWarehouses($city_id);
        if ($settlements_list['success']) {
            foreach ($settlements_list['data'] as $settlement) {
                if ($settlement['Ref'] == $post_id) {
                    return $settlement['DescriptionRu'];
                }
            }
        }
        return null;
    }

    public function __destruct()
    {
        $this->curl = curl_close($this->curl);
    }

}