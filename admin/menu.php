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

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}


$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

$moduleHelper->loadLanguage('modinfo');

$i = 0;

// Index
'title' =>  _MI_MYSEARCH_ADMIN0,
'link' =>  'admin/index.php',
'icon' =>  $pathIcon32 . '/home.png',
++$i;
'title' =>  _MI_MYSEARCH_ADMMENU1,
'link' => stats',
'icon' =>  $pathIcon32 . '/stats.png',
++$i;
'title' =>  _MI_MYSEARCH_ADMMENU2,
'link' => purge',
'icon' =>  $pathIcon32 . '/prune.png',
++$i;
'title' =>  _MI_MYSEARCH_ADMMENU3,
'link' => export',
'icon' =>  $pathIcon32 . '/export.png',
++$i;
'title' =>  _MI_MYSEARCH_ADMMENU4,
'link' => blacklist',
'icon' =>  $pathIcon32 . '/manage.png',
++$i;
'title' =>  _MI_MYSEARCH_ADMMENU5,
'link' =>  'admin/about.php',
'icon' =>  $pathIcon32 . '/about.png',
