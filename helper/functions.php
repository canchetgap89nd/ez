<?php 

/**
 * Trả về đoạn string theo độ dài và số từ cho trước mà không làm mất nghĩa của câu văn
 * @param  [type]  $str     [đoạn string đầu vào]
 * @param  [type]  $length  [độ dài string cần cắt ra]
 * @param  integer $minword [số từ tối đa ở đầu ra]
 * @return [type]           [string]
 */
function _substr($str, $length, $minword = 3)
{
    $sub = '';
    $len = 0;
    foreach (explode(' ', $str) as $key => $word)
    {
        $part = (($sub != '') ? ' ' : '') . $word;
        $sub .= $part;
        $len += strlen($part);
        if (($key + 1 > $minword) && (strlen($sub) >= $length - 10))
        {
          break;
        }
    }
    $result = $sub . (($len < strlen($str)) ? '...' : '');
    if (strlen($result) > $length) {
        return substr($result, 0, 1).'...';
    }
    return $result;
}

function hasPhone($str)
{
    if ($str) {
        $str = str_replace(array('-', '.', ' '), '', $str);
        if (!preg_match('/'.PHONE_PATTERN.'/', $str)) return false;
        return true;
    }
    return false;
}

function formatPriceValue($price)
{
    return round($price, 4);
}

function getEmailFromText($string) {
    //$string = "bla bla pickachu@domain.com MIME-Version: balbasur@domain.com bla bla bla";
    $matches = array();
    $pattern = '/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i';
    preg_match_all($pattern,$string,$matches);
    if(isset($matches[0])) $matches = $matches[0];
    return $matches;
}

function extract_email_address ($string) {
    $emails = '';  
    foreach(preg_split('/\s/', $string) as $token) {
        $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if ($email !== false) {
            $emails[] = $email;
        }
    }
    return $emails;
}

function extractPhoneFromText ($string) {
    //$string = "bla bla pickachu@domain.com MIME-Version: balbasur@domain.com bla bla bla";
    $matches = array();
    preg_match_all('/'.PHONE_PATTERN.'/',$string,$matches);
    if(isset($matches[0])) $matches = $matches[0];
    return $matches;
} 

function setupApi() {
    return new \Facebook\Facebook([
        'app_id' => APP_FB_ID,
        'app_secret' => APP_FB_PASS
    ]);
}

function show_cates($cates, $select = 0, $parent = 0, $str = '')
{
    foreach ($cates as $cate) {
        $id = $cate->id;
        $name = $cate->cate_name;
        if ($parent == $cate->cate_parent) {
            if ($id == $select) {
                echo "<option selected value='".$id."'>$str $name</option>";
            } else {
                echo "<option value='".$id."'>$str $name</option>";
            }
            show_cates($cates, $select, $id, $str.'--');
        }
    }
}

function objectToArray(&$object)
{
    return @json_decode(json_encode($object), true);
}

/**
 * check time current has in range time
 * @param  [type] $start [time range start]
 * @param  [type] $end   [time range end]
 * @return [boolean]        [true or false]
 */
function currentInTimeRange($start, $end)
{
    return strtotime($start) <= time() && time() <= strtotime($end);
}

/**
 * conver phone 11 number to 10 number 
 * @param  string $phone [phone 11 number]
 * @return [type]        [phone 10 number]
 */
function convertPhoneNumber(string $phone)
{
    if (!empty($phone) && strlen($phone) === 11) {
        $list = [
            // viettel
            '0162' => '032',
            '0163' => '033',
            '0164' => '034',
            '0165' => '035',
            '0166' => '036',
            '0167' => '037',
            '0168' => '038',
            '0169' => '039',
            // end viettel
            // Mobifone
            '0120' => '070',
            '0121' => '079',
            '0122' => '077',
            '0126' => '076',
            '0128' => '078',
            // end mobifone
            // Vinaphone
            '0123' => '083',
            '0124' => '084',
            '0125' => '085',
            '0127' => '081',
            '0129' => '082',
            // end Vinaphone
            // Vietnamobile
            '0186' => '056',
            '0188' => '058',
            // end Vietnamobile
            // Gmobile
            '0199' => '059',
            // end Gmobile
        ];
        $patterns = [];
        $replacements = [];
        foreach ($list as $key => $item) {
            array_push($patterns, '/' . $key . '/');
            array_push($replacements, $item);
        }
        return preg_replace($patterns, $replacements, $phone);
    }
    return $phone;
}

/**
 * set cookie
 * @param [type]  $name  [name cookie]
 * @param [type]  $value [value cookie]
 * @param integer $days  [count days]
 */
function createCookie(string $name, $value, $days = 1)
{
    setcookie($name, $value, time() + (86400 * $days), "/");
}

/**
 * delete cookie
 * @param  [type] $name [name cookie]
 * @return [type]       [description]
 */
function deleteCookie(string $name)
{
    setcookie($name, "", time() - 3600);
}

/**
 * convert object array to array one column
 * @param  object $obj [description]
 * @param  string $col [property of object]
 * @return [type]      [array containt column]
 */
function groupToArray(object $obj, $col = 'id')
{
    $result = [];
    foreach ($obj as $item) {
        array_push($result, $item->{$col});
    }
    return $result;
}

function removeScript(string $str)
{
    $result = preg_replace('/script.*?\/script/ius', '', $str)
                       ? preg_replace('/script.*?\/script/ius', '', $str)
                       : $str;
    return $result;
}

function changePhone84(string $phone)
{
    $result = null;
    if (substr($phone, 0, 2) == '84') {
        $result = '0' . substr($phone, 2);
    } else
    if (substr($phone, 0, 1) == '0') {
        $result = $phone;
    }
    return $result;
}