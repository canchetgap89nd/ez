<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Respositories\Conversation\ConverRespository;
use App\Respositories\Conversation\MessageRespository;
use App\Respositories\Conversation\CommentRespository;
use App\Respositories\Conversation\AttachRespository;
use App\models\Conversation\PostPage;
use App\models\CustomerAndGroup;
use App\models\CustomerReport;
use App\models\Page;
use App\models\Customer;
use Illuminate\Support\Facades\DB;
use App\Traits\ProcessDataFacebook;

class ConversationMongoController extends Controller
{
    use ProcessDataFacebook;

    public function showConversations()
    {
        return view('client.layout');
    }
    	
	/**
	 * get collection conversation
	 * @param  Request $request [condiction filter]
	 * @return [type]           [conversation follow page]
	 */
    public function getListConversations(Request $request)
    {
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $key = $request->keyword;
        $postId = $request->postId;
        $message = json_decode($request->message);
        $comment = json_decode($request->comment);
        $groupId = $request->groupId;
        $unread = json_decode($request->unread);
        $phone = json_decode($request->phone);
        $pageIdsTar = $request->pageIdsTar;
    	$user = Auth::user();
        $adminId = $user->adminId();
        $arrWhere = [];
        if ($key) {
        	array_push($arrWhere, ['from_name','like',"%$key%"]);
        }
        if ($postId) {
            array_push($arrWhere, ['thread_id', $postId]);
        }
        if ($message) {
            array_push($arrWhere, ['type', '=', MESSAGE]);
        }
        if ($comment) {
            array_push($arrWhere, ['type', '=', COMMENT]);
        }
        if ($timeFrom) {
            $timeFrom = strtotime($timeFrom);
            array_push($arrWhere, ['updated_time', '>=', $timeFrom]);
        }
        if ($timeTo) {
            $timeTo = strtotime($timeTo);
            array_push($arrWhere, ['updated_time', '<=', $timeTo]);
        }
        if ($phone) {
            array_push($arrWhere, ['has_phone', '=', true]);
        }
        if ($unread) {
            array_push($arrWhere, ['unread', '=', true]);
        }
        if ($pageIdsTar) {
            $pageIdsTar = explode(',', trim($pageIdsTar));
            foreach ($pageIdsTar as $k => $item) {
                $pageIdsTar[$k] = (binary) $item;
            }
            $pages = DB::table('pages')
                            ->where('user_id', $user->id)
                            ->select('id', 'fb_page_id')
                            ->whereIn('id', $pageIdsTar)
                            ->where('active', 1)
                            ->get();
            $pageIds = [];
            foreach ($pages as $page) {
                array_push($pageIds, $page->fb_page_id);
            }
        } else {
            $pages = DB::table('pages')
                            ->where('user_id', $user->id)
                            ->select('id', 'fb_page_id')
                            ->where('active', true)
                            ->get();
            $pageIds = groupToArray($pages, 'fb_page_id');
        }
        if ($groupId) {
            $customerHasGr = DB::table('customers')
                                ->leftJoin('customer_and_groups', 'customer_and_groups.customer_id', '=', 'customers.id')
                                ->where('customers.user_id', $adminId)
                                ->where('customer_and_groups.group_id', $groupId)
                                ->whereNotNull('customer_and_groups.group_id')
                                ->select('customers.fb_id_cus')
                                ->get();
            $arrCusIds = groupToArray($customerHasGr, 'fb_id_cus');
            $model = new ConverRespository($adminId);
            $conversations = $model->getDB()
                                    ->where("user_id", $adminId)
                                    ->where($arrWhere)
                                    ->whereIn('fb_page_id', $pageIds)
                                    ->whereIn('from_id', $arrCusIds)
                                    ->orderBy('updated_time', 'desc')
                                    ->paginate(10);
        } else {
            $model = new ConverRespository($adminId);
            $conversations = $model->getDB()
                                    ->where("user_id", $adminId)
                                    ->where($arrWhere)
                                    ->whereIn('fb_page_id', $pageIds)
                                    ->orderBy('updated_time', 'desc')
                                    ->paginate(10);
        }
        $arrCusId = array();
        foreach ($conversations as $conver) {
            if (!empty($conver->from_id)) {
                array_push($arrCusId, $conver->from_id);
            }
        }
        $customers = Customer::with('groups')
                                ->where('user_id', $adminId)
                                ->whereIn('fb_id_cus', $arrCusId)
                                ->get();
        foreach ($conversations as $conver) {
            foreach ($customers as $cus) {
                if ($cus->fb_id_cus === $conver->from_id) {
                    $conver->customer = $cus;
                    break;
                }
            }
            switch ($conver->type) {
                case COMMENT:
                    $model = new CommentRespository($adminId);
                    $condiction = [
                        ['user_id', $adminId],
                        ['conver_id', $conver->id],
                        ['unread', true]
                    ];
                    $unread_count = $model->getCount($condiction);
                    break;
                case MESSAGE;
                    $model = new MessageRespository($adminId);
                    $condiction = [
                        ['user_id', $adminId],
                        ['conver_id', $conver->id],
                        ['unread', true]
                    ];
                    $unread_count = $model->getCount($condiction);
                    break;
                default:
                    $unread_count = 0;
                    break;
            }
            $conver->unread_count = $unread_count;
        }
        return response()->json($conversations);
    }

