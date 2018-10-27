<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\models\Mongo\Conversation;
use App\models\Mongo\TranferUser;
use App\models\Mongo\Message;
use App\models\Mongo\Comment;
use App\models\Mongo\Attachment;
use App\models\Mongo\PostPage as PostPageMg;
use App\Respositories\Conversation\ConverRespository;
use App\Respositories\Conversation\MessageRespository;
use App\Respositories\Conversation\CommentRespository;
use App\Respositories\Conversation\AttachRespository;
use App\models\Conversation\PostPage;
use Illuminate\Support\Facades\Redis;

class TransferDataController extends Controller
{
    public function conversations(int $userId)
    {
        try {
        	$conversation = Conversation::where('user_id', $userId)->where('tranfer', '<>', true)->orderByDesc('updated_time')->first();
            if ($conversation) {
                var_dump($conversation->from_id);
                var_dump($conversation->from_name);
                var_dump($conversation->fb_page_id);
                var_dump($conversation->thread_id);
                var_dump($conversation->id);
                var_dump($conversation->tranfer);
        		$condict = [
        			['user_id', $userId],
        			['fb_page_id', $conversation->fb_page_id],
        			['from_id', $conversation->from_id],
                    ['thread_id', $conversation->thread_id]
        		];
        		$conver = (new ConverRespository($userId))->getFirst($condict);
        		if (empty($conver)) {
        			$type = $conversation->type == 'MESSAGE' ? MESSAGE : COMMENT;
        			$post = PostPageMg::find($conversation->post_id);
                    $postId = null;
                    if ($post) {
            			$newPost = PostPage::create([
            				'user_id' => $userId,
            				'fb_post_id' => $post->fb_post_id,
            				'fb_page_name' => $post->fb_page_name,
            				'fb_page_id' => $post->fb_page_id,
            				'message' => $post->message,
            				'picture' => $post->picture,
            				'created_time' => $post->created_time,
            				'updated_time' => $post->updated_time
            			]);
                        $postId = $newPost->id;
                    }
        			$newConver = (new ConverRespository($userId))->create([
        				'user_id' => $userId,
    		            'fb_page_id' => $conversation->fb_page_id,
    		            'from_id' => $conversation->from_id,
    		            'from_name' => $conversation->from_name,
    		            'thread_id' => $conversation->thread_id,
    		            'post_id' => $postId,
    		            'last_message' => $conversation->last_message,
    		            'updated_time' => $conversation->updated_time,
    		            'unread' => $conversation->unread ? $conversation->unread : false,
    		            'has_phone' => $conversation->has_phone ? $conversation->has_phone : false,
    		            'has_key' => $conversation->has_key ? $conversation->has_key : false,
    		            'type' => $type,
    		            'unreply' => $conversation->unreply ? $conversation->unreply : false,
    		            'has_order' => $conversation->has_order ? $conversation->has_order : false,
    		            'has_note' => $conversation->has_note ? $conversation->has_note : false,
    		            'is_multiple_chat' => $conversation->is_multiple_chat ? $conversation->is_multiple_chat : false
        			]);
        			$update = $conversation->update([
        				'tranfer' => true
        			]);
                    if ($type == MESSAGE) {
            			$messages = Message::where('user_id', $userId)->where('conver_id', $conversation->id)->orderByDesc('created_time')->take(10)->get();
            			foreach ($messages as $mess) {
            				$newMesId = (new MessageRespository($userId))->create([
            					'user_id' => $userId,
            					'conver_id' => $newConver,
            					'from_id' => $mess->from_id,
            					'from_name' => $mess->from_name,
            					'fb_message_id' => $mess->fb_message_id,
            					'message' => $mess->message,
            					'has_phone' => $mess->has_phone ? $mess->has_phone : false,
            					'has_key' => $mess->has_key,
            					'unread' => $mess->unread ? $mess->unread : false,
            					'staff_reply_id' => $mess->staff_reply_id,
            					'fb_page_id' => $mess->fb_page_id,
            					'from_platform' => $mess->from_platform,
            					'created_time' => $mess->created_time
            				]);
            				$atts = Attachment::where('entity_id', $mess->id)->where('entity_type', 'MESSAGE')->get();
            				$arrAtts = [];
            				foreach ($atts as $att) {
            					array_push($arrAtts, [
            						'entity_id' => $newMesId,
            						'url' => $att->url,
            						'preview_url' => $att->preview_url,
            						'file_url' => $att->file_url,
            						'entity_type' => MESSAGE,
            						'type' => $att->type
            					]);
            				}
            				(new AttachRespository($userId))->insert($arrAtts);
            			}
                    } else {
            			$comments = Comment::where('user_id', $userId)->whereNull('parent_id')->where('conver_id', $conversation->id)->orderByDesc('created_time')->take(10)->get();
            			foreach ($comments as $cmt) {
            				$newCmtId = (new CommentRespository($userId))->create([
            					'user_id' => $userId,
            					'fb_page_id' => $cmt->fb_page_id,
            					'conver_id' => $newConver,
            					'fb_comment_id' => $cmt->fb_comment_id,
            					'parent_id' => null,
            					'from_id' => $cmt->from_id,
            					'from_name' => $cmt->from_name,
            					'message' => $cmt->message,
            					'can_reply_privately' => $cmt->can_reply_privately ? $cmt->can_reply_privately : false,
            					'is_hidden' => $cmt->is_hidden ? $cmt->is_hidden : false,
            					'is_remove' => $cmt->is_remove ? $cmt->is_remove : false,
            					'user_likes' => $cmt->user_likes ? $cmt->user_likes : false,
            					'can_hide' => $cmt->can_hide ? $cmt->can_hide : false,
            					'can_like' => $cmt->can_like ? $cmt->can_like : false,
            					'can_comment' => $cmt->can_comment ? $cmt->can_comment : false,
            					'can_remove' => $cmt->can_remove ? $cmt->can_remove : false,
            					'has_phone' => $cmt->has_phone ? $cmt->has_phone : false,
            					'has_key' => $cmt->has_key ? $cmt->has_key : false,
            					'unread' => $cmt->unread ? $cmt->unread : false,
            					'created_time' => $cmt->created_time,
            					'staff_reply_id' => $cmt->staff_reply_id,
            					'post_id' => $postId,
            					'from_platform' => $cmt->from_platform
            				]);
                            $childs = Comment::where('user_id', $userId)->where('conver_id', $conversation->id)->where('parent_id', $cmt->id)->get();
                            foreach ($childs as $child) {
                                $newIdChi = (new CommentRespository($userId))->create([
                                    'user_id' => $userId,
                                    'fb_page_id' => $child->fb_page_id,
                                    'conver_id' => $newConver,
                                    'fb_comment_id' => $child->fb_comment_id,
                                    'parent_id' => $newCmtId,
                                    'from_id' => $child->from_id,
                                    'from_name' => $child->from_name,
                                    'message' => $child->message,
                                    'can_reply_privately' => $child->can_reply_privately ? $child->can_reply_privately : false,
                                    'is_hidden' => $child->is_hidden ? $child->is_hidden : false,
                                    'is_remove' => $child->is_remove ? $child->is_remove : false,
                                    'user_likes' => $child->user_likes ? $child->user_likes : false,
                                    'can_hide' => $child->can_hide ? $child->can_hide : false,
                                    'can_like' => $child->can_like ? $child->can_like : false,
                                    'can_comment' => $child->can_comment ? $child->can_comment : false,
                                    'can_remove' => $child->can_remove ? $child->can_remove : false,
                                    'has_phone' => $child->has_phone ? $child->has_phone : false,
                                    'has_key' => $child->has_key ? $child->has_key : false,
                                    'unread' => $child->unread ? $child->unread : false,
                                    'created_time' => $child->created_time,
                                    'staff_reply_id' => $child->staff_reply_id,
                                    'post_id' => $postId,
                                    'from_platform' => $child->from_platform
                                ]);
                                $attsChi = Attachment::where('entity_id', $child->id)->where('entity_type', 'COMMENT')->get();
                                $arrNewAtts = [];
                                foreach ($attsChi as $att) {
                                    array_push($arrNewAtts, [
                                        'entity_id' => $newIdChi,
                                        'url' => $att->url,
                                        'preview_url' => $att->preview_url,
                                        'file_url' => $att->file_url,
                                        'entity_type' => COMMENT,
                                        'type' => $att->type
                                    ]);
                                }
                                (new AttachRespository($userId))->insert($arrNewAtts);
                            }
            				$atts = Attachment::where('entity_id', $cmt->id)->where('entity_type', 'COMMENT')->get();
            				$arrAtts = [];
            				foreach ($atts as $att) {
            					array_push($arrAtts, [
            						'entity_id' => $newCmtId,
            						'url' => $att->url,
            						'preview_url' => $att->preview_url,
            						'file_url' => $att->file_url,
            						'entity_type' => COMMENT,
            						'type' => $att->type
            					]);
            				}
            				(new AttachRespository($userId))->insert($arrAtts);
            			}
                    }
                } else {
                    $update = $conversation->update([
                        'tranfer' => true
                    ]);
                }
                $tranfer = TranferUser::where('user_id', $userId)->first();
                $upCount = $tranfer ? json_decode($tranfer->conver_count) + 1 : 1;
                echo "<br>";
                var_dump($upCount);
                if ($tranfer) {
                    $tranfer->update(['conver_count' => $upCount]);
                } else {
                    TranferUser::create([
                        'user_id' => $userId,
                        'conver_count' => $upCount
                    ]);
                }
                echo "<br>";
                return "Success!";
        	}
            return "Fail! not has data";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function tranferEveryUser()
    {
        // Redis::set('user:tranfer:every', '');
        $useCheck = json_decode(Redis::get('user:tranfer:every'));
        if ($useCheck) {
            $user = DB::table('users')->where('id', '>', $useCheck)->whereMonth('updated_at', '10')->orderBy('id')->first();
        } else {
            $user = DB::table('users')->whereMonth('updated_at', '10')->orderBy('id')->first();
        }
        var_dump($user);
        if ($user) {
            $tranfer = TranferUser::where('user_id', $user->id)->first();
            $count = $tranfer ? json_decode($tranfer->conver_count) : 1;
            if ($count < 15) {
                $this->conversations($user->id);
                if ($tranfer) {
                    $count += 1;
                    $tranfer->update(['conver_count' => $count]);
                } else {
                    TranferUser::create([
                        'user_id' => $user->id,
                        'conver_count' => $count
                    ]);
                }
                echo "<br>";
                return "Done " . $user->id;
            } else {
                echo "<br>";
                Redis::set('user:tranfer:every', $user->id);
                return "Clear " . $user->id;
            }
        }
        return "no has user update tranfer";
    }

    public function resetCheckOnlineUser()
    {
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            Redis::set('user:online:' . $user->id, '');
        }
        echo "done!";
    }

    public function resetRedis()
    {
        Redis::set('user:tranfer:every', null);
    }
}
