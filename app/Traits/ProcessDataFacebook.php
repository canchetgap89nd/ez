<?php

namespace App\Traits;

use App\Jobs\PushNotification;
use App\Events\MessagePosted;
use App\Events\NotificationFace;
use App\Respositories\Conversation\ConverRespository;
use App\Respositories\Conversation\MessageRespository;
use App\Respositories\Conversation\AttachRespository;
use App\Respositories\Conversation\CommentRespository;
use App\models\Conversation\PostPage;
use App\User;
use App\models\Page;
use App\models\Customer;
use App\Jobs\HideComment;
use App\Jobs\LikeComment;
use App\Jobs\Dashboard\AutoReply;
use App\Jobs\FilterEmailMarketting;
use App\Jobs\FilterPhonePMarketting;
use App\Jobs\Dashboard\ReceiveDataFromFacebook;
use Illuminate\Support\Facades\Redis;
use App\Traits\FacebookApiTrait;
use Illuminate\Support\Facades\DB;

trait ProcessDataFacebook
{
    use FacebookApiTrait;

	public function hanldData($type, $payload, $fbPageId, $timeMess)
	{
        $startTime = microtime(true);
        switch ($type) {
            case 'conversations':
                $threadId = $payload['thread_id'];
                $this->pushDataToUser($threadId, $fbPageId, $timeMess, 'MESSAGE');
                break;
            case 'feed':
                if ($payload['item'] == "comment" && $payload['verb'] == "add") {
                    $tg = explode("_", $payload['post_id']);
                    $fbPageIdFix = $tg[0];
                    $threadId = $payload['post_id'];
                    $fbCommentId = $payload['comment_id'];
                    $this->pushDataToUser($threadId, $fbPageIdFix, $timeMess, 'COMMENT', $fbCommentId);
                }
                break;
            
            default:
                break;
        }
        $endTime = microtime(true);
        \Log::info('Tong thoi gian __ ' . round(($endTime - $startTime) * 1000));
	}

    public function pushDataToUser($threadId, $fbPageId, $timeMess, $type, $fbCommentId = null)
    {
        $pages = Page::where('fb_page_id', $fbPageId)->get();
        $arrIds = [];
        foreach ($pages as $page) {
            array_push($arrIds, $page->user_id);
        }
        $users = User::whereIn('id', $arrIds)->get();
        foreach ($users as $user) {
            $clientOnline = json_decode(Redis::get('user:online:' . $user->id));
            $page = null;
            foreach ($pages as $it) {
                if ($it->user_id == $user->id) {
                    $page = $it;
                    break;
                }
            }
            if ($clientOnline && count($clientOnline) > 0) {
                // event(new NotificationFace($user->id, $this->zipPayload($page, $threadId, $fbCommentId, $type)));
                $this->updateConversation($user, $page, $threadId, $fbCommentId, $timeMess, $type);
            } else {
                ReceiveDataFromFacebook::dispatch($user->id, $page->fb_page_id, $threadId, $fbCommentId, $timeMess, $type)->onConnection('database')->onQueue('receiveDataFromFace');
            }
        }
    }

    public function zipPayload(Page $page, $threadId, $fbCommentId, $type)
    {
        $resp = [
            'fb_page_id' => $page->fb_page_id,
            'thread_id' => $threadId,
            'type' => $type
        ];
        if ($type == 'COMMENT') {
            $resp['fb_comment_id'] = $fbCommentId;
        }
        return $resp;
    }

