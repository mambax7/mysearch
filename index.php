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

include __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'mysearch_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';
$mysearchHandler = xoops_getModuleHandler('searches', 'mysearch');
$visiblekeywords = 0;
$visiblekeywords = mysearch_getmoduleoption('showindex');
$xoopsTpl->assign('visiblekeywords', $visiblekeywords);

if ($visiblekeywords > 0) {
    $totalcount = $mysearchHandler->getCount();
    $start      = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $critere    = new Criteria('keyword');
    $critere->setSort('datesearch');
    $critere->setLimit($visiblekeywords);
    $critere->setStart($start);
    $critere->setOrder('DESC');
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $pagenav = new XoopsPageNav($totalcount, $visiblekeywords, $start, 'start', '');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());

    $elements = $mysearchHandler->getObjects($critere);
    foreach ($elements as $oneelement) {
        $xoopsTpl->append('keywords', [
            'keyword' => $oneelement->getVar('keyword'),
            'date'    => formatTimestamp(strtotime($oneelement->getVar('datesearch')))
        ]);
    }
}

require_once XOOPS_ROOT_PATH . '/footer.php';
