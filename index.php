<?php

ob_start('ob_gzhandler');
// {{{ defining constants
define('DIR_WEB', dirname(__FILE__));
define('DIR_RT', dirname(__FILE__) . '/router');
define('DIR_SYS', DIR_RT . '/system');
define('DIR_CTRL', DIR_RT . '/controller');
define('DIR_LIB', DIR_RT . '/library');
define('DIR_TMPL', DIR_RT . '/template');
define('DIR_VIEW', DIR_RT . '/view');
// }}}
// {{{ disecting the URI
$ru = &$_SERVER['REQUEST_URI'];
$qmp = strpos($ru, '?');
list($path, $params) = $qmp === FALSE
    ? array($ru, NULL)
    : array(substr($ru, 0, $qmp), substr($ru, $qmp + 1));
$parts = explode('/', $path);
$i = 0;
foreach ($parts as $part)
{
    if (strlen($part) && $part !== '..' && $part !== '.')
    {
        define('URI_PART_' . $i++, $part);
    }
}
define('URI_PARAM', isset($params) ? '' : $params);
define('URI_PARTS', $i);
define('URI_PATH', $path);
define('URI_REQUEST', $_SERVER['REQUEST_URI']);
// }}}
// {{{ init
include DIR_SYS . '/helper.php';
spl_autoload_register('router_autoload');
date_default_timezone_set('America/New_York');
// }}}
// {{{ routing 
include DIR_SYS . '/config.routes.php';
if ($ctrl = MPRouter::controller()) 
{
    include $ctrl;
}
else
{
    header('HTTP/1.1 404 Not Found');
    if (is_file(DIR_CTRL . '/404.php'))
    {
        include DIR_CTRL . '/404.php';
    }
}
// }}}
