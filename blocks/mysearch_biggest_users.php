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

function b_mysearch_big_user_show()
{
    require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';
    $mysearchHandler = xoops_getModuleHandler('searches', 'mysearch');
    $block           = [];
    $visiblekeywords = mysearch_getmoduleoption('showindex');
    if ($visiblekeywords > 0) {
        $tmpmysearch    = new searches();
        $keywords_count = mysearch_getmoduleoption('admincount');

        // Total keywords count
        $block['total_keywords'] = $mysearchHandler->getCount();

        // Biggest users
        $elements = $mysearchHandler->getBiggestContributors(0, $keywords_count);
        foreach ($elements as $oneuser => $onecount) {
            $block['biggesusers'][] = [
                'uid'   => $oneuser,
                'uname' => $tmpmysearch->uname($oneuser),
                'count' => $onecount
            ];
        }
    }

    return $block;
}
