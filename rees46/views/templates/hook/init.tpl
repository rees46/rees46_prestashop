{*
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author    p0v1n0m <ay@rees46.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *}

{foreach from=$rees46_modules item=module}
{if $module.params}
<script type="text/javascript">
{if {$rees46_css}}
r46('add_css', 'recommendations');
{/if}
r46('recommend', '{$module.type}', {$module.params|json_encode nofilter}, function(results) {
  if (results.length > 0) {
    $('#rees46-recommended-{$module.id_module}').load('{$module.link nofilter}&fc=module&ajax=1&module_id={$module.id_module}&product_ids=' + results);
  }
});
</script>
<div id="rees46-recommended-{$module.id_module}" class="clearfix"></div>
{/if}
{/foreach}
