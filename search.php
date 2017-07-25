<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright     {@link https://xoops.org/ XOOPS Project}
 * @license       {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author        XOOPS Development Team
 */

//$xoopsOption['pagetype'] = "search";

include __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';

if (file_exists(XOOPS_ROOT_PATH . '/modules/mysearch/language/' . $xoopsConfig['language'] . '/blocks.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/mysearch/language/' . $xoopsConfig['language'] . '/blocks.php';
} else {
    require_once XOOPS_ROOT_PATH . '/modules/mysearch/language/english/modinfo.php';
}

/*
 $configHandler = xoops_getHandler('config');
 $xoopsConfigSearch = $configHandler->getConfigsByCat(XOOPS_CONF_SEARCH);

 if ($xoopsConfigSearch['enable_search'] != 1) {
 header('Location: '.XOOPS_URL.'/index.php');
 exit();
 }
 */
if ($xoopsModuleConfig['enable_deep_search'] == 1) {
    $GLOBALS['xoopsOption']['template_main'] = 'mysearch_search_deep.tpl';
    $search_limiter                          = 0;    // Do not limit search results.
} else {
    $search_limiter = $xoopsModuleConfig['num_shallow_search'];    // Limit the number of search results based on user preference.
}
$GLOBALS['xoopsOption']['template_main'] = 'mysearch_search.tpl';

include XOOPS_ROOT_PATH . '/header.php';

$action = 'search';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
} elseif (!empty($_POST['action'])) {
    $action = $_POST['action'];
}
$query = '';
if (!empty($_GET['query'])) {
    $query = $_GET['query'];
} elseif (!empty($_POST['query'])) {
    $query = $_POST['query'];
}
$andor = 'AND';
if (!empty($_GET['andor'])) {
    $andor = $_GET['andor'];
} elseif (!empty($_POST['andor'])) {
    $andor = $_POST['andor'];
}
$mid = $uid = $start = 0;
if (!empty($_GET['mid'])) {
    $mid = (int)$_GET['mid'];
} elseif (!empty($_POST['mid'])) {
    $mid = (int)$_POST['mid'];
}
if (!empty($_GET['uid'])) {
    $uid = (int)$_GET['uid'];
} elseif (!empty($_POST['uid'])) {
    $uid = (int)$_POST['uid'];
}
if (!empty($_GET['start'])) {
    $start = (int)$_GET['start'];
} elseif (!empty($_POST['start'])) {
    $start = (int)$_POST['start'];
}

$xoopsTpl->assign('start', $start + 1);

$queries = array();

if ($action == 'recoosults') {
    if ($query == '') {
        redirect_header('search.php', 1, _MA_MYSEARCH_PLZENTER);
    }
} elseif ($action == 'showall') {
    if ($query == '' || empty($mid)) {
        redirect_header('search.php', 1, _MA_MYSEARCH_PLZENTER);
    }
} elseif ($action == 'showallbyuser') {
    if (empty($mid) || empty($uid)) {
        redirect_header('search.php', 1, _MA_MYSEARCH_PLZENTER);
    }
}

$groups            = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$gpermHandler      = xoops_getHandler('groupperm');
$available_modules = $gpermHandler->getItemIds('module_read', $groups);

$xoopsTpl->assign('basic_search', false);
if ($action == 'search') {
    // This area seems to handle the 'just display the advanced search page' part.
    $search_form = include __DIR__ . '/include/searchform.php';
    $xoopsTpl->assign('search_form', $search_form);
    $xoopsTpl->assign('basic_search', true);
    include XOOPS_ROOT_PATH . '/footer.php';
    exit();
}

if ($andor != 'OR' && $andor != 'exact' && $andor != 'AND') {
    $andor = 'AND';
}
$xoopsTpl->assign('search_type', $andor);

$myts = MyTextSanitizer::getInstance();

if ($action != 'showallbyuser') {
    if ($andor != 'exact') {
        $ignored_queries = array(); // holds kewords that are shorter than allowed minmum length
        $temp_queries    = preg_split('/[\s,]+/', $query);
        foreach ($temp_queries as $q) {
            $q = trim($q);
            if (strlen($q) >= $xoopsModuleConfig['keyword_min']) {
                $queries[] = $myts->addSlashes($q);
            } else {
                $ignored_queries[] = $myts->addSlashes($q);
            }
        }
        if (count($queries) == 0) {
            redirect_header('search.php', 2, sprintf(_MA_MYSEARCH_KEYTOOSHORT, $xoopsModuleConfig['keyword_min']));
        }
    } else {
        $query = trim($query);
        if (strlen($query) < $xoopsModuleConfig['keyword_min']) {
            redirect_header('search.php', 2, sprintf(_MA_MYSEARCH_KEYTOOSHORT, $xoopsModuleConfig['keyword_min']));
        }
        $queries = array($myts->addSlashes($query));
    }
}
$xoopsTpl->assign('label_search_results', _MA_MYSEARCH_SEARCHRESULTS);

// Keywords section.
$xoopsTpl->assign('label_keywords', _MA_MYSEARCH_KEYWORDS . ':');
$keywords         = array();
$ignored_keywords = array();
foreach ($queries as $q) {
    $keywords[] = htmlspecialchars(stripslashes($q));
}
if (!empty($ignored_queries)) {
    $xoopsTpl->assign('label_ignored_keywords', sprintf(_MA_MYSEARCH_IGNOREDWORDS, $xoopsModuleConfig['keyword_min']));
    foreach ($ignored_queries as $q) {
        $ignored_keywords[] = htmlspecialchars(stripslashes($q));
    }
    $xoopsTpl->assign('ignored_keywords', $ignored_keywords);
}
$xoopsTpl->assign('searched_keywords', $keywords);

$all_results        = array();
$all_results_counts = array();
switch ($action) {
    case 'results':
        $max_results_per_page = $xoopsModuleConfig['num_shallow_search'];
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $criteria      = new CriteriaCompo(new Criteria('hassearch', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
        $modules = $moduleHandler->getObjects($criteria, true);
        $mids    = isset($_REQUEST['mids']) ? $_REQUEST['mids'] : array();
        if (empty($mids) || !is_array($mids)) {
            unset($mids);
            $mids = array_keys($modules);
        }
        //log it
        mysearch_search($queries, $andor, $search_limiter, $start, $uid);
        //end log
        foreach ($mids as $mid) {
            $mid = (int)$mid;
            if (in_array($mid, $available_modules)) {
                $module  =& $modules[$mid];
                $results =& $module->search($queries, $andor, $search_limiter, 0);
                $xoopsTpl->assign('showing', sprintf(_MA_MYSEARCH_SHOWING, 1, $max_results_per_page));
                $count                                       = count($results);
                $all_results_counts[$module->getVar('name')] = $count;

                if (!is_array($results) || $count == 0) {
                    $all_results[$module->getVar('name')] = array();
                } else {
                    $num_show_this_page = (($count - $start) > $max_results_per_page) ? $max_results_per_page : $count - $start;
                    for ($i = 0; $i < $num_show_this_page; ++$i) {
                        $results[$i]['processed_image_alt_text'] = $module->getVar('name') . ': ';
                        if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
                            $results[$i]['processed_image_url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['image'];
                        } else {
                            $results[$i]['processed_image_url'] = XOOPS_URL . '/images/icons/posticon2.gif';
                        }
                        if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                            $results[$i]['link'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['link'];
                        }
                        $results[$i]['processed_title'] = $myts->htmlspecialchars($results[$i]['title']);
                        $results[$i]['uid']             = @(int)$results[$i]['uid'];
                        if (!empty($results[$i]['uid'])) {
                            $uname                              = XoopsUser::getUnameFromId($results[$i]['uid']);
                            $results[$i]['processed_user_name'] = $uname;
                            $results[$i]['processed_user_url']  = XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'];
                        }
                        $results[$i]['processed_time'] = !empty($results[$i]['time']) ? ' (' . formatTimestamp((int)$results[$i]['time']) . ')' : '';
                    }

                    if ($xoopsModuleConfig['enable_deep_search'] == 1) {
                        if ($count > $max_results_per_page) {
                            $search_url = XOOPS_URL . '/modules/mysearch/search.php?query=' . urlencode(stripslashes(implode(' ', $queries)));
                            $search_url .= "&mid=$mid&action=showall&andor=$andor";
                        } else {
                            $search_url = '';
                        }
                    } else {
                        if ($count >= $max_results_per_page) {
                            $search_url = XOOPS_URL . '/modules/mysearch/search.php?query=' . urlencode(stripslashes(implode(' ', $queries)));
                            $search_url .= "&mid=$mid&action=showall&andor=$andor";
                        } else {
                            $search_url = '';
                        }
                    }

                    $all_results[$module->getVar('name')] = array(
                        'search_more_title' => _MA_MYSEARCH_SHOWALLR,
                        'search_more_url'   => $myts->htmlspecialchars($search_url),
                        'results'           => array_slice($results, 0, $num_show_this_page)
                    );
                }
            }
            unset($results);
            unset($module);
        }
        break;
    case 'showall':
    case 'showallbyuser':
        $max_results_per_page = $xoopsModuleConfig['num_module_search'];
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->get($mid);
        //log it
        mysearch_search($queries, $andor, 0, $start, $uid);
        //end log
        $results =& $module->search($queries, $andor, 0, $start, $uid);
        //$xoopsTpl->assign("showing", sprintf(_MA_MYSEARCH_SHOWING, $start + 1, $start + 20));
        $count                                       = count($results);
        $all_results_counts[$module->getVar('name')] = $count;
        if (is_array($results) && $count > 0) {
            $num_show_this_page = (($count - $start) > $max_results_per_page) ? $max_results_per_page : $count - $start;
            for ($i = 0; $i < $num_show_this_page; ++$i) {
                $results[$i]['processed_image_alt_text'] = $module->getVar('name') . ': ';
                if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
                    $results[$i]['processed_image_url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['image'];
                } else {
                    $results[$i]['processed_image_url'] = XOOPS_URL . '/images/icons/posticon2.gif';
                }
                if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                    $results[$i]['link'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['link'];
                }
                $results[$i]['processed_title'] = $myts->htmlspecialchars($results[$i]['title']);
                $results[$i]['uid']             = @(int)$results[$i]['uid'];
                if (!empty($results[$i]['uid'])) {
                    $uname                              = XoopsUser::getUnameFromId($results[$i]['uid']);
                    $results[$i]['processed_user_name'] = $uname;
                    $results[$i]['processed_user_url']  = XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'];
                }
                $results[$i]['processed_time'] = !empty($results[$i]['time']) ? ' (' . formatTimestamp((int)$results[$i]['time']) . ')' : '';
            }

            $search_url_prev       = '';
            $search_url_next       = '';
            $search_url_base       = XOOPS_URL . '/modules/mysearch/search.php?';
            $search_url_get_params = 'query=' . urlencode(stripslashes(implode(' ', $queries)));
            $search_url_get_params .= "&mid=$mid&action=$action&andor=$andor";
            if ($action == 'showallbyuser') {
                $search_url_get_params .= "&uid=$uid";
            }
            $search_url = $search_url_base . $search_url_get_params;

            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav                              = new XoopsPageNav($count, $max_results_per_page, $start, 'start', $search_url_get_params);
            $all_results[$module->getVar('name')] = array(
                'results'  => array_slice($results, 0, $num_show_this_page),
                'page_nav' => $pagenav->renderNav()
            );
        } else {
            echo '<p>' . _MA_MYSEARCH_NOMATCH . '</p>';
        }
        break;
}

arsort($all_results_counts);
$xoopsTpl->assign('module_sort_order', $all_results_counts);
$xoopsTpl->assign('search_results', $all_results);

$search_form = include __DIR__ . '/include/searchform.php';
$xoopsTpl->assign('search_form', $search_form);

include XOOPS_ROOT_PATH . '/footer.php';
