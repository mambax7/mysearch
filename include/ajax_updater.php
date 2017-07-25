<?php
include __DIR__ . '/../../../mainfile.php';
error_reporting(0);
$xoopsLogger->activated = false;
$myts                   = MyTextSanitizer::getInstance();
$mysearchHandler        = xoops_getModuleHandler('searches', 'mysearch');
if (isset($_POST['query'])) {
    $query    = $myts->addSlashes(utf8_decode($_POST['query']));
    $elements = $mysearchHandler->ajaxMostSearched(0, 5, $query);
    if (is_array($elements) && count($elements) > 0) {
        header('Content-type: text/html; ' . _CHARSET);
        echo '<ul>';
        for ($i = 0, $iMax = count($elements); $i < $iMax; ++$i) {
            echo '<li>';
            echo strtolower($elements[$i]['keyword']);
            //echo ' ('.$elements[$i]['count'].')';
            echo '</li>';
        }
        echo '</ul>';
    }
}
