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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

//load scripts
if (!defined('MYSEARCH_INCLUDED')) {
    define('MYSEARCH_INCLUDED', '1');
    if (@is_object($xoTheme)) {
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/mysearch/css/style.css');
        $xoTheme->addScript(XOOPS_URL . '/modules/mysearch/js/scriptaculous/lib/prototype.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/mysearch/js/scriptaculous/src/scriptaculous.js');
    } else {
        $xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="'
                                                 . XOOPS_URL
                                                 . '/modules/mysearch/css/style.css"><script type="text/javascript" src="'
                                                 . XOOPS_URL
                                                 . '/modules/mysearch/js/scriptaculous/lib/prototype.js"></script><script type="text/javascript" src="'
                                                 . XOOPS_URL
                                                 . '/modules/mysearch/js/scriptaculous/src/scriptaculous.js"></script>'
                                                 . @$xoopsTpl->get_template_vars('xoops_module_header'));
    }
}

// create form
$search_form = new XoopsThemeForm(_MA_MYSEARCH_SEARCH, 'ajaxsearchform', 'search.php', 'get');

// create form elements

$search_form->addElement(new XoopsFormLabel(_MA_MYSEARCH_KEYWORDS, '
<input type="text" id="autocomplete_2" name="query" size="14" value="' . htmlspecialchars(stripslashes(implode(' ', $queries))) . '">
<span id="indicator2" style="display: none"><img src="' . XOOPS_URL . '/modules/mysearch/assets/images/ajax-loader.gif" alt="' . _MB_MYSEARCH_AJAX_WORKING . '"></span>
<div id="autocomplete_choices_2" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter("autocomplete_2", "autocomplete_choices_2", "' . XOOPS_URL . '/modules/mysearch/include/ajax_updater.php", {indicator: \'indicator2\',minChars: 2});
</script>'));
$type_select = new XoopsFormSelect(_MA_MYSEARCH_TYPE, 'andor', $andor);
$type_select->addOptionArray(['AND' => _MA_MYSEARCH_ALL, 'OR' => _MA_MYSEARCH_ANY, 'exact' => _MA_MYSEARCH_EXACT]);
$search_form->addElement($type_select);
if (!empty($mids)) {
    $mods_checkbox = new XoopsFormCheckBox(_MA_MYSEARCH_SEARCHIN, 'mids[]', $mids);
} else {
    $mods_checkbox = new XoopsFormCheckBox(_MA_MYSEARCH_SEARCHIN, 'mids[]', $mid);
}
if (empty($modules)) {
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('hassearch', 1));
    $criteria->add(new Criteria('isactive', 1));
    if (!empty($available_modules)) {
        $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
    }
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $mods_checkbox->addOptionArray($moduleHandler->getList($criteria));
} else {
    foreach ($modules as $mid => $module) {
        $module_array[$mid] = $module->getVar('name');
    }
    $mods_checkbox->addOptionArray($module_array);
}
$search_form->addElement($mods_checkbox);
if ($xoopsModuleConfig['keyword_min'] > 0) {
    $search_form->addElement(new XoopsFormLabel(_MA_MYSEARCH_SEARCHRULE, sprintf(_MA_MYSEARCH_KEYIGNORE, $xoopsModuleConfig['keyword_min'])));
}
$search_form->addElement(new XoopsFormHidden('action', 'results'));
$search_form->addElement(new XoopsFormHiddenToken('id'));
$search_form->addElement(new XoopsFormButton('', 'submit', _MA_MYSEARCH_SEARCH, 'submit'));

return $search_form->render();    // Added by Lankford on 2007/7/26.
