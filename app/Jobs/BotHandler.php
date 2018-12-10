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
use Illuminate\Support\Facades\Cache;
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
        $bot = new Bot($this->messaging);
        $custom = $bot->extractData();

        //a request for a new question
        if ($custom["type"] == Trivia::NEW_QUESTION) {
            $bot->reply(Trivia::getNew());
        } else if ($custom["type"] == Trivia::ANSWER) {
            if (Cache::has("solution")) {
                $bot->reply(Trivia::checkAnswer($custom["data"]["answer"]));
            } else {
                $bot->reply("Looks like that question has already been answered. Try \"new\" for a new question");
            }
        } else if($custom["type"] == "get-started") {
            $bot->sendWelcomeMessage();
            $bot->reply(Trivia::getNew());
        } else {
            $bot->reply("I don't understand. Try \"new\" for a new question");
        }
    }


}
