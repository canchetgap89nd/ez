<?php

namespace App\Http\Controllers\Facebook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ProcessDataFacebook;

class WebhookController extends Controller
{
    use ProcessDataFacebook;

	public function webhook() {
        $local_verify_token = env('WEBHOOK_VERIFY_TOKEN');
        $hub_verify_token = \Input::get('hub_verify_token');
        $hub_challenge = \Input::get('hub_challenge');
        if ($local_verify_token == $hub_verify_token) {
            return $hub_challenge;
        }
        return $hub_challenge;
    }

    public function receiveWebhook(Request $request)
    {
        $signHub = $request->header('X-Hub-Signature');
        if ($signHub) {
	        $input = \Input::all();
            if (!empty($input) && count($input)) {
                $checkField = isset($input['entry'][0]['changes'][0]['field']) ? $input['entry'][0]['changes'][0]['field'] : null;
                $info = isset($input['entry'][0]['changes'][0]['value']) ? $input['entry'][0]['changes'][0]['value'] : null;
                $fbPageId = isset($input['entry'][0]['id']) ? $input['entry'][0]['id'] : null;
                $timeMess = isset($input['entry'][0]['time']) ? $input['entry'][0]['time'] : null;
                $this->hanldData($checkField, $info, $fbPageId, $timeMess);
            }
        } else {
        	\Log::info(print_r("Facebook webhook HTTP header 'X-Hub-Signature' is missing.", true));
        }
    }
}
