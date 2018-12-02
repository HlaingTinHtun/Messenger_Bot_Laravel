<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
  public function receive(Request $request)
  {
      $data = $request->all();
      //get the user’s id
      $id = $data["entry"][0]["messaging"][0]["sender"]["id"];
      $this->sendTextMessage($id, "Hello");
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

    $ch = curl_init("https://graph.facebook.com/v3.2/me?messages%3Faccess_token=EAAHSVKcYsSgBAIU6NIHNDyyzEZB8gZCCZAytdDjXTxZBZCeCdt63yp32ZAOQ1sG1S3EO3cq4a7pBZBs3ZBX1PUaTAllZBU7LWvdM4v0LvkWaQI972TZBVqxph8MNntEuEMW1xp4cOdJpnY0xXWtZCV39D35CwYfM9JcZBgSWfaXdzuukdv12DTB8liIf&access_token=EAAHSVKcYsSgBAAUn96qEfIArwaenPicZAmseVgVwsXaeB6oZA8wrTSrCZAtvvxPif2500zJ7eJMWyeZClak8y0RfSbNLx1xs44Y152mq1yVsNdMlTtDZCLHGOsHBUDzWFhI1eqB8MdATFIxnCCC3WSLjOUZAO44aM59q8fpE5AemaIydXrSZBEMrhHlu2pFWe0ZD");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
    curl_exec($ch);

  }

}
