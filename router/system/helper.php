<?php

// {{{ function ake($key, $array)
/**
 * Shortcut for array_key_exists()
 *
 * @param string $key the key to check for
 * @param array $array array to find key in
 * @return bool
 */
function ake($key, $array)
{
    return is_array($array) && array_key_exists($key, $array);
}
// }}}
// {{{ function array_clean($array, $deep = FALSE)
/**
 * Goes into array and removes empty elements
 *
 * @param array $array an array to be cleaned
 * @param bool $deep go multiple levels deep if true, else one level
 * @return array cleaned array or original array
 */
function array_clean($array, $deep = FALSE)
{
    foreach ($array as $k => $v)
    {
        if (empty($v))
        {
            unset($array[$k]);
        }
        elseif ($deep && is_array($v))
        {
            $array[$k] = array_clean($v, TRUE);
        }
    }
    return $array;
}
// }}}
// {{{ function array_drill($array, $keys)
/**
 * Drills down the array into the keys provided
 *
 * @param array $array
 * @param string|array $key,... optional keys to drill down to, or array of keys
 * @return array|null null if key doesn't exist
 */
function array_drill($array)
{
    if (is_array($array))
    {
        $keys = array_slice(func_get_args(), 1);
        if (is_array($keys[0]))
        {
            $kc = count($keys[0]);
            for ($i = 0; $i < $kc; ++$i)
            {
                $param = $keys[0][$i];
                if (ake($param, $array))
                {
                    $array = $array[$param];
                }
                else
                {
                    return NULL;
                }
            }
        }
        else
        {
            foreach ($keys as $key)
            {
                if (is_array($array) && ake($key, $array))
                {
                    $array = $array[$key];
                }
                else
                {
                    return NULL;
                }
            }
        }
        return $array;
    }
    return NULL;
}
// }}}
// {{{ function array_join($master)
/**
 * This is similar to array_merge with the exception that
 * only the keys of the master array will be returned
 *
 * @param array $master the formatted array structure
 * @param array $array,... arrays of data to merge
 * @return array formatted and merged array based on the $master
 */
function array_join($master)
{
    return array_intersect_key(call_user_func_array('array_merge', func_get_args()), $master);
}
// }}}
// {{{ function deka($default = NULL)
/**
 * Works like eka() but with default value set as first parameter
 * Use this as a shortcut of a ternary. Example:
 *
 *      $foo = isset($bar['baz']) ? $bar['baz'] : 'default';
 *      can become:
 *      $foo = deka('default', $bar, 'baz');
 *
 * @param mixed $default what to return if the drill fails
 * @param string $key,... keys to drill down to
 * @return mixed the $default param if data isn't found
 */
function deka($default)
{
    $data = call_user_func_array('array_drill', array_slice(func_get_args(), 1));
    return is_null($data) ? $default : $data;
}
// }}}
// {{{ function eka($array)
/**
 * Works like array_key_exists() but with array name first then multiple keys
 * Letters reversed because the parameters are sort of reversed to ake()
 *
 * @param array $array array to search
 * @param string $key,... keys to drill down to
 * @return bool
 */
function eka($array)
{
    return !is_null(call_user_func_array('array_drill', func_get_args()));
}
// }}}
// {{{ function get_device_type($desktops = array(), $override = '')
/**
 * This function should return the device type of client. It's based off of Brett Jankord's
 * Categorizer {@link http://www.brettjankord.com/2012/01/16/categorizr-a-modern-device-detection-script/}
 *
 * @param array $desktops An array of views to be treated as desktops
 * @param string $override Override all following get_device_type calls with single device
 * @return string
 */
