{if $page_name == 'category'}
  {if Configuration::get('category_page_recently_viewed') == 1}
    <div class="rees46 rees46-recommend" data-limit="4" data-type="recently_viewed"></div>
  {/if}
  {if Configuration::get('category_page_interesting') == 1}
    <div class="rees46 rees46-recommend" data-limit="4" data-type="interesting"></div>
  {/if}
{/if}
