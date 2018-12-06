<?php

namespace App\Jobs;

use App\Bot\Bot;
use App\Bot\Trivia;
use App\Bot\Webhook\Messaging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BotHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //the messaging instance sent to our bothandler
    protected $messaging;

    /**
     * Create a new job instance.
     *
     * @param Messaging $messaging
     */
    public function __construct(Messaging $messaging)
    {
	//Log::info('Get to handler const');
        $this->messaging = $messaging;
    }

    /**
     * Execute the job.
     *
     * @param Messaging $messaging
     */
    public function handle()
    {
	//Log::info('Get to handler');
	/*$messageData = [
            "recipient" => [
                "id" => $this->messaging->getSenderId()
            ],
            "message" => [
                "text" => 'ji'
            ]
        ];

        $ch = curl_init('https://graph.facebook.com/v3.2/me/messages?access_token='.'EAAHSVKcYsSgBAHR26tWH4NhR7N2XcBniZBiPpcZCAM5QSllYxD2pUcBwN2TLgaGZCEoUe5a3LB5hYTxq6KOwfudzMo2DLi5B6PWL711ZCTXgvN14isV9hLrcZBY34l3kMfbct6l2xGfPcIKOJ6ZCkqC5BXsvrxpK1HI3gj1HK1ngZDZD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        curl_exec($ch);*/
        if ($this->messaging->getType() == "message") {
            $bot = new Bot($this->messaging);
            $custom = $bot->extractDataFromMessage();
            //a request for a new question
            if ($custom["type"] == Trivia::NEW_QUESTION) {
                $bot->reply(Trivia::getNew());
            } else if ($custom["type"] == Trivia::ANSWER) {
                $bot->reply(Trivia::checkAnswer($custom["data"]["answer"]));
            } else {
                $bot->reply("I don't understand. Try \"new\" for a new question");
            }
        }
    }
}
