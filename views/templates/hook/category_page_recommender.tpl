{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Servilovskiy Sergey <sergey.servilovsky@mkechinov.ru>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

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
            type: 'interesting'
          }
        });
        $('#center_column').append(interesting);
      });
    </script>
  {/if}
{/if}
