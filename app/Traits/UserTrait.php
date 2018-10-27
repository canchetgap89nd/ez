<?php

namespace App\Traits;

use App\User;
use App\models\Mongo\ActivityUser;
use App\models\Page;

trait UserTrait
{
    public function inPageLimit($user)
    {
        $pagesActiveCount = $user->pages()->where('active', true)->count();
        if ($pagesActiveCount <= $this->getPageLimit($user)) {
            return true;
        }
        return false;
    }

    public function getPageLimit(User $user)
    {
        $packages = $user->packages()->get();
        $pageCount = isset($packages[0]->page_limit) ? $packages[0]->page_limit : PAGE_LIMIT;
        foreach ($packages as $pack) {
            if ($pack->page_limit > $pageCount && strtotime($pack->pivot->expire_at) > time()) {
                $pageCount = $pack->page_limit;
            }
        }
        return $pageCount;
    }

    public function saveHistoryActivity(User $user, $action, $fromPlatform, $desc = null)
    {
        switch ($action) {
            case 'LOGIN':
                $actionCode = 1;
                break;
            case 'LOGOUT':
                $actionCode = 2;
            case 'REGISTERED':
                $actionCode = 3;
            case 'FIND_PHONE':
                $actionCode = 4;
            case 'FIND_FB':
                $actionCode = 5;
            default:
                $actionCode = 0;
                break;
        }
        ActivityUser::create([
            'user_id' => $user->id,
            'activity_code' => $actionCode,
            'activity' => $action,
            'from_platform' => $fromPlatform,
            'activity_desc' => $desc
        ]);
    }

    /**
     * generate user with facebook ID
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function convertStrWithId($id)
    {
        //reverse id facebook
        $str = strval($id);
        $result['username'] = '';
        $result['password'] = '';
        $result['username'] = strrev($str);
        //get 3 character first of id facebook
        $a = substr($str, 0, 3);
        //get 4 character end of id facebook
        $b = substr($str, strlen($str) - 4, 4);
        $mid = strlen($str)%2;
        $c = substr($str, strlen($str) - 5, 3);
        $result['password'] = $b . $a . $mid . $c . '_&cs';
        return $result;
    }

    public function updateWhenLogin(User $user, $infoFb, string $accessToken)
    {
        $pages = $this->getAllPages($accessToken);
        //if user not setup name
        $updateEmail = isset($infoFb['email']) ? $infoFb['email'] : $user->user_fb_email;
        $updateName = !$user->name ? $infoFb['name'] : $user->name;
        if (!$user->name || !$user->user_fb_email) {
            $user->update([
                'name' => $updateName,
                'user_fb_email' => $updateEmail,
                'user_access_token' => $accessToken
            ]);
        } else {
            $user->update([
                'user_fb_email' => $updateEmail,
                'user_access_token' => $accessToken
            ]);
        }
        $pagesOf = $user->pages()->get();
        $timeNow = date('Y-m-d H:i:s', time());
        $newPages = [];
        foreach ($pages as $page) {
            $ch = false;
            // fix error not has acccess_token from facebook
            $page_token = isset($page['access_token']) ? $page['access_token'] : '';
            foreach ($pagesOf as $entity) {
                if ($page['id'] == $entity->fb_page_id) {
                    $entity->update([
                        'page_name' => $page['name'],
                        'page_token' => $page_token ? $page_token : $entity->page_token,
                        'page_category' => $page['category']
                    ]);
                    $ch = true;
                    break;
                }
            }
            if (!$ch) {
                array_push($newPages, [
                    'user_id' => $user->id,
                    'fb_page_id' => $page['id'],
                    'page_name' => $page['name'],
                    'page_token' => $page_token,
                    'active' => 0,
                    'page_category' => $page['category'],
                    'created_at' => $timeNow
                ]);
            }
        }
        //update more page if user is admin
        if ($user->isAdmin()) {
            Page::insert($newPages);
        }
        // delete page not has role manager
        foreach ($pagesOf as $page) {
            $has = false;
            foreach ($pages as $item) {
                if ($item['id'] == $page->fb_page_id) {
                    $has = true;
                    break;
                }
            }
            if (!$has) {
                $page->delete();
            }
        }
    }
}