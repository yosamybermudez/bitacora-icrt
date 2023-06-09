<?php


namespace App\Service;

use App\Repository\AppConfiguracionRepository;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use TelegramBot\Api\BotApi;


class BotTelegram
{
//Telegram Bot data

    private $config;
    //LoggerInterface
    private $logger;

    public function __construct(LoggerInterface $logger, AppConfiguracionRepository $config)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUrlstart()
    {
        return $this->urlstart;
    }

    public function getUrlend()
    {
        return $this->urlend;
    }

    public function getChatid()
    {
        return $this->chatid;
    }

    public function getFullurl()
    {
        $token = $this->getToken();
        $urlstart = $this->getUrlstart();
        $urlend = $this->getUrlend();

        return $urlstart.$token.$urlend;
    }

    public function sendMessage($message, $sender = null, $usename = null){

        if($sender){
            $message = $message . "\n\n &#128172; Enviado por: " . $sender;
        }
        try {
            $telegram = new BotApi($this->config->getVariable('api_telegram_token'));
            $chat_id = $this->config->getVariable('api_telegram_group_id');
            $telegram->sendMessage($usename ?: $chat_id, $message,'HTML');
        }
        catch (Exception $e) {
            $this->logger->critical($e);
        }

//        $chatid = $this->getChatid();
//        $content = array (
//            'headers' => array("Content-Type" => "application/x-www-form-urlencoded"),
//            "body"  => array("chat_id" => $usename ? '@' . $usename : $chatid, "text" => $message, "parse_mode" => "HTML"),
//        );
//
//        $url = $this->getFullurl();
//        $httpClient = HttpClient::create();
//        dd($content);
//        $response = $httpClient->request('GET', $url, $content);
//        $content = $response->getContent();
//        try {
//            $response = $httpClient->request('GET', $url, $content);
//            $content = $response->getContent();
//
//            $this->logger->info($content);
//        }
//        catch (Exception $e) {
//            $this->logger->critical($e);
//        }
    }
}