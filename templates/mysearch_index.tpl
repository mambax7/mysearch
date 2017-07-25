<{if $visiblekeywords>0}>
    <h2><{$smarty.const._MA_MYSEARCH_KEYWORD}></h2>
    <br>
    <{if $pagenav}>
        <div style="text-align: left; margin: 10px;"><{$smarty.const._MA_MYSEARCH_PAGE}> <{$pagenav}></div><{/if}>
    <table width="100%" border="0">
        <tr>
            <th align="center"><{$smarty.const._MA_MYSEARCH_DATE}></th>
            <th align="center"><{$smarty.const._MA_MYSEARCH_KEYWORD}></th>
        </tr>
        <{foreach item=onekeyword from=$keywords}>
            <tr class="<{cycle values="even,odd"}>">
                <td align='center'><{$onekeyword.date}></td>
                <td align='center'><{$onekeyword.keyword}></td>
            </tr>
        <{/foreach}>
    </table>
    <{if $pagenav}>
        <div style="text-align: left; margin: 10px;"><{$smarty.const._MA_MYSEARCH_PAGE}> <{$pagenav}></div><{/if}>
<{/if}>