    public function updateConversation(User $user, Page $page, $threadId, $entityId, $timeMess, $type)
    {        
        if ($page->isActive()) {
            $admin = $user;
            $setting = null;
            if ($user->parent_user_id) {
                $admin = User::find($user->parent_user_id);
                $setting = DB::table('setting_basics')
                                ->where('user_id', $user->parent_user_id)
                                ->first();
            } else {
                $setting = DB::table('setting_basics')
                                ->where('user_id', $user->id)
                                ->first();
            }
            switch ($type) {
                case 'MESSAGE':
                    $conversation = $this->createConversationMessage($admin, $page, $threadId, $timeMess, $setting);
                    break;
                case 'COMMENT':
                    $conversation = $this->createConversationComment($admin, $page, $threadId, $entityId, $setting);
                    break;
                default:
                    $conversation = null;
                    break;
            }
            if (!empty($conversation)) {
                if (!$conversation->is_me) {
                    PushNotification::dispatch($user, $conversation)->onQueue('notificationsMobile');
                }
                event(new MessagePosted($conversation, $user->id));
            }
        }
    }

    public function updateConversationJob($userId, $fbPageId, $threadId, $entityId, $timeMess, $type)
    {
        $user = User::find($userId);
        $page = Page::where('fb_page_id', $fbPageId)->where('user_id', $userId)->first();
        if ($user && $page) {
            $this->updateConversation($user, $page, $threadId, $entityId, $timeMess, $type);
        } else {
            \Log::error("Error not find user or page for update conversation");
        }
    }

