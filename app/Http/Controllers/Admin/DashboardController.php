<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\models\Mongo\Conversation;
use App\models\Mongo\Comment;
use App\models\Mongo\Message;
use App\models\PostBlog;
use App\models\Customer;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
    	$time1 = date('Y-m-d H:i:s', time());
    	$time2 = date('Y-m-d H:i:s', strtotime('-7 days', time()));
    	$time3 = date('Y-m-d H:i:s', strtotime('-14 days', time()));
    	$accounts = $this->countAccountRecent($time1, $time2, $time3);
		$conversations = $this->countConverRecent($time1, $time2, $time3);
		$comments = $this->countCommentRecent($time1, $time2, $time3);
		$messages = $this->countMessageRecent($time1, $time2, $time3);
        $posts = PostBlog::count();
        $customers = Customer::count();
    	return view('admin.dashboard', compact('accounts', 'conversations', 'comments', 'messages', 'posts', 'customers'));
    }

    /**
     * fix day has not data in collection groupBy
     * @param  [array]  $data   [data collection]
     * @param  [type]  $numDay [number day need get]
     * @param  integer $began  [day begin]
     * @return [array]          [array collection full day]
     */
    public function fixDayNotHasData($data, $numDay, $began = 0)
    {
    	$data = json_encode($data);
    	$data = json_decode($data, true);
    	$today = date('Y-m-d', time());
		$sday = strtotime($today);
		$day = strtotime('-' . $began . ' days' ,$sday);
		$result = [];
		for ($i=1; $i <= $numDay; $i++) {
			$hasThis = false;
			foreach ($data as $item) {
				if ($day === strtotime($item['date'])) {
					$hasThis = true;
					break;
				}
			}
			if ($hasThis) {
				array_push($result, $item);
			} else {
				$tg['date'] = date('Y-m-d', $day);
				$tg['total'] = 0;
				array_push($result, $tg);
			}
			$day = strtotime('-1 days', $day);
		}
		return $result;
    }

    /**
     * convert name column _id to date and fix total = 0 with day not has data
     * @param  [type]  $data   [data collection]
     * @param  [type]  $numDay [number day need get]
     * @param  integer $began  [day begin, format date]
     * @return [type]          [collection array]
     */
    public function fixDayAndConvertTime($data, $numDay, $began = 0)
    {
    	$data = json_encode($data);
    	$data = json_decode($data, true);
    	$today = date('Y-m-d', time());
		$sday = strtotime($today);
		$day = strtotime('-' . $began . ' days' ,$sday);
		$result = [];
		for ($i=1; $i <= $numDay; $i++) {
			$hasThis = false;
			foreach ($data as $item) {
				if ($day === strtotime($item['_id'])) {
					$hasThis = true;
					break;
				}
			}
			if ($hasThis) {
				$tg['date'] = date('Y-m-d', $day);
				$tg['total'] = $item['total'];
				array_push($result, $tg);
			} else {
				$tg['date'] = date('Y-m-d', $day);
				$tg['total'] = 0;
				array_push($result, $tg);
			}
			$day = strtotime('-1 days', $day);
		}
		return $result;
    }

    public function countAccountRecent($time1, $time2, $time3)
    {
    	$usersThisWeek = DB::table('users')
    					->where([
    						['created_at', '>=', $time2],
    						['created_at', '<=', $time1]
    					])
				      	->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
				      	->groupBy('date')
				      	->get()
				      	->toArray();
		$usersLastWeek = DB::table('users')
    					->where([
    						['created_at', '<=', $time2],
    						['created_at', '>=', $time3]
    					])
				      	->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
				      	->groupBy('date')
				      	->get()
				      	->toArray();
		$usersThisWeek = $this->fixDayNotHasData($usersThisWeek, 7);
		$usersLastWeek = $this->fixDayNotHasData($usersLastWeek, 7, 7);
		$total = DB::table('users')->count();
		return [
			'thisWeek' => $usersThisWeek,
			'lastWeek' => $usersLastWeek,
			'total' => $total
		];
    }

    public function countConverRecent($time1, $time2, $time3)
    {
    	$time1 = strtotime($time1);
    	$time2 = strtotime($time2);
    	$time3 = strtotime($time3);
    	$conversThisWeek = Conversation::raw(function($collection) use ($time2, $time1)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'updated_time' => [
    													'$gte' => $time2,
    													'$lte' => $time1
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$updated_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$conversLastWeek = Conversation::raw(function($collection) use ($time2, $time3)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'updated_time' => [
    													'$gte' => $time3,
    													'$lte' => $time2
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$updated_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$conversThisWeek = $this->fixDayAndConvertTime($conversThisWeek, 7);
		$conversLastWeek = $this->fixDayAndConvertTime($conversLastWeek, 7, 7);
		$total = Conversation::count();
		return [
			'thisWeek' => $conversThisWeek,
			'lastWeek' => $conversLastWeek,
			'total' => $total
		];
    }

    public function countCommentRecent($time1, $time2, $time3)
    {
    	$time1 = strtotime($time1);
    	$time2 = strtotime($time2);
    	$time3 = strtotime($time3);
    	$commentThisWeek = Comment::raw(function($collection) use ($time2, $time1)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $time2,
    													'$lte' => $time1
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$created_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$commentLastWeek = Comment::raw(function($collection) use ($time2, $time3)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $time3,
    													'$lte' => $time2
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$created_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$commentThisWeek = $this->fixDayAndConvertTime($commentThisWeek, 7);
		$commentLastWeek = $this->fixDayAndConvertTime($commentLastWeek, 7, 7);
		$total = Comment::count();
		return [
			'thisWeek' => $commentThisWeek,
			'lastWeek' => $commentLastWeek,
			'total' => $total
		];
    }

    public function countMessageRecent($time1, $time2, $time3)
    {
    	$time1 = strtotime($time1);
    	$time2 = strtotime($time2);
    	$time3 = strtotime($time3);
    	$messageThisWeek = Message::raw(function($collection) use ($time2, $time1)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $time2,
    													'$lte' => $time1
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$created_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$messageLastWeek = Message::raw(function($collection) use ($time2, $time3)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $time3,
    													'$lte' => $time2
    												]
    											]
    										],
    										[
    											'$group' => [
    												'_id' => [
    													'$dateToString' => [
    														'format' => '%Y-%m-%d',
    														'date' => [
    															'$toDate' => [
    																'$multiply' => [
    																	1000, '$created_time'
    																]
    															]
    														],
    														'timezone' => 'Asia/Saigon'
    													]
    												],
    												'total' => [
    													'$sum' => 1
    												]
    											]
    										]
    									]);
    	})
    	->toArray();
		$messageThisWeek = $this->fixDayAndConvertTime($messageThisWeek, 7);
		$messageLastWeek = $this->fixDayAndConvertTime($messageLastWeek, 7, 7);
		$total = Message::count();
		return [
			'thisWeek' => $messageThisWeek,
			'lastWeek' => $messageLastWeek,
			'total' => $total
		];
    }
}
