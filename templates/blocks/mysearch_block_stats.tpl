<table width="100%" border="0">
    <{foreach item=onesearch from=$block.mostsearched}>
        <tr class="<{cycle values="even,odd"}>">
            <td align='center'><{$onesearch.keyword}></td>
            <td align='center'><{$onesearch.count}></td>
        </tr>
    <{/foreach}>
</table>
