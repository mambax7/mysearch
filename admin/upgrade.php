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

require_once __DIR__ . '/../../../include/cp_header.php';
xoops_cp_header();
require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';

if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
    if (!mysearch_FieldExists('ip', $xoopsDB->prefix('mysearch_searches'))) {
        mysearch_AddField("ip varchar(32) NOT NULL default ''", $xoopsDB->prefix('mysearch_searches'));
    }
    echo '<br>ok';
} else {
    printf("<H2>%s</H2>\n", _ERRORS);
}
xoops_cp_footer();
