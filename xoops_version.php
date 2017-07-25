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
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
$moduleDirName = basename(__DIR__);

$modversion['version']             = 1.22;
$modversion['module_status']       = 'Beta 1';
$modversion['release_date']        = '2014/04/23';
$modversion['name']                = _MI_MYSEARCH_NAME;
$modversion['description']         = _MI_MYSEARCH_DESC;
$modversion['credits']             = 'Christian, Marco, Lankford, Smart2, Trabis';
$modversion['author']              = 'Hervet, Trabis & Others';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GNU GPL 2.0';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']            = 0; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = basename(__DIR__);
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'www.xoops.org/';
$modversion['module_website_name'] = 'XOOPS';
$modversion['author_website_url']  = 'https://xoops.org/';
$modversion['author_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = array('mysql' => '5.5');

//About
$modversion['developer_website_url']  = 'http://www.xuups.com';
$modversion['developer_website_name'] = 'Xuups';
$modversion['developer_email']        = 'lusopoemas@gmail.com';
$modversion['status_version']         = 'Final';
$modversion['status']                 = 'Final';
$modversion['date']                   = '2008-11-01';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = array(
    ['name' => _MI_MYSEARCH_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_MYSEARCH_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_MYSEARCH_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_MYSEARCH_SUPPORT, 'link' => 'page=support'],
);

$modversion['people']['developers'][] = 'Hervet(herve-thouzard.com)';
$modversion['people']['developers'][] = 'Marco(xoops.instant-zero.com)';
$modversion['people']['developers'][] = 'Lankford(lankfordfamily.com)';
$modversion['people']['developers'][] = 'Smart2(s-martinez.com)';
$modversion['people']['developers'][] = 'Trabis(xuups.com)';

$modversion['people']['testers'][] = '';

$modversion['people']['translators'][] = '';

$modversion['people']['documenters'][] = '';

$modversion['people']['other'][] = '';

$modversion['demo_site_url']     = '';
$modversion['demo_site_name']    = '';
$modversion['support_site_url']  = 'http://www.xuups.com';
$modversion['support_site_name'] = 'Xuups';
$modversion['submit_bug']        = 'http://www.xuups.com';
$modversion['submit_feature']    = 'http://www.xuups.com';

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

$modversion['tables'][0] = 'mysearch_searches';

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';
$modversion['system_menu'] = 1;

// Templates
$modversion['templates'][1]['file']        = 'mysearch_index.tpl';
$modversion['templates'][1]['description'] = '';

$modversion['templates'][2]['file']        = 'mysearch_search.tpl';
$modversion['templates'][2]['description'] = '';

// Blocks
$modversion['blocks'][1]['file']        = 'mysearch_last_search.php';
$modversion['blocks'][1]['name']        = _MI_MYSEARCH_BNAME1;
$modversion['blocks'][1]['description'] = 'Show last searches';
$modversion['blocks'][1]['show_func']   = 'b_mysearch_last_search_show';
$modversion['blocks'][1]['template']    = 'mysearch_block_last_search.tpl';

$modversion['blocks'][2]['file']        = 'mysearch_biggest_users.php';
$modversion['blocks'][2]['name']        = _MI_MYSEARCH_BNAME2;
$modversion['blocks'][2]['description'] = 'Show users with the highest # of searches';
$modversion['blocks'][2]['show_func']   = 'b_mysearch_big_user_show';
$modversion['blocks'][2]['template']    = 'mysearch_block_big_user.tpl';

$modversion['blocks'][3]['file']        = 'mysearch_stats.php';
$modversion['blocks'][3]['name']        = _MI_MYSEARCH_BNAME3;
$modversion['blocks'][3]['description'] = 'Show statistics';
$modversion['blocks'][3]['show_func']   = 'b_mysearch_stats_show';
$modversion['blocks'][3]['template']    = 'mysearch_block_stats.tpl';

$modversion['blocks'][4]['file']        = 'mysearch_ajax_search.php';
$modversion['blocks'][4]['name']        = _MI_MYSEARCH_BNAME4;
$modversion['blocks'][4]['description'] = _MI_MYSEARCH_BNAME4;
$modversion['blocks'][4]['show_func']   = 'b_mysearch_ajaxsearch_show';
$modversion['blocks'][4]['template']    = 'mysearch_block_ajax_search.tpl';

// Menu
$modversion['hasMain'] = 1;

// Comments
$modversion['hasComments'] = 0;

/**
 * Show last searches on the module's index page ?
 */
$i = 0;
++$i;
$modversion['config'][$i]['name']        = 'showindex';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_OPT0';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_OPT0_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 10;

/**
 * Groups that should not be recorded
 */
$memberHandler = xoops_getHandler('member');
++$i;
$modversion['config'][$i]['name']        = 'bannedgroups';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_OPT1';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_OPT1_DSC';
$modversion['config'][$i]['formtype']    = 'select_multi';
$modversion['config'][$i]['valuetype']   = 'array';
$modversion['config'][$i]['default']     = array();
$modversion['config'][$i]['options']     = array_flip($memberHandler->getGroupList());

/**
 * How many keywords to see at a time in the admin's part of the module ?
 */
++$i;
$modversion['config'][$i]['name']        = 'admincount';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_OPT2';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_OPT2_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 10;

++$i;
$modversion['config'][$i]['name']        = 'keyword_min';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_MIN_SEARCH';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_MIN_SEARCH_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 3;

++$i;
$modversion['config'][$i]['name']        = 'enable_deep_search';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_DO_DEEP_SEARCH';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_DO_DEEP_SEARCH_DSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;

++$i;
$modversion['config'][$i]['name']        = 'num_shallow_search';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_INIT_SRCH_RSLTS';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_INIT_SRCH_RSLTS_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 5;

++$i;
$modversion['config'][$i]['name']        = 'num_module_search';
$modversion['config'][$i]['title']       = '_MI_MYSEARCH_MDL_SRCH_RESULTS';
$modversion['config'][$i]['description'] = '_MI_MYSEARCH_MDL_SRCH_RESULTS_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 10;

// Notifications
$modversion['hasNotification'] = 0;
