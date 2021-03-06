<?php
namespace App\Bot;
use App\Bot\Webhook\Messaging;
use Illuminate\Support\Facades\Log;
class Bot
{
    private $messaging;
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function extractData()
    {
        $type = $this->messaging->getType();
        if($type == "message") {
            return $this->extractDataFromMessage();
        } else if ($type == "postback") {
            return $this->extractDataFromPostback();
        }
        return [];
    }

    public function extractDataFromMessage()
    {
        $matches = [];
        $qr = $this->messaging->getMessage()->getQuickReply();
        if (!empty($qr)) {
            $text = $qr["payload"];
        } else {
            $text = $this->messaging->getMessage()->getText();
        }
        //single letter message means an answer
        if (preg_match("/^(\\w)\$/i", $text, $matches)) {
            return [
                "type" => Trivia::ANSWER,
                "data" => [
                    "answer" => $matches[0]
                ]
            ];
        } else if (preg_match("/^new|next\$/i", $text, $matches)) {
            return [
                "type" => Trivia::NEW_QUESTION,
                "data" => []
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function extractDataFromPostback()
    {
        $payload = $this->messaging->getPostback()->getPayload();
        //is it an answer to a question
        if (preg_match("/^(\\w)\$/i", $payload)){
            return [
                "type" => Trivia::ANSWER,
                "data" => [
                    "answer" => $payload
                ]
            ];
            //or a Get Started button click?
        } else if ($payload === "get-started") {
            return [
                "type" => "get-started",
                "data" => []
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function reply($data)
    {
        if (method_exists($data, "toMessage")) {
            $data = $data->toMessage();
        } else if (gettype($data) == "string") {
            $data = ["text" => $data];
        }
        $id = $this->messaging->getSenderId();
        $this->sendMessage($id, $data);
    }
    private function sendMessage($recipientId, $message)
    {
        $messageData = [
            "recipient" => [
                "id" => $recipientId
            ],
            "message" => $message
	];
        $ch = curl_init('https://graph.facebook.com/v3.2/me/messages?access_token='.'EAAHSVKcYsSgBAHR26tWH4NhR7N2XcBniZBiPpcZCAM5QSllYxD2pUcBwN2TLgaGZCEoUe5a3LB5hYTxq6KOwfudzMo2DLi5B6PWL711ZCTXgvN14isV9hLrcZBY34l3kMfbct6l2xGfPcIKOJ6ZCkqC5BXsvrxpK1HI3gj1HK1ngZDZD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        Log::info(print_r(curl_exec($ch), true));
    }

    public function sendWelcomeMessage()
    {
        $name = $this->getUserDetails()["first_name"];
        $this->reply("Hi $name! Welcome to Zech Bot! You can type \"new\" to get a new question, but why don’t we start with this one?");
    }

    private function getUserDetails()
    {
        $id = $this->messaging->getSenderId();
        $ch = curl_init("https://graph.facebook.com/v3.2/$id?access_token=" .'EAAHSVKcYsSgBAHR26tWH4NhR7N2XcBniZBiPpcZCAM5QSllYxD2pUcBwN2TLgaGZCEoUe5a3LB5hYTxq6KOwfudzMo2DLi5B6PWL711ZCTXgvN14isV9hLrcZBY34l3kMfbct6l2xGfPcIKOJ6ZCkqC5BXsvrxpK1HI3gj1HK1ngZDZD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        return json_decode(curl_exec($ch), true);
    }
}