function get_device_type($desktops = array(), $override = '')
{
    $category = 'device_type';
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if ($override)
    {
        $device_types = array(
            'desktop', 'tablet', 'tv', 'mobile'
        );
        if (in_array($override, $device_types))
        {
            $_SESSION[$category] = $override;
        } 
    }
    if (!isset($_SESSION[$category]))
    {
        // Check if user agent is a smart TV - http://goo.gl/FocDk
        if ((preg_match('/GoogleTV|SmartTV|Internet.TV|NetCast|NETTV|AppleTV|boxee|Kylo|Roku|DLNADOC|CE\-HTML/i', $ua)))
        {
            $_SESSION[$category] = "tv";
        }
        // Check if user agent is a TV Based Gaming Console
        else if ((preg_match('/Xbox|PLAYSTATION.3|Wii/i', $ua)))
        {
            $_SESSION[$category] = "tv";
        }  
        // Check if user agent is a Tablet
        else if((preg_match('/iP(a|ro)d/i', $ua)) || (preg_match('/tablet/i', $ua)) && (!preg_match('/RX-34/i', $ua)) || (preg_match('/FOLIO/i', $ua)))
        {
            $_SESSION[$category] = "tablet";
        }
        // Check if user agent is an Android Tablet
        else if ((preg_match('/Linux/i', $ua)) && (preg_match('/Android/i', $ua)) && (!preg_match('/Fennec|mobi|HTC.Magic|HTCX06HT|Nexus.One|SC-02B|fone.945/i', $ua)))
        {
            $_SESSION[$category] = "tablet";
        }
        // Check if user agent is a Kindle or Kindle Fire
        else if ((preg_match('/Kindle/i', $ua)) || (preg_match('/Mac.OS/i', $ua)) && (preg_match('/Silk/i', $ua)))
        {
            $_SESSION[$category] = "tablet";
        }
        // Check if user agent is a pre Android 3.0 Tablet
        else if ((preg_match('/GT-P10|SC-01C|SHW-M180S|SGH-T849|SCH-I800|SHW-M180L|SPH-P100|SGH-I987|zt180|HTC(.Flyer|\_Flyer)|Sprint.ATP51|ViewPad7|pandigital(sprnova|nova)|Ideos.S7|Dell.Streak.7|Advent.Vega|A101IT|A70BHT|MID7015|Next2|nook/i', $ua)) || (preg_match('/MB511/i', $ua)) && (preg_match('/RUTEM/i', $ua)))
        {
            $_SESSION[$category] = "tablet";
        } 
        // Check if user agent is unique Mobile User Agent	
        else if ((preg_match('/BOLT|Fennec|Iris|Maemo|Minimo|Mobi|mowser|NetFront|Novarra|Prism|RX-34|Skyfire|Tear|XV6875|XV6975|Google.Wireless.Transcoder/i', $ua)))
        {
            $_SESSION[$category] = "mobile";
        }
        // Check if user agent is an odd Opera User Agent - http://goo.gl/nK90K
        else if ((preg_match('/Opera/i', $ua)) && (preg_match('/Windows.NT.5/i', $ua)) && (preg_match('/HTC|Xda|Mini|Vario|SAMSUNG\-GT\-i8000|SAMSUNG\-SGH\-i9/i', $ua)))
        {
            $_SESSION[$category] = "mobile";
        }
        // Check if user agent is Windows Desktop
        else if ((preg_match('/Windows.(NT|XP|ME|9)/', $ua)) && (!preg_match('/Phone/i', $ua)) || (preg_match('/Win(9|.9|NT)/i', $ua)))
        {
            $_SESSION[$category] = "desktop";
        }  
        // Check if agent is Mac Desktop
        else if ((preg_match('/Macintosh|PowerPC/i', $ua)) && (!preg_match('/Silk/i', $ua)))
        {
            $_SESSION[$category] = "desktop";
        } 
        // Check if user agent is a Linux Desktop
        else if ((preg_match('/Linux/i', $ua)) && (preg_match('/X11/i', $ua)))
        {
            $_SESSION[$category] = "desktop";
        } 
        // Check if user agent is a Solaris, SunOS, BSD Desktop
        else if ((preg_match('/Solaris|SunOS|BSD/i', $ua)))
        {
            $_SESSION[$category] = "desktop";
        }
        // Check if user agent is a Desktop BOT/Crawler/Spider
        else if ((preg_match('/Bot|Crawler|Spider|Yahoo|ia_archiver|Covario-IDS|findlinks|DataparkSearch|larbin|Mediapartners-Google|NG-Search|Snappy|Teoma|Jeeves|TinEye/i', $ua)) && (!preg_match('/Mobile/i', $ua)))
        {
            $_SESSION[$category] = "desktop";
        }  
        // Otherwise assume it is a Mobile Device
        else {
            $_SESSION[$category] = "mobile";
        }
        
    }// End if session not set

    if (in_array($_SESSION[$category], $desktops))
    {
        $_SESSION[$category] = 'desktop';
    }

    return $_SESSION[$category];
}
// }}}
// {{{ function hsc($string, $ent = ENT_QUOTES, $enc = 'UTF-8')
/**
 * shortcut for htmlspecialchars()
 */