    /**
     * check has keyword on string
     * @param  [string]  $key [string]
     * @param  [type]  $arr [array keyword]
     * @return boolean      [has a keyword of array keyword on string]
     */
    public function hasKey($key, $arr)
    {
        if ($key) {
            foreach ($arr as $item) {
                if (preg_match('/'.$item.'/', $key)) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    /**
     * get user facebook send message
     * @param  [type] $page     [Page model]
     * @param  [type] $messages [object message receive facebook api]
     * @return [type]           [from array containt name and id facebook]
     */
    public function getFromOfMessages($page, $message)
    {
        if ((binary) $message['from']['id'] !== (binary) $page->fb_page_id) {
            $from = $message['from'];
        } else 
        if (isset($message['to']['data'][0])) {
            $from = $message['to']['data'][0];
        }
        return $from;
    }

    /**
     * create conversations message
     * @param  User   $user     [user of conversation]
     * @param  [type] $page     [page model has conversation]
     * @param  [type] $threadId [facebook id of conversation]
     * @param  [type] $timeMess [time of message]
     * @return [type]           [conversation and just message]
     */
    public function createConversationMessage(User $user, $page, $threadId, $timeMess, $setting)
    {
        try {
            $result = null;
            $startTime = microtime(true);
            $message = $this->getJustMessage($threadId, $page->page_token, $timeMess);
            $endTime = microtime(true);
            \Log::info('Thoi gian lay thong tin message ___ ' . round(($endTime - $startTime) * 1000));
            if (isset($message['id'])) {
                $arrKey = $setting ? json_decode(base64_decode($setting->key_cmt_priority)) : [];
                $isMe = $message['from']['id'] == $page->fb_page_id ? true : false;

                $model = (new ConverRespository($user->id));
                $curr = $model->getFirst([
                    ['user_id', $user->id],
                    ['fb_page_id', $page->fb_page_id],
                    ['thread_id', $threadId],
                    ['type', MESSAGE]
                ]);
                $hasKey = $this->hasKey($message['message'], $arrKey) && !$isMe ? true : false;
                $hasPhone = (hasPhone($message['message']) && !$isMe) ? true : false;
                $unReply = $unread = !$isMe ? true : false;
                $lastMessage = isset($message['attachments']) || isset($message['shares']) ? '@Tệp tin' : _substr($message['message'], 150);
                $timeUpdate = strtotime($message['created_time']);
                // get id facebook of customer, fix error bot chat
                $from = $this->getFromOfMessages($page, $message);
                $fromName = $from['name'];
                $fromId = $from['id'];
                $conversation = $this->createConver($user, $page, $fromId, $fromName, $threadId, $isMe, $hasKey, $hasPhone, $unReply, $unread, $timeUpdate, $lastMessage, MESSAGE);
                $tA = microtime(true);
                $newMess = $this->createMessage($user, $page, $conversation->id, $message, $hasPhone, $hasKey, $unread);
                $tB = microtime(true);
                \Log::info('Thoi gian tao tin nhan ___ ' . round(($tB - $tA) * 1000));
                $result = $this->packData($user, $conversation, $isMe, $newMess, 'MESSAGE');
            }
            return $result;
        } catch (\Exceptions $ex) {
            \Log::error($ex->getMessage());
            return null;
        }
    }

    /**
     * create message
     * @param  User   $user           [user of message]
     * @param  [type] $conversationId [id of conversation has message]
     * @param  [type] $data           [data of message get from facebook api]
     * @param  [type] $hasPhone       [has phone number]
     * @param  [type] $hasKey         [has keyword]
     * @param  [type] $unread         [is unread message]
     * @return [type]                 [message object model]
     */
    public function createMessage(User $user, Page $page, $converId, $data, $hasPhone, $hasKey, $unread, $platForm = null, $staffId = null)
    {
        try {
            $mess = new MessageRespository($user->id);
            $mess = $mess->getFirst([
                ['conver_id', $converId],
                ['user_id', $user->id],
                ['from_id', $data['from']['id']],
                ['fb_message_id', $data['id']]
            ]);
            $timeCreated = strtotime($data['created_time']);
            $infoNewMess = [
                'conver_id' => $converId,
                'fb_page_id' => $page->fb_page_id,
                'fb_message_id' => $data['id'],
                'from_id' => $data['from']['id'],
                'from_name' => $data['from']['name'],
                'message' => $data['message'],
                'unread' => $unread,
                'has_phone' => $hasPhone,
                'has_key' => $hasKey,
                'created_time' => $timeCreated,
                'user_id' => $user->id,
                'staff_reply_id' => $staffId,
                'from_platform' => $platForm
            ];
            $attris = [];
            if (isset($data['attachments']['data'])) {
                foreach ($data['attachments']['data'] as $att) {
                    $type = $att['mime_type'] == 'image/jpeg' ? 'IMAGE' : 'FILE';
                    $url = isset($att['image_data']['url']) ? $att['image_data']['url'] : '';
                    $preview_url = isset($att['image_data']['preview_url']) ? $att['image_data']['preview_url'] : '';
                    $file_url = isset($att['file_url']) ? $att['file_url'] : '';
                    array_push($attris, [
                        'type' => $type,
                        'url' => $url,
                        'preview_url' => $preview_url,
                        'file_url' => $file_url,
                        'entity_type' => MESSAGE
                    ]);
                }
            }
            if (isset($data['shares']['data'])) {
                foreach ($data['shares']['data'] as $sticker) {
                    $type = 'STICKER';
                    $url = isset($sticker['link']) ? $sticker['link'] : '';
                    $preview_url = '';
                    $file_url = '';
                    array_push($attris, [
                        'type' => $type,
                        'url' => $url,
                        'preview_url' => $preview_url,
                        'file_url' => $file_url,
                        'entity_type' => MESSAGE
                    ]);
                }
            }
            if (empty($mess)) {
                $newMess = new MessageRespository($user->id);
                $newMess = $newMess->create($infoNewMess);
                // return data new message
                $infoNewMess['id'] = $newMess;
                $mess = (object) $infoNewMess;
                if (count($attris) > 0) {
                    foreach ($attris as $k => $item) {
                        $attris[$k]['entity_id'] = $newMess;
                    }
                    $newAtt = new AttachRespository($user->id);
                    $newAtt->insert($attris);
                }
            } else {
                foreach ($attris as $k => $item) {
                    $attris[$k]['entity_id'] = $mess->id;
                }
            }
            // return data attachment
            $attachments = (object) $attris;
            $mess->attachments = $attachments;
            return $mess;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return null;
        }
    }

    /**
     * create conversation with comment facebook
     * @param  User   $user        [user has conversation]
     * @param  [type] $page        [page object has conversation]
     * @param  [type] $threadId    [post id facebook]
     * @param  [type] $fbCommentId [facebook comment id]
     * @return [type]              [conversation has just comment]
     */
    public function createConversationComment(User $user, Page $page, $threadId, $fbCommentId, $setting)
    {
        try {
            $tA = microtime(true);
            $dataCmt = $this->getInfoComment($fbCommentId, $page->page_token);
            $tB = microtime(true);
            \Log::info('Thoi gian lay thong tin comment ___ ' . round(($tB - $tA) * 1000));
            $result = null;
            if (isset($dataCmt['id'])) {
                $keyPriority = $setting ? json_decode(base64_decode($setting->key_cmt_priority)) : [];
                $isMe = $dataCmt['from']['id'] == $page->fb_page_id ? true : false;
                $hasKey = $this->hasKey($dataCmt['message'], $keyPriority) && !$isMe ? true : false;
                $hasPhone = (hasPhone($dataCmt['message']) && !$isMe) ? true : false;
                $unReply = $unread = !$isMe ? true : false;
                $lastMessage = ! isset($dataCmt['attachment']) ? _substr($dataCmt['message'], 150) : '@Tệp tin';
                $timeUpdate = strtotime($dataCmt['created_time']);
                $this->doSettingWithComment($user, $page, $threadId, $dataCmt, $setting);
                if (isset($dataCmt['parent'])) {
                    $infoParent = [
                        'id' => $dataCmt['parent']['id'],
                        'message' => $dataCmt['parent']['message'],
                        'from' => $dataCmt['parent']['from'],
                        'created_time' => $dataCmt['parent']['created_time'],
                        'can_comment' => $dataCmt['can_comment'],
                        'can_remove' => $dataCmt['can_remove'],
                        'can_hide' => $dataCmt['can_hide'],
                        'can_like' => $dataCmt['can_like'],
                        'can_reply_privately' => $dataCmt['can_reply_privately'],
                        'is_hidden' => $dataCmt['is_hidden'],
                        'user_likes' => $dataCmt['user_likes'],
                    ];
                    $fromId = $infoParent['from']['id'];
                    $fromName = $infoParent['from']['name'];
                    $conversation = $this->createConver($user, $page, $fromId, $fromName, $threadId, $isMe, $hasKey, $hasPhone, $unReply, $unread, $timeUpdate, $lastMessage, COMMENT);
                    $tA = microtime(true);
                    $parentCmt = $this->createComment($user, $page, $conversation->id, null, $infoParent, false, false, false, $conversation->post_id);
                    $newCmt = $this->createComment($user, $page, $conversation->id, $parentCmt->id, $dataCmt, $hasPhone, $hasKey, $unread, $conversation->post_id);
                    $tB = microtime(true);
                    \Log::info('Thoi gian lay tao comment ___ ' . round(($tB - $tA) * 1000));
                    $result = $this->packData($user, $conversation, $isMe, $newCmt, 'COMMENT');
                } else {
                    $fromId = $dataCmt['from']['id'];
                    $fromName = $dataCmt['from']['name'];
                    $isParent = true;
                    $conversation = $this->createConver($user, $page, $fromId, $fromName, $threadId, $isMe, $hasKey, $hasPhone, $unReply, $unread, $timeUpdate, $lastMessage, COMMENT, $isParent);
                    $newCmt = $this->createComment($user, $page, $conversation->id, null, $dataCmt, $hasPhone, $hasKey, $unread, $conversation->post_id);
                    $result = $this->packData($user, $conversation, $isMe, $newCmt, 'COMMENT');
                }
            }
            return $result;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return null;
        }
    }

    public function packData(User $user, object $conversation, bool $isMe, object $justMess, $type)
    {
        $unread_count = 0;
        switch ($type) {
            case 'MESSAGE':
                $model = new MessageRespository($user->id);
                $condiction = [
                    ['user_id', $user->id],
                    ['conver_id', $conversation->id],
                    ['unread', true]
                ];
                $unread_count = $model->getCount($condiction);
                break;
            case 'COMMENT':
                $model = new CommentRespository($user->id);
                $condiction = [
                    ['user_id', $user->id],
                    ['conver_id', $conversation->id],
                    ['unread', true]
                ];
                $unread_count = $model->getCount($condiction);
            default:
                break;
        }
        $customer = Customer::with('groups')
                            ->where('user_id', $user->id)
                            ->where('fb_id_cus', $conversation->from_id)
                            ->first();
        $conversation->customer = $customer;
        $conversation->just_message = $justMess;
        $conversation->is_me = $isMe;
        $conversation->unread_count = $unread_count;
        return $conversation;
    }
    
    public function createConver(User $user, Page $page, $fromId, $fromName, $threadId, $isMe, $hasKey, $hasPhone, $unReply, $unread, $timeUpdate, $lastMessage, $type, $isParent = false)
    {
        $curr = new ConverRespository($user->id);
        $curr = $curr->getFirst([
            ['user_id', $user->id],
            ['fb_page_id', $page->fb_page_id],
            ['thread_id', $threadId],
            ['from_id', $fromId],
            ['type', $type]
        ]);
        $postId = null;
        if ($type == COMMENT) {
            $post = $this->createPostPage($user, $page, $threadId);
            $postId = $post->id;
        }
        $infoNewConver = [
            'user_id' => $user->id,
            'fb_page_id' => $page->fb_page_id,
            'from_id' => $fromId,
            'from_name' => $fromName,
            'thread_id' => $threadId,
            'post_id' => $postId,
            'last_message' => $lastMessage,
            'updated_time' => $timeUpdate,
            'unread' => $unread,
            'has_phone' => $hasPhone,
            'has_key' => $hasKey,
            'type' => $type,
            'unreply' => $unReply,
        ];
        $result = null;
        if ($curr) {
            $hasPhone = $curr->has_phone ? $curr->has_phone : $hasPhone;
            $hasKey = $curr->has_key ? $curr->has_key : $hasKey;
            $isMulti = $isParent && $type == COMMENT && !$curr->is_multiple_chat ? true : $curr->is_multiple_chat;
            $upConver = new ConverRespository($user->id);
            $upConver = $upConver->update($curr->id, [
                            'last_message' => $lastMessage,
                            'updated_time' => $timeUpdate,
                            'unreply' => $unReply,
                            'unread' => $unread,
                            'has_phone' => $hasPhone,
                            'has_key' => $hasKey,
                            'is_multiple_chat' => $isMulti
                       ]);
            $infoNewConver['id'] = $curr->id;
            $infoNewConver['has_phone'] = $hasPhone;
            $infoNewConver['has_key'] = $hasKey;
            $result = (object) $infoNewConver;
        } else {
            $newConver = new ConverRespository($user->id);
            $newConver = $newConver->create($infoNewConver);
            $infoNewConver['id'] = $newConver;
            $result = (object) $infoNewConver;
        }
        return $result;
    }

    /**
     * create comment
     * @param  User   $user        [user has comment]
     * @param  [type] $converId    [conversation has comment]
     * @param  [type] $fbCommentId [facebook id comment]
     * @param  [type] $parentId    [parent id of comment in database]
     * @param  [type] $data        [data comment receive on facebook api]
     * @param  [type] $hasPhone    [has phone in comment]
     * @param  [type] $hasKey      [has keyword in comment]
     * @param  [type] $unread      [comment is unread or not unread]
     * @return [type]              [comment created]
     */
    public function createComment(User $user, Page $page, $converId, $parentId, $data, $hasPhone, $hasKey, $unread, $postId, $platForm = null, $staffId = null)
    {
        try {
            $fbCommentId = $data['id'];
            $cmt = new CommentRespository($user->id);
            $cmt = $cmt->getFirst([
                ['fb_comment_id', $fbCommentId],
                ['user_id', $user->id],
                ['fb_page_id', $page->fb_page_id],
                ['from_id', $data['from']['id']]
            ]);
            $timeCreated = strtotime($data['created_time']);
            $infoNewCmt = [
                'conver_id' => $converId,
                'fb_page_id' => $page->fb_page_id,
                'post_id' => $postId,
                'fb_comment_id' => $fbCommentId,
                'parent_id' => $parentId,
                'from_id' => $data['from']['id'],
                'from_name' => $data['from']['name'],
                'message' => $data['message'],
                'can_comment' => $data['can_comment'],
                'can_hide' => $data['can_hide'],
                'can_like' => $data['can_like'],
                'can_remove' => $data['can_remove'],
                'can_reply_privately' => $data['can_reply_privately'],
                'is_hidden' => $data['is_hidden'],
                'user_likes' => $data['user_likes'],
                'is_remove' => false,
                'has_phone' => $hasPhone,
                'has_key' => $hasKey,
                'unread' => $unread,
                'created_time' => $timeCreated,
                'user_id' => $user->id,
                'from_platform' => $platForm,
                'staff_reply_id' => $staffId
            ];
            $infoAtt = [];
            if (isset($data['attachment'])) {
                $att = $data['attachment'];
                $preview_url = '';
                $type = '';
                $url = '';
                switch ($att['type']) {
                    case 'photo':
                        $type = 'IMAGE';
                        $url = isset($att['media']['image']['src']) ? $att['media']['image']['src'] : '';
                        break;
                    case 'sticker':
                        $type = 'STICKER';
                        $url = isset($att['media']['image']['src']) ? $att['media']['image']['src'] : '';
                        break;
                    case 'video_inline':
                        $type = 'VIDEO';
                        $preview_url = isset($att['media']['image']['src']) ? $att['media']['image']['src'] : '';
                        $url = isset($att['url']) ? $att['url'] : '';
                    default:
                        break;
                }
                $infoAtt = [
                    'type' => $type,
                    'url' => $url,
                    'preview_url' => $preview_url,
                    'entity_type' => COMMENT
                ];
            }
            if (empty($cmt)) {
                $newCmt = new CommentRespository($user->id);
                $newCmt = $newCmt->create($infoNewCmt);

                // return comment data
                $infoNewCmt['id'] = $newCmt;
                $cmt = (object) $infoNewCmt;

                if (count($infoAtt) > 0) {
                    $infoAtt['entity_id'] = $newCmt;
                    $attach = new AttachRespository($user->id);
                    $attach->create($infoAtt);
                    // return data attachment
                    $attachment = (object) $infoAtt;
                    $cmt->attachment = $attachment;
                }
            } else {
                $upCmt = new CommentRespository($user->id);
                $upCmt = $upCmt->update($cmt->id, [
                    'message' => $data['message'],
                    'can_comment' => $data['can_comment'],
                    'can_hide' => $data['can_hide'],
                    'can_like' => $data['can_like'],
                    'can_remove' => $data['can_remove'],
                    'can_reply_privately' => $data['can_reply_privately'],
                    'is_hidden' => $data['is_hidden'],
                    'user_likes' => $data['user_likes']
                ]);
                // return comment data
                $infoNewCmt['id'] = $infoAtt['entity_id'] = $cmt->id;
                $infoNewCmt['attachment'] = $infoAtt;
                $cmt = (object) $infoNewCmt;
            }
            return $cmt;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return null;
        }
    }

    /**
     * do setting of user
     * @param  User   $user      [user]
     * @param  Page   $page      [page do setting]
     * @param  [type] $postId    [post id facebook]
     * @param  [type] $comment   [comment do setting]
     * @return [type]            [not return]
     */
    public function doSettingWithComment(User $user, Page $page, $postId, $comment, $setting)
    {
        if (!empty($setting)) {
            $commentId = $comment['id'];
            $message = $comment['message'];
            $nameCus = isset($comment['from']['name']) ? $comment['from']['name'] : '';
            $ofMe = $comment['from']['id'] == $page->fb_page_id ? true : false;
            if ($setting->hide_all_cmt) {
                HideComment::dispatch($page, $commentId)->onQueue('actionWithFace');
            } else 
            if ($setting->hide_cmt_has_phone && hasPhone($message)) {
                HideComment::dispatch($page, $commentId)->onQueue('actionWithFace');
            } else
            if ($setting->hide_cmt_has_key) {
                $allKey = json_decode(base64_decode($setting->key_cmt_hide));
                foreach ($allKey as $key) {
                    if (strpos($message, $key) !== false) {
                        HideComment::dispatch($page, $commentId)->onQueue('actionWithFace');
                        break;
                    }
                }
            }
            if (!$ofMe) {
                if ($setting->auto_like) {
                    LikeComment::dispatch($page, $commentId)->onQueue('actionWithFace');
                }
                if ($setting->filter_email) {
                    FilterEmailMarketting::dispatch($user, $page, $postId, $commentId, $message)->onQueue('actionWithFace');
                }
                if ($setting->filter_phone) {
                    FilterPhonePMarketting::dispatch($user, $page, $postId, $commentId, $message)->onQueue('actionWithFace');
                }
                if ($setting->auto_comment && !isset($comment['parent'])) {
                    if ($setting->has_time_comment) {
                        $inTimeRange = currentInTimeRange($setting->time_start_comment, $setting->time_end_comment);
                        if ($inTimeRange) {
                            AutoReply::dispatch($page, $commentId, $setting->content_comment, 'COMMENT', $nameCus)->onQueue('actionWithFace');
                        }
                    } else {
                        AutoReply::dispatch($page, $commentId, $setting->content_comment, 'COMMENT', $nameCus)->onQueue('actionWithFace');
                    }
                }
                if ($setting->auto_inbox && isset($comment['can_reply_privately'])) {
                    if ($setting->has_time_inbox) {
                        $inTimeRange = currentInTimeRange($setting->time_start_inbox, $setting->time_end_inbox);
                        if ($inTimeRange) {
                            AutoReply::dispatch($page, $commentId, $setting->content_inbox, 'MESSAGE', $nameCus)->onQueue('actionWithFace');
                        }
                    } else {
                        AutoReply::dispatch($page, $commentId, $setting->content_inbox, 'MESSAGE', $nameCus)->onQueue('actionWithFace');
                    }
                }
            }
        }
    }

    /**
     * create infomation post of comment
     * @param  User   $user     [user has post]
     * @param  Page   $page     [page has post]
     * @param  [type] $fbPostId [facebook page id]
     * @return [type]           [PostPage model]
     */
    public function createPostPage(User $user, Page $page, $fbPostId)
    {
        $post = PostPage::where('user_id', $user->id)
                        ->where('fb_page_id', $page->fb_page_id)
                        ->where("fb_post_id", $fbPostId)
                        ->first();
        if (empty($post)) {
            $info = $this->getInfoPost($fbPostId, $page->page_token);
            $infoNewPost = [
                'user_id' => $user->id,
                'fb_post_id' => $info['id'],
                'fb_page_name' => $info['from']['name'],
                'fb_page_id' => $info['from']['id'],
                'created_time' => strtotime($info['created_time']),
                'updated_time' => strtotime($info['updated_time']),
                'message' => isset($info['message']) ? $info['message'] : '',
                'picture' => isset($info['picture']) ? $info['picture'] : '',
            ];
            $newPost = PostPage::create($infoNewPost);
            $infoNewPost['id'] = $newPost->id;
            $post = (object) $infoNewPost;
        }
        return $post;
    }
}