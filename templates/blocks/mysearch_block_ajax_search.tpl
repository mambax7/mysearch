<form id="ajaxsearchform" style="margin-top: 0px;" action="<{$xoops_url}>/modules/mysearch/search.php" method="get">
    <input type="text" id="autocomplete" name="query" size="14">
    <span id="indicator1" style="display: none;"><img src="<{$xoops_url}>/modules/mysearch/assets/images/ajax-loader.gif"
                                                      alt="<{$smarty.const._MB_MYSEARCH_AJAX_WORKING}>"></span>
    <div id="autocomplete_choices" class="autocomplete"></div>
    <input type="hidden" name="action" value="results"><br>
    <input type="submit" value="<{$smarty.const._MB_MYSEARCH_SEARCH}>">
</form>
<a href="<{$xoops_url}>/modules/mysearch/search.php"><{$block.lang_advsearch}></a>

<script type="text/javascript">
    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "<{$xoops_url}>/modules/mysearch/include/ajax_updater.php", {
        indicator: 'indicator1',
        minChars: 2
    });
</script>