function hsc($string, $ent = ENT_QUOTES, $enc = 'UTF-8')
{
    return htmlspecialchars($string, $ent, $enc);
}
// }}}
// {{{ function is_email($email)
/**
 * follows ~99.99% of RFC 2822 according to http://www.regular-expressions.info/email.html
 *
 * @param string $email email address
 * @return bool
 */
function is_email($email)
{
    $regex = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?";
    $result = preg_match($regex, $email);
    return (bool)$result;
}
// }}}
// {{{ function prepend_name($key, $name)
/**
 * Prepends $name with $key while keeping it in array notation
 *
 * @param string $key string to prepend with
 * @param string $name string to change
 * @param bool $multiple append [] for html array fields
 */
function prepend_name($key, $name)
{
    if (strpos($name, '[') === FALSE)
    {
        return $key.'['.$name.']';
    }
    else
    {
        $pos = strpos($name, '[');
        $first = substr($name, 0, $pos);
        $second = substr($name, $pos);
        return $key.'['.$first.']'.$second;
    }
}
// }}}
// {{{ function random_string($length = 10, $base = 62)
/**
 * Create a random using only numbers and letters
 *
 * @param int $length length of the random string
 * @param int $base the base number for math random number
 * @return string
 */
function random_string($length = 10, $base = 62)
{
    $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $o = '';
    $max = $base - 1;
    for ($i = 0; $i < $length; ++$i)
    {
        $o .= substr($c, mt_rand(0, $max), 1);
    }
    return $o;
}
// }}}
// {{{ function router_autoload($class)
function router_autoload($class)
{
    $file = DIR_SYS.'/classes/' . $class . '.php';
    if (!class_exists($class) && is_file($file))
    {
        include_once $file;
    }
}
// }}}
// {{{ function slugify($name, $replacement = '-')
function slugify($name, $replacement = '-')
{
    // Characters to process. All other characters will be dropped
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKKLMNOPQRSTUVWXYZ-_ ';
    // Characters to replace with hyphens
    $hyphened = '-_ ';
    $o = '';
    $last_char = '';
    $length = strlen($name);
    for ($i = 0; $i < $length; ++$i)
    {
        $char = substr($name, $i, 1);
        if (strpos($chars, $char) !== FALSE)
        {
            if (strpos($hyphened, $char) === FALSE)
            {
                $o .= $char;
                $last_char = $char;
            }
            else
            {
                if ($last_char !== $replacement)
                {
                    $o .= $replacement;
                    $last_char = $replacement;
                }
            }
        }
    }
    return strtolower($o);
}
// }}}
// {{{ function time_zones()
/**
 * Returns an array of country named time zones
 */
function time_zones()
{
    $zones = DateTimeZone::listIdentifiers();
    $continents = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
    $locations = array();
    foreach ($zones as $zone)
    {
        $zone = explode('/', $zone);
        if (in_array($zone[0], $continents) && isset($zone[1]) && $zone[1] !== '')
        {
            $locations[$zone[0]][$zone[0].'/'.$zone[1]] = str_replace('_', ' ', $zone[1]);
        }
    }
    foreach ($locations as &$zones)
    {
        asort($zones);
    }
    return $locations;
}
// }}}
// {{{ function word_split($str, $words = 15, $random = FALSE)
/**
 *
 * @param string $str The text string to split
 * @param int $words The number of words to extract. Defaults to 15
 */
function word_split($str, $words = 15, $random = FALSE)
{
    if ($random)
    {
	    $arr = preg_split("/[\s]+/", $str);
        $max_start = count($arr) - $words;
        $start = mt_rand(0, $max_start);
    }
    else
    {
	    $arr = preg_split("/[\s]+/", $str, $words + 1);
        $start = 0;
    }
	$arr = array_slice($arr, $start, $words);
	return implode(' ',$arr);
}
// }}}
