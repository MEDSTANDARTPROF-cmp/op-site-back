<div>
    {if $files}
            {foreach $files as $file}
                    <img src="[[!phpthumbon? &input=`{$file['url']}` &options=`&w=960&f=webp`]]" class="w-100"alt="{$file['description']}" title="{$file['name']}">
            {/foreach}
    {else}
    {/if}
</div>
