<table width="100%" border="0">
    <{foreach item=onesearch from=$block.biggesusers}>
        <tr class="<{cycle values="even,odd"}>">
            <td align='center'><{$onesearch.uname}></td>
            <td align='center'><{$onesearch.count}></td>
        </tr>
    <{/foreach}>
</table>
