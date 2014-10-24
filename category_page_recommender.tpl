{if $page_name == 'category'}
  {if Configuration::get('category_page_popular') == 1}
    <div class="rees46 rees46-recommend" data-type="popular" style="padding-top: 20px; margin-left: 20px;"></div>
  {/if}

  {if Configuration::get('category_page_recently_viewed') == 1}
    <script type="text/javascript">
      $(function(){
        var recently_viewed = $('<div>', {
          class: 'rees46 rees46-recommend',
          data: {
            limit: 4,
            type: 'recently_viewed'
          }
        });
        $('#center_column').append(recently_viewed);
      });
    </script>
  {/if}
  {if Configuration::get('category_page_interesting') == 1}
    <script type="text/javascript">
      $(function(){
        var interesting = $('<div>', {
          class: 'rees46 rees46-recommend',
          data: {
            limit: 4,
            type: 'interesting'
          }
        });
        $('#center_column').append(interesting);
      });
    </script>
  {/if}
{/if}
