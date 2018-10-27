<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class NotificationController extends Controller
{
	/*
	push notification to firebase
	 */
  public function notifyFcm ($token, $title, $body, $conver)
  {
    $url = 'https://fcm.googleapis.com/fcm/send';
  
  	if($token == '') return;
    
  	$apiKey = env('FIREBASE_KEY');
    
  	$data = array(
  		"to" => $token,
     	"notification" => array(
          "title" => $title,
          "body" => $body,
          'conversation' => $conver,
          'sound' => 'default'
     	),
     	"data" => array(
          "title" => $title,
          "body" => $body,
          'conversation' => $conver,
          'sound' => 'default'
     	)
  	);

  	$fields = json_encode ( $data );
    
  	$headers = array (
    	'Authorization: key='. $apiKey,
        'Content-Type: application/json'
  	);
    
  	$ch = curl_init ();
  	curl_setopt ( $ch, CURLOPT_URL, $url );
  	curl_setopt ( $ch, CURLOPT_POST, true );
  	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
	  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
  	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
    
  	$result = curl_exec ( $ch );
  	curl_close ( $ch );
  }

  /**
   * push notifications on onesignal
   * @param  array  $playerIds [array player id on onesignal]
   * @param  [type] $title     [description]
   * @param  [type] $body      [description]
   * @param  [type] $conver    [data payload]
   * @return [type]            
   */
  public function sendMessageOneSignal (array $playerIds, $title, $body, $conver)
  {
    try {
      $content      = array(
          "en" => $body
      );
      $heading = array(
        "en" => $title
      );
      $fields = array(
          'app_id' => env('ONE_SIGNAL_APP_ID'),
          'include_player_ids' => $playerIds,
          'data' => array(
              "title" => $title,
              "body" => $body,
              "conversation" => $conver
          ),
          'headings' => $heading,
          'contents' => $content
      );
      
      $fields = json_encode($fields);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json; charset=utf-8',
          'Authorization: Basic ' . env('ONE_SIGNAL_AUTH_KEY')
      ));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      
      $response = curl_exec($ch);
      curl_close($ch);
    } catch (\Exception $e) {
      \Log::info(print_r($e, true));
      return null;
    }
    return $response;
  }

  public function pushNotifyOneSignal(array $playerIds, $title, $body, $conver)
  {
    $response = $this->sendMessageOneSignal($playerIds, $title, $body, $conver);
    $data = json_decode($response, true);
    if (isset($data['id'])) {
      return $data;
    }
    return null;
  }

  public function testNotifyOneSignal()
  {
    $playerIds = [
      '33f6519d-a4cd-4104-9358-7edbc46ff8e1', 
      '2cfea45a-3d97-4e12-bf2a-39b2f7bd9175'
    ];
    $title = 'Chào bạn nha';
    $body = 'Đây là Trường đẹp trai';
    $conversation = [
      'name' => 'Le Huu Hoang',
      'id' => '12',
      'conver_id' => '99f299'
    ];
    $result = $this->pushNotifyOneSignal($playerIds, $title, $body, $conversation);
    dd($result);
  }
}
