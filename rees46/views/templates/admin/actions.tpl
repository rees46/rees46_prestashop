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
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *}

<form class="form-horizontal clearfix">
    <div class="panel col-lg-12" id="fieldset_actions">
        <div class="panel-heading"><i class="icon-wrench"></i> {$rees46_lang_actions}</div>
        <div class="table-responsive-row clearfix">
            <table class="table module">
                <thead>
                    <tr class="nodrag nodrop">
                        <th class=" text-left">
                            <span class="title_box">{$rees46_lang_name}</span>
                        </th>
                        <th class=" text-center">
                            <span class="title_box">{$rees46_lang_status}</span>
                        </th>
                        <th class=" text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$rees46_list_values item=value name=value}
                        <tr>
                            <td class="pointer text-left">{$value.name}</td>
                            <td class="pointer text-center">
                                {if $value.status}
                                    <img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">
                                {else}
                                    <img src="../img/admin/disabled.gif" alt="disabled.gif" title="disabled.gif">
                                {/if}
                            </td>
                            <td class="pointer text-right">
                                {if !$value.status}
                                    <a id="{$value.id}" class="button btn btn-default pull-right"><i class="icon-refresh"></i> {$rees46_lang_repeat}</a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</form>
