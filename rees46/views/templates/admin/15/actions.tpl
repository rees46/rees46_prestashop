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

<div id="module_toolbar" class="toolbar-placeholder">
    <div class="toolbarBox toolbarHead" style="width: 1393px;">
        <div class="pageTitle">
            <h3>
                <span style="font-weight: normal;">
                    <span class="breadcrumb"><i class="icon-wrench"></i> {$rees46_lang_actions|escape:'htmlall':'UTF-8'}</span>
                </span>
            </h3>
        </div>
    </div>
</div>
<form class="form">
    <table class="table_grid" name="list_table">
        <tr>
            <td>
                <table class="table  module" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class=" text-left">
                                <span class="title_box">{$rees46_lang_name|escape:'htmlall':'UTF-8'}</span>
                            </th>
                            <th class=" text-center">
                                <span class="title_box">{$rees46_lang_status|escape:'htmlall':'UTF-8'}</span>
                            </th>
                            <th class=" text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$rees46_list_values item=value name=value}
                            <tr>
                                <td class="pointer text-left">{$value.name|escape:'htmlall':'UTF-8'}</td>
                                <td class="pointer text-center">
                                    {if $value.status}
                                        <img src="../img/admin/enabled.gif" alt="enabled.gif" title="enabled.gif">
                                    {else}
                                        <img src="../img/admin/disabled.gif" alt="disabled.gif" title="disabled.gif">
                                    {/if}
                                </td>
                                <td class="pointer text-right">
                                    {if !$value.status}
                                        <a id="{$value.id|escape:'htmlall':'UTF-8'}" class="button btn btn-default pull-right"><i class="icon-refresh"></i> {$rees46_lang_repeat|escape:'htmlall':'UTF-8'}</a>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</form>
