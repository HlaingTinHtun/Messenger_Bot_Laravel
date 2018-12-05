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
    public function extractDataFromMessage()
    {
        $matches = [];
        $text = $this->messaging->getMessage()->getText();
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
    public function reply($data)
    {
        if (method_exists($data, "toMessage")) {
            $data = $data->toMessage();
        } else if (gettype($data) == "string") {
            $data = ["text" => $data];
        }
        $id = $this->messaging->getSenderId();
        $this->sendMessage($id, 'hi Jc');
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
}