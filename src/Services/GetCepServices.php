<?php

namespace App\Services;

use Cake\Http\Client;

class GetCepServices {
    public function findCep(string $cep) {
        $client = new Client();
        $response = $client->get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->getStatusCode() === 200) {
            $body = $response->getJson();
            return $body;
        } else {
            return null;
        }
    }
}
