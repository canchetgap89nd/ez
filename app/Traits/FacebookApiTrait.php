<?php

namespace App\Traits;

use App\Traits\GenerateTrait;

trait FacebookApiTrait
{
    use GenerateTrait;

    public function getPhoneFromUID($uid)
    {
        $userAgents = $this->generateUserAgents();
        $head = $this->listHeader;
        $url = 'http://hacklike.biz/danhbafacebook/get/get_info1.php?uid=' . $uid;
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_USERAGENT, $userAgents );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $head );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 120 );
        curl_setopt ( $ch, CURLOPT_ENCODING, "" );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        $res = json_decode($result, true);
        $phone = isset($res['phone']) ? $res['phone'] : null;
        if ($phone && strlen($phone) < 13) {
            $phone = removeScript($phone);
            $phone = changePhone84($phone);
            return $phone;
        }
        return null;
    }

    /**
     * unsubcribe listen from page
     * @param  [type] $pageId    [facebook page id]
     * @param  [type] $pageToken [page access token]
     * @return [type]            
     */
    public function unsubcribeApp($pageId, $pageToken)
    {
        $fb = setupApi();
        $att = [
            'object' => 'page'
        ];
        try {
            $response = $fb->delete('/' . $pageId . '/subscribed_apps', $att, $pageToken);
        } catch (\Facebook\ Exceptions\ FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: '.$e->getMessage()
            ];
        } catch (\Facebook\ Exceptions\ FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: '.$e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * subcribe listen from page
     * @param  [type] $pageId    [facebook page id]
     * @param  [type] $pageToken [page access token]
     * @return [type]
     */
    public function subcribeApp($pageId, $pageToken)
    {
        $fb = setupApi();
        $att = [
          'access_token' => $pageToken
        ];
        try {
            $response = $fb->post('/'.$pageId.'/subscribed_apps', $att, $pageToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: '.$e-> getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: '.$e-> getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * get infomation of post
     * @param  [type] $postId      [facebook post id]
     * @param  [type] $accessToken [access token of page]
     * @return [type]              [infomation post]
     */
    public function getInfoPost($postId, $accessToken)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/'.$postId.'?fields=admin_creator, caption, created_time, description, from, icon, is_hidden, link, message, picture, type, updated_time', $accessToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * get infomation of conversation inbox
     * @param  [type] $conversationId [facebook conversation inbox id]
     * @param  [type] $access_token   [page access token]
     * @return [type]                 [array infomation of conversation]
     */
    public function getConversationInfo($conversationId, $access_token)
    {
        $fb = setupApi();
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/'.$conversationId.'?fields=snippet,updated_time,participants', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return null;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return null;
        }
        $res = $response->getDecodedBody();
        $res['snippet'] = isset($res['snippet']) ? $res['snippet'] : '';
        return $res;
    }

    /**
     * get message infomation from webhook page
     * @param  [type] $conversationId [conversation ID]
     * @param  [type] $access_token   [page token]
     * @param  [type] $timeMess       [time of receive notification from webhook]
     * @return [array]                 [infomation message]
     */
    public function getJustMessage($conversationId, $access_token, $timeMess)
    {
        $fb = setupApi();
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/'.$conversationId.'/messages?fields=from,message,attachments,created_time,to,shares{link}&limit=5', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return null;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return null;
        }
        $res = $response->getDecodedBody();
        if (isset($res['data'][0])) {
            foreach ($res['data'] as $mess) {
                $timec = strtotime($mess['created_time']);
                if (intval($timeMess) >= $timec) {
                    return $mess;
                }
            }
        }
        return null;
    }

    public function getListMessages($conversationId, $access_token, $limit = 2)
    {
        $fb = setupApi();
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/'.$conversationId.'/messages?fields=from,message,attachments,created_time,to&limit=' . $limit, $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return [];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return [];
        }
        $res = $response->getDecodedBody();
        if (isset($res['data'])) {
            return $res['data'];
        }
        return [];
    }

    /**
     * get infomation message
     * @param  [type] $messageId    [message ID]
     * @param  [type] $access_token [page token]
     * @return [array]               [infomation message]
     */
    public function getInfoMessage($messageId, $access_token)
    {
        $fb = setupApi();
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get($messageId.'?fields=from,message,attachments,created_time,to,shares{link}', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return [];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return [];
        }
        return $response->getDecodedBody();
    }

    /**
     * get infomation comment
     * @param  [type] $commentId    [comment ID]
     * @param  [type] $access_token [page token]
     * @return [array]               [info comment]
     */
    public function getInfoComment($commentId, $access_token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/'.$commentId.'?fields=attachment,can_comment,can_remove,can_hide,can_like,can_reply_privately,created_time,from,message,object,parent,user_likes,private_reply_conversation, like_count,is_hidden', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return null;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return null;
        }
        return $response->getDecodedBody();
    }

    /**
     * get list comment childs of comment another (max 25 comment)
     * @param  [type] $commentId [comemnt ID need to get]
     * @param  [type] $token     [page token]
     * @return [array]            [list comment childs]
     */
    public function getChildsComments($commentId, $token, $limit = 25)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/'.$commentId.'/comments?fields=attachment,can_comment,can_remove,can_hide,can_like,can_reply_privately,created_time,from,message,object,parent,user_likes,private_reply_conversation, like_count, is_hidden&limit='. $limit, $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            \Log::info(print_r('Graph returned an error: ' . $e->getMessage(), true));
            return null;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            \Log::info(print_r('Facebook SDK returned an error: ' . $e->getMessage(), true));
            return null;
        }
        return $response->getDecodedBody();
    }

    public function sendMessageToFb($threadId, $message, $accessToken)
    {
        $fb = setupApi();
        try {
            $attachment = [
                'message' => $message,
            ];
            $response = $fb->post('/'.$threadId.'/messages', $attachment, $accessToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        $res = $response->getDecodedBody();
        return isset($res['id']) ? $res['id'] : null;
    }

    public function sendCommentToFb($targetId, $message, $accessToken)
    {
        $fb = setupApi();
        try {
            $attachment = [
                'message' => $message,
            ];
            $response = $fb->post('/'.$targetId.'/comments', $attachment, $accessToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        $cmt = $response->getDecodedBody();
        return isset($cmt['id']) ? $cmt['id'] : null;
    }

    /**
     * delete comment on facebook
     * @param  [type] $id    [facebook comment id]
     * @param  [type] $token [page access token]
     * @return [type]        [success or not success]
     */
    public function deleteCommentFromFb($id, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->delete('/'.$id,array() ,$token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * send inbox has infomation post to customer comment to post
     * @param  [type] $id      [facebook comment id]
     * @param  [type] $message [message]
     * @param  [type] $token   [page access token]
     * @return [type]          just message
     */
    public function sendMessagesWithPostToFb($id, $message, $token)
    {
        $fb = setupApi();
        $att = [
            'message' => $message,
            'id' => $id
        ];
        try {
            $response = $fb->post('/'.$id.'/private_replies',$att ,$token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * hide comment on facebook page
     * @param  [type] $commentId [facebook comment id]
     * @param  [type] $isHide    [true is hide or false for remove hide]
     * @param  [type] $token     [page acccess token]
     * @return [type]            
     */
    public function hideCommentOnFb($commentId, $isHide, $token)
    {
        $fb = setupApi();

        $isHide = $isHide ? true : false;

        $att = [
            'is_hidden' => $isHide
        ];
        try {
            $response = $fb->post('/'.$commentId,$att ,$token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * like comment on facebook
     * @param  [type] $fbCommentId [facebook comment id]
     * @param  [type] $token       [page access token]
     * @return [type]              
     */
    public function likeCommentOnFb($fbCommentId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->post('/'.$fbCommentId.'/likes',array() ,$token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * dislike comment on facebook
     * @param  [type] $fbCommentId [facebook comment id]
     * @param  [type] $token       [page access token]
     * @return [type]             
     */
    public function dislikeCommentOnFb($fbCommentId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->delete('/'.$fbCommentId.'/likes',array() ,$token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    public function getListConversationOfPage($fbPageId, $token, $limit = 25)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/'.$fbPageId.'/conversations?fields=id,snippet,updated_time,unread_count,senders&limit=' . $limit, $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * get infomation facebook of user
     * @param  [type] $accessToken [access token facebook of user]
     * @return [type]              
     */
    public function getInfoFb($accessToken)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/me?fields=email,name,id', $accessToken);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'success' => 0,
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
            ];
        }
        return $response->getDecodedBody();
    }

    /**
     * get infomation all facebook page of user
     * @param  [type] $token [access token facebook user]
     * @return [type]        
     */
    public function getInfoPages($token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/me/accounts?fields=name,access_token,id,category,category_enum', $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'message' => 'Graph returned an error: ' . $e->getMessage(),
                'success' => 0
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage(),
                'success' => 0
            ];
        }
        return $response->getDecodedBody();
    }

    public function getAllPages($token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/me/accounts?fields=category,id,name,access_token', $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'message' => 'Graph returned an error: ' . $e->getMessage(),
                'success' => 0
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage(),
                'success' => 0
            ];
        }
        $nextEdge = $pageEdge = $response->getGraphEdge();
        $pages = [];
        foreach ($pageEdge as $item) {
            array_push($pages, $item->asArray());
        }
        $index = 1;
        // get all page less more 5 pages
        while ($nextEdge && $index < 5) {
            $index++;
            $nextEdge = $fb->next($pageEdge);
            if ($nextEdge) {
                foreach ($nextEdge as $nex) {
                    array_push($pages, $nex->asArray());
                }
            }
            $pageEdge = $nextEdge;
        }
        return $pages;
    }

    /**
     * get all member of page
     * @param  [type] $fbPageId [description]
     * @param  [type] $token    [description]
     * @return [type]           [description]
     */
    public function getRolesPage($fbPageId, $token)
    {
        $fb = setupApi();
        try {
            $response = $fb->get('/'.$fbPageId. '/roles', $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return [
                'success' => 0,
                'message' => 'Graph returned an error: ' . $e->getMessage()
            ];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return [
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage(),
                'success' => 0
            ];
        }
        return $response->getDecodedBody();
    }
}