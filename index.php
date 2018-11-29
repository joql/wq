<?php
/**
 * [WeEngine System] Copyright (c) 20180322102317 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

require './framework/bootstrap.inc.php';

$host = $_SERVER['HTTP_HOST'];
if (!empty($host)) {
    $bindhost = pdo_fetch("SELECT * FROM " . tablename('site_multi') . " WHERE bindhost = :bindhost", array(':bindhost' => $host));
    if (!empty($bindhost)) {
        header("Location: " . $_W['siteroot'] . 'app/index.php?i=' . $bindhost['uniacid'] . '&t=' . $bindhost['id']);
        exit;
    }
}
if(empty($_SERVER['QUERY_STRING'])){
    header('Location: ./app/index.php?i=3&c=entry&m=ewei_shopv2&do=mobile');
} else if ($_W['os'] == 'mobile' && (!empty($_GPC['i']) || !empty($_SERVER['QUERY_STRING']))) {
    header('Location: ./app/index.php?' . $_SERVER['QUERY_STRING']);
} else {
    header('Location: ./web/index.php?' . $_SERVER['QUERY_STRING']);
}