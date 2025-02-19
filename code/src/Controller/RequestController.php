<?php
/*
Author
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use \GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class RequestController extends AbstractController
{
    public function start(): Response
    {


            $this->sendRequest2();




        return new Response("started");
    }
    private function sendRequest2(){


        $client = new GuzzleHttp\Client(['base_uri' => 'http://testsym.local']);
        $promise = $client->requestAsync('GET', '/world/answer?index=4');
        $promise->then(
            function (ResponseInterface $res) {
                print 1;
                echo $res->getStatusCode() . "\n";
                print_r($res);
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );
    }
    private function sendRequest(){
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://testsym.local',
        ]);

        // Список адресов
        $urls = [
            '/world/answer?index = 4',
            '/world/answer?index = 5',

        ];

        $promises = [];

        foreach($urls as $urlIndex => $url) {
            $request = new \GuzzleHttp\Psr7\Request('GET', $url, []);

            echo date('d.m.Y H:i:s') . ' запрос ' . $url . PHP_EOL;

            $promises[$urlIndex] = $client->sendAsync($request, [
                'timeout' => 10,
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) use ($url) {
                    // Тут можно получить статистику запроса
                    $stat = $stats->getHandlerStats();
                    echo date('d.m.Y H:i:s') . ' получена статистика ' . $url . PHP_EOL;
                }
            ]);

            $promises[$urlIndex]->then(
                function (\Psr\Http\Message\ResponseInterface $res) use ($url) {
                    // Тут обработка ответа

                    echo date('d.m.Y H:i:s') . ' запрос выполнен ' . $url . PHP_EOL;
                },
                function (\GuzzleHttp\Exception\RequestException $e) {
                    // Тут обработка ошибки
                }
            );
        }

        // Ждать ответов
        $results = \GuzzleHttp\Promise\Utils::settle($promises)->wait(true);

        // Обработка результатов по всем запросам
        if(sizeof($results) > 0) {
            foreach ($results as $urlIndex => $result) {
                // Обработка ответа по запросу $urls[$urlIndex]

                if ($result['state'] != 'fulfilled' || !isset($result['value'])) {
                    // Если запрос выполнился с ошибкой
                    continue;
                }

                /** @var \GuzzleHttp\Psr7\Response $response */
                $response = $result['value'];

                // Получение заголовков
                // $response->getHeaderLine('Content-Length');

                // Обработка тела ответа
                $body = $response->getBody();
                print_r($body);
                echo date('d.m.Y H:i:s') . ' обработка запроса в цикле' . $urls[$urlIndex] . PHP_EOL;
            }
        }
    }

}
