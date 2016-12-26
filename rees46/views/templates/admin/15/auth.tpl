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

<style>
#auth_form #fieldset_0, #auth_form #fieldset_1 {
  display: none;
}
</style>
<fieldset id="fieldset_auth">
  <div class="pull-left" style="width: 50%; float: left; text-align: center;">
    <button type="submit" id="rees46_login" class="button btn btn-primary center-block">
      <i class="icon-key"></i> {$rees46_authorize|escape:'htmlall':'UTF-8'}
    </button>
  </div>
  <div class="pull-right" style="width: 50%; float: right; text-align: center;">
    <button type="submit" id="rees46_register" class="button btn btn-primary center-block">
      <i class="icon-plus-sign"></i> {$rees46_register|escape:'htmlall':'UTF-8'}
    </button>
  </div>
  <div style="clear: both;"></div>
</fieldset>