    /**
     * get infomation of conversation
     * @param  [type] $id [id conversation]
     * @return [type]     [all message, comment of conversation]
     */
    public function loadConversation($id)
    {
        $start = microtime(true);
        $user = Auth::user();
        $adminId = $user->adminId();
        $model = new ConverRespository($adminId);
        $conversation = $model->getFirst([
            ['user_id', $adminId],
            ['id', $id]
        ]);
        if ($conversation) {
            $reports = CustomerReport::where('fb_id_cus', $conversation->from_id)->take(20)->get();
            switch ($conversation->type) {
                case MESSAGE:
                    $condiction = [
                        ['user_id', $adminId],
                        ['conver_id', $conversation->id]
                    ];
                    $model = new MessageRespository($adminId);
                    $messages = $model->getPaginate($condiction, array('*'), 'created_time desc', 10);
                    $arrIds = groupToArray($messages);
                    $model = new AttachRespository($adminId);
                    $attachments = $model->getCondictionIn('entity_id', $arrIds, [['entity_type', MESSAGE]]);
                    foreach ($messages as $mess) {
                        $mess->attachments = [];
                        foreach ($attachments as $att) {
                            if ($att->entity_id == $mess->id) {
                                array_push($mess->attachments, $att);
                            }
                        }
                    }
                    $end = microtime(true);
                    \Log::error('Thoi gian load cuoc chat inbox --- ' . round(($end - $start) * 1000));
                    return response()->json([
                        'conversation' => $conversation,
                        'messages' => $messages,
                        'reports' => $reports
                    ]);
                    break;
                case COMMENT:
                    $condiction = [
                        ['user_id', $adminId],
                        ['conver_id', $conversation->id],
                        ['parent_id', '=', null]
                    ];
                    $infoPost = PostPage::find($conversation->post_id);
                    $comments = (new CommentRespository($adminId))
                                    ->getPaginate($condiction, array('*'), 'created_time desc', 10);
                    $arrIds = groupToArray($comments);
                    $childs = (new CommentRespository($adminId))
                                    ->getCondictionIn('parent_id', $arrIds, [], ['*'], 'created_time desc');
                    $arrIdsCh = groupToArray($childs);
                    $arrTt = array_merge($arrIds, $arrIdsCh);
                    $attachs = (new AttachRespository($adminId))->getCondictionIn('entity_id', $arrTt, [['entity_type', COMMENT]]);
                    foreach ($childs as $child) {
                        $child->attachment = null;
                        foreach ($attachs as $att) {
                            if ($att->entity_id == $child->id) {
                                $child->attachment = $att;
                                break;
                            }
                        }
                    }
                    foreach ($comments as $comment) {
                        $comment->childs = [];
                        $comment->attachment = null;
                        foreach ($childs as $child) {
                            if ($child->parent_id == $comment->id) {
                                array_push($comment->childs, $child);
                            }
                        }
                        foreach ($attachs as $att) {
                            if ($att->entity_id == $comment->id) {
                                $comment->attachment = $att;
                                break;
                            }
                        }
                    }
                    $end = microtime(true);
                    \Log::error('Thoi gian load cuoc chat binh luan --- ' . round(($end - $start) * 1000));
                    return response()->json([
                        'conversation' => $conversation,
                        'messages' => $comments,
                        'infoPost' => $infoPost,
                        'reports' => $reports
                    ]);
                    break;
                default:
                    break;
            }
        }
        return response()->json([
            'message' => 'can not find object',
        ], 500);
    }

