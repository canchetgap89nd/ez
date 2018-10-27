<?php

namespace App\Traits;

use App\models\Admin\PackageAndPayment\UserPayment;

trait GenerateTrait
{
    protected $listUserAgents = array(
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6)    Gecko/20070725 Firefox/2.0.0.6",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
        "Opera/9.20 (Windows NT 6.0; U; en)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
        "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48",
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'
    );

    protected $listHeader = [
        'Connection: keep-alive',
        'Keep-Alive: 300',
        "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
        "Accept-Language: vi,vi;q=0.5"
    ];

    public function generateUserAgents()
    {
        $userAgents = $this->listUserAgents;
        $random = rand(0, count($userAgents) - 1);
        return $userAgents[$random];
    }

    public function generatePayCode()
    {
        $code = '' . rand(100, 10000) . '';
        $has = UserPayment::where('pay_code', $code)->first();
        $ind = 1;
        while (!empty($has) && $ind < 1000) {
            $code = '' . rand(100, 10000) . '';
            $ind++;
        }
        if ($ind >= 1000) {
            \Log::info(print_r('limit code payment', true));
        }
        return $code;
    }
}