<?php

namespace frontend\controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class AmoController
{
    /**
     * @var Client
     */
    public $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://' . \Yii::$app->amo->domain . '.amocrm.ru/',
            'cookies' => true,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);
        $this->auth();
    }

    private function auth()
    {
        $body = json_encode([
            'USER_LOGIN' => \Yii::$app->amo->login,
            'USER_HASH' => \Yii::$app->amo->hash,
        ]);

        $authRequest = new Request('POST', 'private/api/auth.php?type=json', [], $body);
        $response = $this->client->send($authRequest);
    }

    public function getLeads()
    {
        $leads = new Request('GET', 'api/v2/leads');
        return json_decode($this->client->send($leads)->getBody(), true);
    }
}
