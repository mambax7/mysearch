<?php
//  ------------------------------------------------------------------------ //
//                       mysearch - MODULE FOR XOOPS 2                        //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://xoops.instant-zero.com>                      //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

require_once __DIR__ . '/../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/modules/mysearch/admin/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';
require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();
global $pathIcons16;

if (file_exists(XOOPS_ROOT_PATH . '/modules/mysearch/language/' . $xoopsConfig['language'] . '/main.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/mysearch/language/' . $xoopsConfig['language'] . '/main.php';
} else {
    require_once XOOPS_ROOT_PATH . '/modules/mysearch/language/english/main.php';
}

/**
 * Module's parameters
 */
$keywords_count = mysearch_getmoduleoption('admincount');

// **********************************************************************************************************************************************
// **** Main
// **********************************************************************************************************************************************
$op = 'default';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
} elseif (isset($_GET['op'])) {
    $op = $_GET['op'];
}
$mysearchHandler = xoops_getModuleHandler('searches', 'mysearch');
$myts            = MyTextSanitizer::getInstance();

switch ($op) {
    /**
     * Remove datas by keyword or by date
     */
    case 'purge':
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        xoops_cp_header();
        $adminObject->displayNavigation('main.php?op=purge');
        //mysearch_adminmenu(1);
        echo '<br>';
        $sform = new XoopsThemeForm(_AM_MYSEARCH_PRUNE, 'pruneform', XOOPS_URL . '/modules/mysearch/admin/index.php', 'post', true);
        $sform->addElement(new XoopsFormTextDateSelect(_AM_MYSEARCH_PRUNE_DATE, 'prune_date', 15, time()), false);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_PRUNE_KEYONLY, 'keyword', 50, 255, ''), false);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_IP, 'ip', 20, 255, ''), false);
        $sform->addElement(new XoopsFormHidden('op', 'ConfirmBeforeToPrune'), false);
        $button_tray = new XoopsFormElementTray(_AM_MYSEARCH_PRUNE_DESC, '');
        $submit_btn  = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform->display();
        break;

    /**
     * Ask a confirmation before to remove keywords
     */
    case 'ConfirmBeforeToPrune':
        xoops_cp_header();
        //mysearch_adminmenu(1);
        echo '<br>';
        $criteria = new CriteriaCompo();

        $date      = '';
        $timestamp = 0;
        $keyword   = '';
        $ip        = '';

        if (isset($_POST['prune_date']) && xoops_trim($_POST['prune_date']) != '') {
            $date      = $_POST['prune_date'];
            $timestamp = mktime(0, 0, 0, (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
            $date      = date('Y-m-d', $timestamp);
            $criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $date, '<='));
        }
        if (isset($_POST['keyword']) && xoops_trim($_POST['keyword']) != '') {
            $keyword = $_POST['keyword'];
            $criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']), '='));
        }
        if (isset($_POST['ip']) && xoops_trim($_POST['ip']) != '') {
            $ip = isset($_POST['ip']) ? $_POST['ip'] : '';
            $criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']), '='));
        }
        $count = 0;
        $count = $mysearchHandler->getCount($criteria);
        if ($count > 0) {
            $msg = sprintf(_AM_MYSEARCH_PRUNE_CONFIRM, $count);
            xoops_confirm(array(
                              'op'         => 'pruneKeywords',
                              'keyword'    => $keyword,
                              'prune_date' => $timestamp,
                              'ip'         => $ip,
                              'ok'         => 1
                          ), 'index.php', $msg);
        } else {
            printf(_AM_MYSEARCH_NOTHING_PRUNE);
        }
        break;

    /**
     * Effectively delete keywords
     */
    case 'pruneKeywords':
        $timestamp = 0;
        $keyword   = '';
        $ip        = '';
        $criteria  = new CriteriaCompo();

        if (isset($_POST['prune_date']) && (int)$_POST['prune_date'] != 0) {
            $timestamp = $_POST['prune_date'];
            $date      = date('Y-m-d', $timestamp);
            $criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $date, '<='));
        }
        if (isset($_POST['keyword']) && xoops_trim($_POST['keyword']) != '') {
            $keyword = $_POST['keyword'];
            $criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']), '='));
        }
        if (isset($_POST['ip']) && xoops_trim($_POST['ip']) != '') {
            $ip = isset($_POST['ip']) ? $_POST['ip'] : '';
            $criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']), '='));
        }

        if ((int)$_POST['ok'] == 1) {
            xoops_cp_header();
            $mysearchHandler->deleteAll($criteria);
            redirect_header('index.php?op=purge', 2, _AM_MYSEARCH_DBUPDATED);
        }
        break;

    /**
     * Remove a keyword from the database (directly called from the statistics part)
     */
    case 'removekeyword':
        xoops_cp_header();
        if ((int)$_GET['id'] != 0) {
            $tmp_search = $mysearchHandler->get((int)$_GET['id']);
            if (is_object($tmp_search)) {
                $critere = new Criteria('keyword', $tmp_search->getVar('keyword'), '=');
                $mysearchHandler->deleteAll($critere);
            }
            unset($tmp_search);
        }
        redirect_header('index.php', 2, _AM_MYSEARCH_DBUPDATED);
        break;

    /**
     * Export datas to a pure text file
     */
    case 'export':
        xoops_cp_header();
        $adminObject->displayNavigation('main.php?op=export');
        //mysearch_adminmenu(2);
        echo '<br>';
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $min  = $max = '';
        $mint = $maxt = 0;
        $mysearchHandler->getMinMaxDate($min, $max);
        $mint = strtotime($min);
        $maxt = strtotime($max);

        $sform      = new XoopsThemeForm(_AM_MYSEARCH_EXPORT, 'exportform', XOOPS_URL . '/modules/mysearch/admin/index.php', 'post', true);
        $dates_tray = new XoopsFormElementTray(_AM_MYSEARCH_EXPORT_BETWEEN);
        $date1      = new XoopsFormTextDateSelect('', 'date1', 15, $mint);
        $date2      = new XoopsFormTextDateSelect(_AM_MYSEARCH_EXPORT_AND, 'date2', 15, $maxt);
        $dates_tray->addElement($date1);
        $dates_tray->addElement($date2);
        $sform->addElement($dates_tray, false);
        $sform->addElement(new XoopsFormSelectUser(_AM_MYSEARCH_USER, 'user', true, '', 5, true), false);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_KEYWORD, 'keyword', 50, 255, ''), false);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_IP, 'ip', 10, 32, ''), false);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_DATE_FORMAT, 'dateformat', 15, 255, _SHORTDATESTRING), true);
        $sform->addElement(new XoopsFormText(_AM_MYSEARCH_DELIMITER, 'delimiter', 2, 255, ';'), true);
        $sform->addElement(new XoopsFormHidden('op', 'SearchExport'), false);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform->display();
        break;

    /**
     * Lauch the export
     */
    case 'SearchExport':
        xoops_cp_header();
        //mysearch_adminmenu(2);
        $criteria    = new CriteriaCompo();
        $dateformat  = isset($_POST['dateformat']) ? $_POST['dateformat'] : '';
        $delimiter   = isset($_POST['delimiter']) ? $_POST['delimiter'] : ';';
        $searchfile  = XOOPS_ROOT_PATH . '/uploads/mysearch_keywords.txt';
        $searchfile2 = XOOPS_URL . '/uploads/mysearch_keywords.txt';
        $tbl         = array();

        if (isset($_POST['date1']) && isset($_POST['date2'])) {
            $startdate = date('Y-m-d', strtotime($_POST['date1']));
            $enddate   = date('Y-m-d', strtotime($_POST['date2']));
            $criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $startdate, '>='));
            $criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $enddate, '<='));
        }
        if (isset($_POST['user']) && xoops_trim($_POST['user']) != '') {
            $criteria->add(new Criteria('uid', '(' . implode(',', $_POST['user']) . ')', 'IN'));
        }
        if (isset($_POST['keyword']) && xoops_trim($_POST['keyword']) != '') {
            $criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']), '='));
        }
        if (isset($_POST['ip']) && xoops_trim($_POST['ip']) != '') {
            $criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']), '='));
        }
        $criteria->setSort('datesearch');
        $criteria->setOrder('desc');

        $tbl = $mysearchHandler->getObjects($criteria);
        if (count($tbl) > 0) {
            $fp = fopen($searchfile, 'w');
            if (!$fp) {
                redirect_header('index.php', 4, sprintf(_AM_MYSEARCH_EXPORT_ERROR, $searchfile));
            }
            $tmpmysearch = new searches();
            fwrite($fp, 'id' . $delimiter . 'date' . $delimiter . 'keyword' . $delimiter . 'uid' . $delimiter . 'uname' . $delimiter . 'ip' . "\r\n");
            foreach ($tbl as $onesearch) {
                fwrite($fp, $onesearch->getVar('mysearchid')
                            . $delimiter
                            . formatTimestamp(strtotime($onesearch->getVar('datesearch')))
                            . $delimiter
                            . $onesearch->getVar('keyword')
                            . $delimiter
                            . $onesearch->getVar('uid')
                            . $delimiter
                            . $tmpmysearch->uname($onesearch->getVar('uid'))
                            . $delimiter
                            . $onesearch->getVar('ip')
                            . "\r\n");
            }
            fclose($fp);
            printf(_AM_MYSEARCH_EXPORT_READY, $searchfile2, XOOPS_URL . '/modules/mysearch/admin/index.php?op=deletefile');
        } else {
            echo _AM_MYSEARCH_NOTHING_TO_EXPORT;
        }
        break;

    /**
     * Delete the exported file
     */
    case 'deletefile':
        xoops_cp_header();
        $statfile = XOOPS_ROOT_PATH . '/uploads/mysearch_keywords.txt';
        if (unlink($statfile)) {
            redirect_header('index.php', 2, _AM_MYSEARCH_DELETED_OK);
        } else {
            redirect_header('index.php', 2, _AM_MYSEARCH_DELETED_PB);
        }
        break;

    /**
     * Blacklist manager
     */
    case 'blacklist':
        xoops_cp_header();
        $adminObject->displayNavigation('main.php?op=blacklist');

        //mysearch_adminmenu(3);
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        require_once XOOPS_ROOT_PATH . '/modules/mysearch/class/blacklist.php';
        echo '<h3>' . _AM_MYSEARCH_BLACKLIST . '</h3>';
        $sform = new XoopsThemeForm(_AM_MYSEARCH_BLACKLIST, 'MetagenBlackList', XOOPS_URL . '/modules/mysearch/admin/index.php', 'post', true);
        $sform->addElement(new XoopsFormHidden('op', 'MetagenBlackList'), false);

        // Remove words
        $remove_tray = new XoopsFormElementTray(_AM_MYSEARCH_BLACKLIST);
        $remove_tray->setDescription(_AM_MYSEARCH_BLACKLIST_DESC);
        $blacklist = new XoopsFormSelect('', 'blacklist', '', 5, true);
        $words     = array();
        $metablack = new mysearch_blacklist();
        $words     = $metablack->getAllKeywords();
        if (is_array($words) && count($words) > 0) {
            foreach ($words as $key => $value) {
                $blacklist->addOption($key, $value);
            }
        }
        $blacklist->setDescription(_AM_MYSEARCH_BLACKLIST_DESC);
        $remove_tray->addElement($blacklist, false);
        $remove_btn = new XoopsFormButton('', 'go', _AM_MYSEARCH_DELETE, 'submit');
        $remove_tray->addElement($remove_btn, false);
        $sform->addElement($remove_tray);

        // Add some words
        $add_tray = new XoopsFormElementTray(_AM_MYSEARCH_BLACKLIST_ADD);
        $add_tray->setDescription(_AM_MYSEARCH_BLACKLIST_ADD_DSC);
        $add_field = new XoopsFormTextArea('', 'keywords', '', 5, 70);
        $add_tray->addElement($add_field, false);
        $add_btn = new XoopsFormButton('', 'go', _AM_MYSEARCH_BLACKLIST_ADD, 'submit');
        $add_tray->addElement($add_btn, false);
        $sform->addElement($add_tray);
        $sform->display();
        break;

    /**
     * Add a word in the blacklist
     */
    case 'addblacklist':
        require_once XOOPS_ROOT_PATH . '/modules/mysearch/class/blacklist.php';
        if ((int)$_GET['id'] != 0) {
            $tmp_search = $mysearchHandler->get((int)$_GET['id']);
            if (is_object($tmp_search)) {
                $keyword   = $tmp_search->getVar('keyword');
                $blacklist = new mysearch_blacklist();
                $keywords  = $blacklist->getAllKeywords();
                $blacklist->addkeywords($keyword);
                $blacklist->store();
            }
        }
        redirect_header('index.php?op=stats', 2, _AM_MYSEARCH_DBUPDATED);
        break;

    /**
     * Actions on the blacklist (add or remove keyword(s))
     */
    case 'MetagenBlackList':
        require_once XOOPS_ROOT_PATH . '/modules/mysearch/class/blacklist.php';
        $blacklist = new mysearch_blacklist();
        $keywords  = $blacklist->getAllKeywords();

        if (isset($_POST['go']) && $_POST['go'] == _AM_MYSEARCH_DELETE) {
            foreach ($_POST['blacklist'] as $black_id) {
                $blacklist->delete($black_id);
            }
            $blacklist->store();
        } else {
            if (isset($_POST['go']) && $_POST['go'] == _AM_MYSEARCH_BLACKLIST_ADD) {
                $p_keywords = $_POST['keywords'];
                $keywords   = explode("\n", $p_keywords);
                foreach ($keywords as $keyword) {
                    if (xoops_trim($keyword) != '') {
                        $blacklist->addkeywords(xoops_trim($keyword));
                    }
                }
                $blacklist->store();
            }
        }
        redirect_header('index.php?op=blacklist', 2, _AM_MYSEARCH_DBUPDATED);
        break;

    /**
     * Remove content based on the IP
     */
    case 'removeip':
        xoops_cp_header();
        if ((int)$_GET['id'] != 0) {
            $tmp_search = $mysearchHandler->get((int)$_GET['id']);
            if (is_object($tmp_search)) {
                $critere = new Criteria('ip', $tmp_search->getVar('ip'), '=');
                $mysearchHandler->deleteAll($critere);
            }
            unset($tmp_search);
        }
        redirect_header('index.php', 2, _AM_MYSEARCH_DBUPDATED);
        break;

    /**
     * Default action, show statistics about keywords, users and many other things
     */
    case 'stats':
    default:
        xoops_cp_header();
        $adminObject->displayNavigation('main.php?op=stats');
        //mysearch_adminmenu(0);

        // Last x words (according to the module's option 'admincount') ***************************************************************************************
        $start          = 0;
        $more_parameter = 'op=stats';
        if (isset($_GET['start1'])) {
            $start = (int)$_GET['start1'];
        } elseif (isset($_SESSION['start1'])) {
            $start = (int)$_SESSION['start1'];
        }
        $_SESSION['start1'] = $start;
        $s_keyword          = $s_uid = $s_ip = '';
        if (isset($_POST['s_keyword'])) {
            $s_keyword = $_POST['s_keyword'];
        } elseif (isset($_GET['s_keyword'])) {
            $s_keyword = $_GET['s_keyword'];
        }

        if (isset($_POST['s_uid'])) {
            $s_uid = $_POST['s_uid'];
        } elseif (isset($_GET['s_uid'])) {
            $s_uid = $_GET['s_uid'];
        }

        if (isset($_POST['s_ip'])) {
            $s_ip = $_POST['s_ip'];
        } elseif (isset($_GET['s_ip'])) {
            $s_ip = $_GET['s_ip'];
        }

        $critere = new CriteriaCompo();
        if ($s_keyword != '') {
            $critere->add(new Criteria('keyword', $s_keyword, 'LIKE'));
            $more_parameter .= '&s_keyword=' . $s_keyword;
        }

        if ($s_uid != '') {
            if (!is_numeric($s_uid)) {
                $memberHandler = xoops_getHandler('member');
                $crituser      = new Criteria('uname', $s_uid, 'LIKE');
                $tbl_users     = array();
                $tbl_users     = $memberHandler->getUsers($crituser);
                if (count($tbl_users) > 0) {
                    $tbl_users2 = array();
                    foreach ($tbl_users as $one_user) {
                        $tbl_users2[] = $one_user->getvar('uid');
                    }
                }
                $users_list = '(' . implode(',', $tbl_users2) . ')';
                $critere->add(new Criteria('uid', $users_list, 'IN'));
            } else {
                $s_uid = (int)$s_uid;
                $critere->add(new Criteria('uid', $s_uid, '='));
            }
            $more_parameter .= '&s_uid=' . $s_uid;
        }

        if ($s_ip != '') {
            $critere->add(new Criteria('ip', $s_ip, 'LIKE'));
            $more_parameter .= '&s_ip=' . $s_ip;
        }
        $critere->setSort('datesearch');
        $critere->setLimit($keywords_count);
        $critere->setStart($start);
        $critere->setOrder('DESC');

        // Total count of keywords
        $totalcount = $mysearchHandler->getCount($critere);
        echo '<h3>' . sprintf(_AM_MYSEARCH_STATS, $totalcount) . '</h3>';

        $pagenav  = new XoopsPageNav($totalcount, $keywords_count, $start, 'start1', $more_parameter);
        $elements = $mysearchHandler->getObjects($critere);
        mysearch_collapsableBar('keywordscount', 'keywordscounticon');
        echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='keywordscounticon' name='keywordscounticon' src=" . XOOPS_URL . "/modules/mysearch/assets/images/close12.gif alt=''></a>&nbsp;" . _AM_MYSEARCH_KEYWORDS . '</h4>';
        echo "<div id='keywordscount'>";
        echo '<br>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>"
             . _AM_MYSEARCH_ID
             . "</th><th align='center'>"
             . _AM_MYSEARCH_KEYWORD
             . "</th><th align='center'>"
             . _AM_MYSEARCH_DATE
             . "</th><th align='center'>"
             . _AM_MYSEARCH_USER
             . "</th><th align='center'>"
             . _AM_MYSEARCH_IP
             . "</th><th align='center'>"
             . _AM_MYSEARCH_ACTION
             . '</th></tr>';
        $class = '';
        foreach ($elements as $oneelement) {
            $class            = ($class == 'even') ? 'odd' : 'even';
            $link1            = "<a href='" . XOOPS_URL . '/search.php?query=' . $oneelement->getVar('keyword') . "&action=results' target='_blank'>" . $oneelement->getVar('keyword') . '</a>';
            $link2            = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $oneelement->getVar('uid') . "'>" . $oneelement->uname() . '</a>';
            $action_black     = '<a '
                                . mysearch_JavascriptLinkConfirm(_AM_MYSEARCH_AREYOUSURE)
                                . " href='index.php?op=addblacklist&id="
                                . $oneelement->getVar('mysearchid')
                                . "' title='"
                                . _AM_MYSEARCH_BLACKLIST
                                . "'><img src='../assets/images/list.png' border='0' alt='"
                                . _AM_MYSEARCH_BLACKLIST
                                . "'></a>";
            $action_remove_ip = '<a ' . mysearch_JavascriptLinkConfirm(_AM_MYSEARCH_AREYOUSURE) . " href='index.php?op=removeip&id=" . $oneelement->getVar('mysearchid') . "' title='" . _AM_MYSEARCH_IP . "'><img src='../assets/images/ip.png' border='0' alt='" . _AM_MYSEARCH_IP . "'></a>";
            $action_del       = '<a '
                                . mysearch_JavascriptLinkConfirm(_AM_MYSEARCH_AREYOUSURE)
                                . " href='index.php?op=removekeyword&id="
                                . $oneelement->getVar('mysearchid')
                                . "' title='"
                                . _AM_MYSEARCH_DELETE
                                . "'><img src='"
                                . $pathIcon16
                                . "/delete.png' border='0' alt='"
                                . _AM_MYSEARCH_DELETE
                                . "'></a>";
            echo "<tr class='"
                 . $class
                 . "'><td align='center'>"
                 . $oneelement->getVar('mysearchid')
                 . "</td><td align='center'>"
                 . $link1
                 . "</td><td align='center'>"
                 . formatTimestamp(strtotime($oneelement->getVar('datesearch')))
                 . "</td><td align='center'>"
                 . $link2
                 . "</td><td align='center'>"
                 . $oneelement->getVar('ip')
                 . "</td><td align='center'>"
                 . $action_remove_ip
                 . '&nbsp;'
                 . $action_black
                 . '&nbsp;'
                 . $action_del
                 . '</td></tr>';
        }
        echo "<tr><form method='post' action='index.php'><th align='center'>"
             . _AM_MYSEARCH_FILTER_BY
             . "</th><th align='center'><input type='text' name='s_keyword' value='"
             . $s_keyword
             . "' size='10'></th><th align='center'></th><th align='center'><input type='text' name='s_uid' value='"
             . $s_uid
             . "' size='10'></th><th align='center'><input type='text' name='s_ip' value='"
             . $s_ip
             . "' size='10'></th><th align='center'><input type='submit' name='btngo_filter' value='"
             . _GO
             . "'></th></form></tr>";
        echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';

        // Most searched words ********************************************************************************************************************************
        $start = 0;
        if (isset($_GET['start2'])) {
            $start = (int)$_GET['start2'];
        } elseif (isset($_SESSION['start2'])) {
            $start = (int)$_SESSION['start2'];
        }
        $_SESSION['start2'] = $start;

        $pagenav  = new XoopsPageNav($mysearchHandler->getMostSearchedCount(), $keywords_count, $start, 'start2', 'op=stats');
        $elements = $mysearchHandler->getMostSearched($start, $keywords_count);
        mysearch_collapsableBar('mostsearch', 'mostsearchicon');
        echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='mostsearchicon' name='mostsearchicon' src=" . XOOPS_URL . "/modules/mysearch/assets/images/close12.gif alt=''></a>&nbsp;" . _AM_MYSEARCH_MOST_SEARCH . '</h4>';
        echo "<div id='mostsearch'>";
        echo '<br>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYSEARCH_HITS . "</th><th align='center'>" . _AM_MYSEARCH_KEYWORD . "</th><th align='center'>" . _AM_MYSEARCH_ACTION . '</th></tr>';
        $class = '';
        foreach ($elements as $onekeyword_id => $onekeyword_datas) {
            $onekeyword      = $onekeyword_datas['keyword'];
            $onekeywordcount = $onekeyword_datas['count'];
            $class           = ($class == 'even') ? 'odd' : 'even';
            $link1           = "<a href='" . XOOPS_URL . '/search.php?query=' . $onekeyword . "&action=results' target='_blank'>" . $onekeyword . '</a>';
            $action_del      = '<a ' . mysearch_JavascriptLinkConfirm(_AM_MYSEARCH_AREYOUSURE) . " href='index.php?op=removekeyword&id=" . $onekeyword_id . "' title='" . _AM_MYSEARCH_DELETE . "'><img src='" . $pathIcon16 . "/delete.png' border='0' alt='" . _AM_MYSEARCH_DELETE . "'></a>";
            $action_black    = '<a ' . mysearch_JavascriptLinkConfirm(_AM_MYSEARCH_AREYOUSURE) . " href='index.php?op=addblacklist&id=" . $onekeyword_id . "' title='" . _AM_MYSEARCH_BLACKLIST . "'><img src='../assets/images/list.png' border='0' alt='" . _AM_MYSEARCH_BLACKLIST . "'></a>";
            echo "<tr class='" . $class . "'><td align='center'>" . $onekeywordcount . "</td><td align='center'>" . $link1 . "</td><td align='center'>" . $action_black . '&nbsp;' . $action_del . '</td></tr>';
        }
        echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';

        // Biggest users of the search ************************************************************************************************************************
        $tmpmysearch = new searches();
        $start       = 0;
        if (isset($_GET['start3'])) {
            $start = (int)$_GET['start3'];
        } elseif (isset($_SESSION['start3'])) {
            $start = (int)$_SESSION['start3'];
        }
        $_SESSION['start3'] = $start;

        $pagenav  = new XoopsPageNav($mysearchHandler->getBiggestContributorsCount(), $keywords_count, $start, 'start3', 'op=stats');
        $elements = $mysearchHandler->getBiggestContributors($start, $keywords_count);
        mysearch_collapsableBar('bigcontribut', 'bigcontributicon');
        echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='bigcontributicon' name='bigcontributicon' src=" . XOOPS_URL . "/modules/mysearch/assets/images/close12.gif alt=''></a>&nbsp;" . _AM_MYSEARCH_BIGGEST_USERS . '</h4>';
        echo "<div id='bigcontribut'>";
        echo '<br>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYSEARCH_USER . "</th><th align='center'>" . _AM_MYSEARCH_HITS . '</th></tr>';
        $class = '';
        foreach ($elements as $oneuser => $onecount) {
            $class = ($class == 'even') ? 'odd' : 'even';
            $link1 = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $oneuser . "'>" . $tmpmysearch->uname($oneuser) . '</a>';
            echo "<tr class='" . $class . "'><td align='center'>" . $link1 . "</td><td align='center'>" . $onecount . '</td></tr>';
        }
        echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';

        // daily stats ****************************************************************************************************************************************
        $start = 0;
        if (isset($_GET['start4'])) {
            $start = (int)$_GET['start4'];
        } elseif (isset($_SESSION['start4'])) {
            $start = (int)$_SESSION['start4'];
        }
        $_SESSION['start4'] = $start;
        $pagenav            = new XoopsPageNav($mysearchHandler->getUniqueDaysCount(), $keywords_count, $start, 'start4', 'op=stats');
        $elements           = $mysearchHandler->GetCountPerDay($start, $keywords_count);
        mysearch_collapsableBar('daystat', 'daystaticon');
        echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='daystaticon' name='daystaticon' src=" . XOOPS_URL . "/modules/mysearch/assets/images/close12.gif alt=''></a>&nbsp;" . _AM_MYSEARCH_DAY_STATS . '</h4>';
        echo "<div id='daystat'>";
        echo '<br>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYSEARCH_DATE . "</th><th align='center'>" . _AM_MYSEARCH_USE . '</th></tr>';
        $class = '';
        foreach ($elements as $onedate => $onecount) {
            $class          = ($class == 'even') ? 'odd' : 'even';
            $datefordisplay = formatTimestamp(strtotime($onedate), 's');
            echo "<tr class='" . $class . "'><td align='center'>" . $datefordisplay . "</td><td align='center'>" . $onecount . '</td></tr>';
        }
        echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';

        // IP stats *******************************************************************************************************************************************
        $start = 0;
        if (isset($_GET['start4'])) {
            $start = (int)$_GET['start4'];
        } elseif (isset($_SESSION['start4'])) {
            $start = (int)$_SESSION['start4'];
        }
        $_SESSION['start4'] = $start;

        $pagenav  = new XoopsPageNav($mysearchHandler->getIPsCount(), $keywords_count, $start, 'start4', 'op=stats');
        $elements = $mysearchHandler->getIPs($start, $keywords_count);
        mysearch_collapsableBar('ipcount', 'ipcounticon');
        echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='ipcounticon' name='ipcounticon' src=" . XOOPS_URL . "/modules/mysearch/assets/images/close12.gif alt=''></a>&nbsp;" . _AM_MYSEARCH_IP . '</h4>';
        echo "<div id='ipcount'>";
        echo '<br>';
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo "<tr><th align='center'>" . _AM_MYSEARCH_IP . "</th><th align='center'>" . _AM_MYSEARCH_HITS . '</th></tr>';
        $class = '';
        foreach ($elements as $oneip => $onecount) {
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "<tr class='" . $class . "'><td align='center'>" . $oneip . "</td><td align='center'>" . $onecount . '</td></tr>';
        }
        echo "</table><div align='right'>" . $pagenav->renderNav() . '</div></div><br>';

        break;
}
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