    /**
     * reply comment or inbox facebook
     * @param  [type]  $converId [conversation id]
     * @param  Request $request  [description]
     * @return [type]            [comment or message model just send]
     */
    public function replyConversation($converId, Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $message = $request->message;
        $target = $request->target;
        $fromPlatform = $request->app;
        $condiction = [
            ['user_id', $adminId],
            ['id', $converId]
        ];
        $conversation = (new ConverRespository($adminId))->getFirst($condiction);
        if ($conversation) {
            switch ($conversation->type) {
                case MESSAGE:
                    $newMess = $this->sendMessage($user, $conversation, $message, $fromPlatform);
                    if ($newMess) {return response()->json($newMess);}
                    break;
                case COMMENT:
                    $newCommt = $this->sendComment($user, $conversation, $message, $target, $fromPlatform);
                    if ($newCommt) {return response()->json($newCommt);}
                    break;
                default:
                    break;
            }
        }
        
        return response()->json([
            'message' => 'Gửi lỗi!'
        ], 500);
    }

    /**
     * send message to inbox facebook
     * @param  User   $user     [user has conversation]
     * @param  [type] $conversation [conversation model]
     * @param  [type] $message  [message]
     * @return [type]           [message just send]
     */
    public function sendMessage(User $user, $conversation, $message, $platform)
    {
        $page = Page::where('user_id', $user->id)
       				->where('fb_page_id', $conversation->fb_page_id)
       				->first();
        $userPa = $user->parent_user_id ? $user->parent()->first() : $user;
        $accessToken = $page->page_token;
        $fbMessageId = $this->sendMessageToFb($conversation->thread_id, $message, $accessToken);
        if (!isset($fbMessageId['success']) || $fbMessageId['success']) {
            $infoMessage = $this->getInfoMessage($fbMessageId, $accessToken);
            if (isset($infoMessage['id'])) {
                return $this->createMessage($userPa, $page, $conversation->id, $infoMessage, false, false, false, $platform, $user->id);
            }
        }
        return null;
    }

    /**
     * send comment
     * @param  User   $user     [user has post comment]
     * @param  [type] $conversation [conversation model]
     * @param  [type] $message  [comment]
     * @param  [type] $target   [facebook id comment parent or facebook post id]
     * @return [type]           [comment just send]
     */
    public function sendComment(User $user, $conversation, $message, $target, $platform)
    {
        $adminId = $user->adminId();
        $page = Page::where('user_id', $user->id)
       				->where('fb_page_id', $conversation->fb_page_id)
       				->first();
        $userPa = $user->parent_user_id ? $user->parent()->first() : $user;
        $condict = [
            ['fb_comment_id', $target],
            ['user_id', $adminId],
            ['conver_id', $conversation->id]
        ];
        $parent = (new CommentRespository($adminId))->getFirst($condict);
        $parentId = $parent ? $parent->id : null;
        $accessToken = $page->page_token;
        $fbCommentId = $this->sendCommentToFb($target, $message, $accessToken);
        \Log::error(print_r($fbCommentId,true));
        if (!isset($fbCommentId['success']) || $fbCommentId['success']) {
            $comment = $this->getInfoComment($fbCommentId, $accessToken);
            if (isset($comment['id'])) {
                return $this->createComment($userPa, $page, $conversation->id, $parentId, $comment, false, false, false, $conversation->post_id, $platform, $user->id);
            }
        }
        return null;
    }

