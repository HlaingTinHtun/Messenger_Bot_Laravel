<?php

namespace App\Http\Controllers;

use App\Bot\Webhook\Entry;
use App\Jobs\BotHandler;
use Illuminate\Http\Request;

class MainController extends Controller
{
  public function receive(Request $request)
  {
      // $data = $request->all();
      // //get the user’s id
      // $id = $data["entry"][0]["messaging"][0]["sender"]["id"];
      // $this->sendTextMessage($id, "Hi Dear");
      $entries = Entry::getEntries($request);
      foreach ($entries as $entry) {
          $messagings = $entry->getMessagings();
          foreach ($messagings as $messaging) {
              dispatch(new BotHandler($messaging));
          }
      }
      return response();
  }

  private function sendTextMessage($recipientId, $messageText)
  {
    $messageData = [
        "recipient" => [
            "id" => $recipientId
        ],
        "message" => [
            "text" => $messageText
        ]
    ];

    $ch = curl_init('https://graph.facebook.com/v3.2/me/messages?access_token='.'EAAHSVKcYsSgBAHR26tWH4NhR7N2XcBniZBiPpcZCAM5QSllYxD2pUcBwN2TLgaGZCEoUe5a3LB5hYTxq6KOwfudzMo2DLi5B6PWL711ZCTXgvN14isV9hLrcZBY34l3kMfbct6l2xGfPcIKOJ6ZCkqC5BXsvrxpK1HI3gj1HK1ngZDZD');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
    curl_exec($ch);

  }

}
