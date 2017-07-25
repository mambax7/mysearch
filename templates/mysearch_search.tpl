<{if $basic_search == false }>
    <strong>Search Type:</strong>
    <{$search_type}>
    <br>
    <strong>Keywords: </strong>
    <{* This section generates a space separated list of keywords that were searched. *}>
    <{section name=cur_kw_searched loop=$searched_keywords}>
        <{$searched_keywords[cur_kw_searched]}><{if $smarty.section.cur_kw_searched.index <> $smarty.section.cur_kw_searched.total}>&nbsp;<{/if}>
    <{/section}>
    <br>
    <br>
    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td colspan="2" style="padding-left: 3px;"><{$label_search_results}>: <{$showing}></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
        <{foreach from=$module_sort_order key=sort_key item=sort_value}>
            <tr>

                <th>
                    <div style="padding-top: 5px; padding-bottom: 5px; text-align: left;"><b><{$sort_key}></b>:
                        (<{$sort_value}> hit(s) returned)
                    </div>
                    <{if $search_results[$sort_key].search_more_url != ''}>
                        <div style="padding-bottom: 5px; text-align: left;"><a
                                    href="<{$search_results[$sort_key].search_more_url}>"><img
                                        src="assets/images/search.more_results.gif"
                                        alt="<{$search_results[$sort_key].search_more_title}>"></a>
                        </div>
                    <{/if}>
                </th>
                <{if $search_results[$sort_key].page_nav != ''}>
                    <div style="padding-bottom: 5px; text-align: right;"><{$search_results[$sort_key].page_nav}></div>
                <{/if}>

            </tr>
            <tr>
                <td>
                    <table class="outer" cellpadding="4" cellspacing="1" width="100%">
                        <{section name=cur_result loop=$search_results[$sort_key].results}>
                            <tr>
                                <td class="head"><{math equation="x + y" x=$smarty.section.cur_result.index y=$start}></td>
                                <td align="left" class="<{cycle values="even,odd"}>" width="100%">
                                    <img alt="<{$search_results[$sort_key].results[cur_result].processed_image_alt_text}>"
                                         src="<{$search_results[$sort_key].results[cur_result].processed_image_url}>">
                                    <{$search_results[$sort_key].results[cur_result].processed_image_tag}>&nbsp;
                                    <b><a href="<{$search_results[$sort_key].results[cur_result].link}>"><{$search_results[$sort_key].results[cur_result].processed_title}></a>
                                    </b>
                                    <br>
                                    <small>&nbsp;&nbsp;<a
                                                href="<{$search_results[$sort_key].results[cur_result].processed_user_url}>"><{$search_results[$sort_key].results[cur_result].processed_user_name}></a> <{$search_results[$sort_key].results[cur_result].processed_time}>
                                    </small>
                                </td>
                            </tr>
                        <{/section}>
                    </table>
                    <{if $search_results[$sort_key].page_nav != ''}>
                        <div style="padding-bottom: 5px; text-align: right;"><{$search_results[$sort_key].page_nav}></div>
                    <{/if}>
                </td>
            </tr>
        <{/foreach}>

    </table>
    <br>
    <br>
    <{$label_ignored_keywords}>&nbsp;
    <strong>
        <{* This section generates a space separated list of keywords that were NOT searched. *}>
        <{section name=cur_kw_not_searched loop=$ignored_keywords}>
            <{$ignored_keywords[cur_kw_not_searched]}><{if $smarty.section.cur_kw_not_searched.index <> $smarty.section.cur_kw_not_searched.total}>&nbsp;<{/if}>
        <{/section}>
    </strong>
    <br>
    <br>
<{/if}>

<{$search_form}>
