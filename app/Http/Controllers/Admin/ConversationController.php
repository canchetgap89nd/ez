<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Mongo\Conversation;
use App\models\Mongo\Comment;
use App\models\Mongo\Message;
use Carbon\Carbon;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
    	$timeFrom = $request->timeFrom;
    	$timeTo = $request->timeTo;
    	$platform = $request->platform;
    	if ($timeFrom && $timeTo) {
    		$timeFrom = Carbon::createFromTimestamp(strtotime($timeFrom));
			$timeTo = Carbon::createFromTimestamp(strtotime($timeTo));
    	} else {
            $timeFrom = Carbon::now()->subDays(30);
            $timeTo = Carbon::now();
        }
        $strTimeF = strtotime($timeFrom);
        $strTimeT = strtotime($timeTo);
		$totalConversation = Conversation::count();
		$totalComment = Comment::count();
		$totalMessage = Message::count();
    	$conversationsOfRangers = Conversation::raw(function($collection) use ($strTimeF, $strTimeT)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'updated_time' => [
    													'$gte' => $strTimeF,
    													'$lte' => $strTimeT
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
    	$conversationsOfRangers = $this->fixDayAndConvertTime($conversationsOfRangers, $timeFrom, $timeTo);
    	$commentsOfRangers = Comment::raw(function($collection) use ($strTimeF, $strTimeT)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $strTimeF,
    													'$lte' => $strTimeT
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
    	$commentsOfRangers = $this->fixDayAndConvertTime($commentsOfRangers, $timeFrom, $timeTo);
    	$messagesOfRangers = Message::raw(function($collection) use ($strTimeF, $strTimeT)
    	{
    		return $collection->aggregate([
    										[
    											'$match' => [
    												'created_time' => [
    													'$gte' => $strTimeF,
    													'$lte' => $strTimeT
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
    	$messagesOfRangers = $this->fixDayAndConvertTime($messagesOfRangers, $timeFrom, $timeTo);
    	return view('admin.conversations', compact('totalConversation', 'totalComment', 'totalMessage', 'timeFrom', 'timeTo', 'conversationsOfRangers', 'commentsOfRangers', 'messagesOfRangers'));
    }

    /**
     * convert name column _id to date and fix total = 0 with day not has data
     * @param  [type]  $data   [data collection]
     * @param  [type]  $numDay [number day need get]
     * @param  integer $began  [day begin, format date]
     * @return [type]          [collection array]
     */
    public function fixDayAndConvertTime($data, $timeFrom, $timeTo)
    {
    	$data = json_encode($data);
        $data = json_decode($data, true);
        $timeFrom = strtotime($timeFrom);
        $timeFrom = date('Y-m-d', $timeFrom);
        $fday = strtotime($timeFrom);
        $tday = strtotime($timeFrom);
        $day = $fday;
        $result = [];
        $date = Carbon::parse($timeFrom);
        $now = Carbon::parse($timeTo);
        $diff = $date->diffInDays($now);
        for ($i=1; $i <= $diff + 1; $i++) {
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
            $day = strtotime('+1 days', $day);
        }
        return $result;
    }
}