    /**
     * create comment when upload nedia done: photo/video
     * @param  Request $request 
     * @return [type]           [just comment]
     */
    public function createCommentWithIdMedia(Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $converId = $request->converId;
        $fbCommentId = $request->fbCommentId;
        $targetId = $request->targetId;
        $fromPlatform = $request->app;
        $userPa = $user->parent_user_id ? $user->parent()->first() : $user;
        $conversation = (new ConverRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $converId]
                            ]);
        if ($conversation) {
        	$page = Page::where('user_id', $user->id)
	       				->where('fb_page_id', $conversation->fb_page_id)
	       				->first();
            $condict = [
                ['fb_comment_id', $targetId],
                ['user_id', $adminId],
                ['conver_id', $conversation->id]
            ];
            $parent = (new CommentRespository($adminId))
                            ->getFirst($condict);
            $parentId = $parent ? $parent->id : null;
            $comment = $this->getInfoComment($fbCommentId, $page->page_token);
            if (isset($comment['id'])) {
                $newCommt = $this->createComment($userPa, $page, $conversation->id, $parentId, $comment, false, false, false, $conversation->post_id, $fromPlatform, $user->id);
                return response()->json($newCommt);
            }
        }
        return response()->json([
            'message' => 'Không tồn tại cuộc trò chuyện hoặc bình luận!'
        ], 500);
    }

    /**
     * create message when upload media done: photo/video/file
     * @param  Request $request 
     * @return [type]           just message
     */
    public function createMessageWithIdMedia(Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $converId = $request->converId;
        $fbMessageId = $request->fbMessageId;
        $fromPlatform = $request->app;
        $userPa = $user->parent_user_id ? $user->parent()->first() : $user;
        $conversation = (new ConverRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $converId]
                            ]);
        if ($conversation) {
        	$page = Page::where('user_id', $user->id)
	       				->where('fb_page_id', $conversation->fb_page_id)
	       				->first();
            $message = $this->getInfoMessage($fbMessageId, $page->page_token);
            if (isset($message['id'])) {
                $newMess = $this->createMessage($userPa, $page, $conversation->id, $message, false, false, false, $fromPlatform, $user->id);
                return response()->json($newMess);
            }
        }
        return response()->json([
            'message' => 'Không tồn tại cuộc trò chuyện hoặc tin nhắn!'
        ], 500);
    }

    /**
     * destroy comment
     * @param  [type] $converId [conversation id]
     * @param  [type] $id       [comment id]
     * @return [type]           [success or not success]
     */
    public function deleteComment($converId, $id)
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        $conversation = (new ConverRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $converId]
                            ]);
        if ($conversation) {
        	$page = Page::where('user_id', $user->id)
	       				->where('fb_page_id', $conversation->fb_page_id)
	       				->first();
            $comment = (new CommentRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $id]
                            ]);
            if ($comment) {
                $res = $this->deleteCommentFromFb($comment->fb_comment_id, $page->page_token);
                if ($res['success']) {
                    $condict = [
                        ['user_id', $adminId],
                        ['parent_id', $comment->id]
                    ];
                    $update = [
                        'is_remove' => true,
                        'unread' => false,
                        'has_key' => false,
                        'has_phone' => false
                    ];
                    (new CommentRespository($adminId))->updateCondict($condict, $update);
                    (new CommentRespository($adminId))->updateCondict([
                        ['user_id', $adminId],
                        ['id', $id]
                    ], $update);
                    return response()->json([
                        'success' => true
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Không tồn tại đối tượng'
        ], 500);
    }

    /**
     * get message conversation with facebook id of customer
     * @param  [type]  $fromId  [facebook id customer]
     * @param  Request $request 
     * @return [type]           conversation and messages
     */
    public function getMessagesWithIdFb($fromId, Request $request) 
    {
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $page = Page::where('user_id', $user->id)
                    ->where('fb_page_id', $request->fb_page_id)
                    ->first();
        if ($page) {
            $condict = [
                ['user_id', $adminId],
                ['from_id', $fromId],
                ['fb_page_id', $page->fb_page_id],
                ['type', MESSAGE]
            ];
            $conversation = (new ConverRespository($adminId))->getFirst($condict);
            if ($conversation) {
                $condict = [
                    ['user_id', $adminId],
                    ['conver_id', $conversation->id]
                ];
                $messages = (new MessageRespository($adminId))->getPaginate($condict, ['*'], 'created_time desc', 10);
                $arrIds = groupToArray($messages);
                $attachments = (new AttachRespository($adminId))
                                    ->getCondictionIn('entity_id', $arrIds, [['entity_type', MESSAGE]]);
                foreach ($messages as $mess) {
                    $mess->attachments = [];
                    foreach ($attachments as $att) {
                        if ($att->entity_id == $mess->id) {
                            array_push($mess->attachments, $att);
                        }
                    }
                }
    	        return response()->json([
    	            'messages' => $messages,
    	            'conversation' => $conversation,
    	            'page' => $page
    	        ]);
            } else {
            	return response()->json([
    	            'messages' => null,
    	            'conversation' => null,
    	            'page' => $page
    	        ]);
            }
        }
        return response()->json([
            'message' => 'can not find object'
        ], 500);
    }

    /**
     * send inbox to customer with post page facebook
     * @param  [type]  $id      [facebook comment id]
     * @param  Request $request 
     * @return [type]           
     */
    public function sendMessagesWithPost($id, Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $message = $request->message;
        $fromPlatform = $request->app;
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $page = Page::where('user_id', $user->id)
                        ->where('fb_page_id', $request->fb_page_id)
                        ->first();
        if ($page) {
            $userPa = $user->parent_user_id ? $user->parent()->first() : $user;
            $token = $page->page_token;
            $comment = $this->getInfoComment($id, $token);
            if ($comment['can_reply_privately']) {
                $mess = $this->sendMessagesWithPostToFb($id, $message, $token);
                if (isset($mess['id'])) {
                    $comment = $this->getInfoComment($id, $token);
                    if (isset($comment['private_reply_conversation']['id'])) {
                        $threadId = $comment['private_reply_conversation']['id'];
                        $infoMessage = $this->getJustMessage($threadId, $token, time());
                        $fromId = $infoMessage['to']['data'][0]['id'];
                        $fromName = $infoMessage['to']['data'][0]['name'];
                        $condict = [
                            ['user_id', $adminId],
                            ['fb_page_id', $page->fb_page_id],
                            ['thread_id', $threadId],
                            ["type", MESSAGE],
                        ];
                        $conversation = (new ConverRespository($adminId))->getFirst($condict);
                        if ($conversation) {
                            $newMess = $this->createMessage($userPa, $page, $conversation->id, $infoMessage, false, false, false, $fromPlatform, $user->id);
                        } else {
                            $timeUpdate = strtotime($infoMessage['created_time']);
                            $lastMessage = _substr($infoMessage['message'], 150);
                            $newConver = $this->createConver($user, $page, $fromId, $fromName, $threadId, false, false, false, false, false, $timeUpdate, $lastMessage, MESSAGE, false);
                            $newMess = $this->createMessage($userPa, $page, $newConver->id, $infoMessage, false, false, false, $fromPlatform, $user->id);
                            $conversation = $newConver;
                        }
                        return response()->json([
                            'success' => true,
                            'data' => $newMess,
                            'conversation' => $conversation
                        ]);
                    }
                    return response()->json([
                        'success' => true,
                        'waitting' => true
                    ]);
                }
            } else {
                $threadId = isset($comment['private_reply_conversation']) ? $comment['private_reply_conversation']['id'] : null;
                $condict = [
                    ['user_id', $adminId],
                    ['fb_page_id', $page->fb_page_id],
                    ['thread_id', $threadId],
                    ["type", MESSAGE],
                ];
                $conversation = (new ConverRespository($adminId))->getFirst($condict);
                if ($conversation) {
                    $mess = $this->sendMessage($user, $conversation, $message, $fromPlatform);
                    if ($mess) {
                        return response()->json([
                            'success' => true,
                            'data' => $mess,
                            'conversation' => $conversation
                        ]);
                    }
                }
                return response()->json([
                    'message' => 'Can not send message to user'
                ], ERR_INTER_CODE);
            }
        }
        
        return response()->json([
            'message' => 'Error send message'
        ], ERR_INTER_CODE);
    }

    /**
     * like comment 
     * @param  [type]  $commentId [comment id ]
     * @param  Request $request   
     * @return [type]             [success or not success]
     */
    public function hideComment($commentId, Request $request)
    {
        $isHide = json_decode($request->isHide);
        $converId = $request->converId;
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $conversation = (new ConverRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $converId]
                            ]);
        $page = Page::where('user_id', $user->id)
                        ->where('fb_page_id', $request->fb_page_id)
                        ->first();
        if ($conversation && $page) {
            $comment = (new CommentRespository($adminId))->getFirst([
                ['conver_id', $conversation->id],
                ['id', $commentId]
            ]);
            if ($comment) {
                $res = $this->hideCommentOnFb($comment->fb_comment_id, $isHide, $page->page_token);
                if ($res['success']) {
                    $infoComment = $this->getInfoComment($comment->fb_comment_id, $page->page_token);
                    $cmt = $this->createComment($user, $page, $conversation->id, null, $infoComment, false, false, false, $conversation->post_id, $comment->from_platform, $comment->staff_reply_id);
                    return response()->json([
                        'success' => true,
                        'data' => $cmt
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Không tìm thấy đối tượng'
        ], 500);
    }

    public function likeComment($commentId, Request $request)
    {
        $isLike = json_decode($request->isLike);
        $converId = $request->converId;
        $user = User::find(Auth::id());
        $adminId = $user->adminId();
        $conversation = (new ConverRespository($adminId))
                            ->getFirst([
                                ['user_id', $adminId],
                                ['id', $converId]
                            ]);
        $page = Page::where('user_id', $user->id)
                        ->where('fb_page_id', $request->fb_page_id)
                        ->first();
        if ($conversation && $page) {
            $comment = (new CommentRespository($adminId))->getFirst([
                ['conver_id', $conversation->id],
                ['id', $commentId]
            ]);
            if ($comment) {
                if ($isLike) {
                    $res = $this->likeCommentOnFb($comment->fb_comment_id, $page->page_token);
                } else {
                    $res = $this->dislikeCommentOnFb($comment->fb_comment_id, $page->page_token);
                }
                if ($res['success']) {
                    $infoComment = $this->getInfoComment($comment->fb_comment_id, $page->page_token);
                    $cmt = $this->createComment($user, $page, $conversation->id, null, $infoComment, false, false, false, $conversation->post_id, $comment->from_platform, $comment->staff_reply_id);
                    return response()->json([
                        'success' => true,
                        'data' => $cmt
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Không tìm thấy đối tượng'
        ], 500);
    }

    /**
     * mark read conversation; update unread = false
     * @param  [type] $entityId [description]
     * @return [type]           [description]
     */
    public function markRead($entityId = null)
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        if ($entityId) {
            $condictConver = [
                ['user_id', $adminId],
                ['id', $entityId]
            ];
            $condiction = [
                ['user_id', $adminId],
                ['conver_id', $entityId],
                ['unread', true]
            ];
            $update = [
                'unread' => false,
                'staff_reply_id' => $user->id
            ];
            (new CommentRespository($adminId))->updateCondict($condiction, $update);
            (new MessageRespository($adminId))->updateCondict($condiction, $update);
            (new ConverRespository($adminId))->updateCondict($condictConver, ['unread' => false]);
        } else {
            $condict = [
                ['user_id', $adminId],
                ['unread', true]
            ];
            $update = [
                'unread' => false
            ];
            (new ConverRespository($adminId))->updateCondict($condict, $update);
            (new CommentRespository($adminId))->updateCondict(
                $condict,
                [
                    'unread' => false,
                    'staff_reply_id' => $user->id
                ]
            );
            (new MessageRespository($adminId))->updateCondict(
                $condict,
                [
                    'unread' => false,
                    'staff_reply_id' => $user->id
                ]
            );
        } 
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * count all message and comment unread
     * @return [type] count number unread
     */
    public function countUnread()
    {
        $user = Auth::user();
        $adminId = $user->adminId();
        $condict = [
            ['user_id', $adminId],
            ['unread', true]
        ];
        $commentCount = (new CommentRespository($adminId))->getCount($condict);
        $messageCount = (new MessageRespository($adminId))->getCount($condict);
        return $commentCount + $messageCount;
    }

    /**
     * get count unread and return json
     * @return [type] 
     */
    public function getCountUnread()
    {
        $count = $this->countUnread();
        return response()->json([
            'count_unread' => $count
        ]);
    }

    /**
     * create some conversation for account new
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function createSomeConversationForAccountNew(User $user)
    {
        $adminId = $user->adminId();
        $pages = $user->pages()->where('active', 1)->take(3)->get();
        foreach ($pages as $page) {
            $res = $this->getListConversationOfPage($page->fb_page_id, $page->page_token, 1);
            $res = isset($res['data'][0]) ? $res['data'][0] : [];
            if (count($res)) {
                $senders = $res['senders']['data'];
                $fromId = $page->fb_page_id;
                $fromName = $page->page_name;
                foreach ($senders as $sender) {
                    if ($sender['id'] != $page->fb_page_id) {
                        $fromId = $sender['id'];
                        $fromName = $sender['name'];
                        break;
                    }
                }
                $newConverId = (new ConverRespository($adminId))->create([
                    'user_id' => $adminId,
                    'from_id' => $fromId,
                    'from_name' => $fromName,
                    'thread_id' => $res['id'],
                    'last_message' => isset($res['snippet']) ? _substr($res['snippet'], 150) : '',
                    'type' => MESSAGE,
                    'updated_time' => strtotime($res['updated_time']),
                    'unreply' => false,
                    'fb_page_id' => $page->fb_page_id
                ]);
                $messages = $this->getListMessages($res['id'], $page->page_token, 2);
                foreach ($messages as $mess) {
                    $this->createMessage($user, $page, $newConverId, $mess, false, false, false);
                }
            }
        }
    }
}
