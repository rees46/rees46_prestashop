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

{$message|escape:'htmlall':'UTF-8'}
<fieldset>
  <legend>Settings</legend>
  <form method="post">
    <p>
      <label for="MOD_REES46_SHOP_ID">Shop id:</label>
      <input id="MOD_REES46_SHOP_ID" name="MOD_REES46_SHOP_ID" type="text" value="{$MOD_REES46_SHOP_ID|escape:'htmlall':'UTF-8'}" />
    </p>
    <p>
      <label for="MOD_REES46_SECRET_KEY">Secret key:</label>
      <input id="MOD_REES46_SECRET_KEY" name="MOD_REES46_SECRET_KEY" type="text" value="{$MOD_REES46_SECRET_KEY|escape:'htmlall':'UTF-8'}" />
    </p>
    <p>
      <label>&nbsp;</label>
      <input id="submit_{$module_name|escape:'htmlall':'UTF-8'}" name="submit_{$module_name|escape:'htmlall':'UTF-8'}" type="submit" value="Save" class="button" />
    </p>
  </form>

  <form method="post">
    <p>
      <label>&nbsp;</label>
      <input id="submit_unloading_orders" name="submit_unloading_orders" type="submit" value="Export orders to rees46" class="button" />
    </p>
  </form>

  <form method="post">
    <fieldset style="display: inline-block; vertical-align: top; margin-left: 190px;">
      <legend>
        Recommenders configuration
      </legend>
      <div>
        <input type="checkbox" {$checked_for_home_page_popular|escape:'htmlall':'UTF-8'} name="home_page_popular" id="home_page_popular">
        <label for="home_page_popular" style="float: none;">
          Popular on home page
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_popular|escape:'htmlall':'UTF-8'} name="category_page_popular" id="category_page_popular">
        <label for="category_page_popular" style="float: none;">
          Popular on category page(top)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_recently_viewed|escape:'htmlall':'UTF-8'} name="category_page_recently_viewed" id="category_page_recently_viewed">
        <label for="category_page_recently_viewed" style="float: none;">
          Recently viewed on category page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_interesting|escape:'htmlall':'UTF-8'} name="category_page_interesting" id="category_page_interesting">
        <label for="category_page_interesting" style="float: none;">
          Interesting on category page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_also_bought|escape:'htmlall':'UTF-8'} name="product_page_also_bought" id="product_page_also_bought">
        <label for="product_page_also_bought" style="float: none;">
          Also bought on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_similar|escape:'htmlall':'UTF-8'} name="product_page_similar" id="product_page_similar">
        <label for="product_page_similar" style="float: none;">
          Similar on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_ineresting|escape:'htmlall':'UTF-8'} name="product_page_ineresting" id="product_page_ineresting">
        <label for="product_page_ineresting" style="float: none;">
          Interesting on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_cart_page_see_also|escape:'htmlall':'UTF-8'} name="cart_page_see_also" id="cart_page_see_also">
        <label for="cart_page_see_also" style="float: none;">
          See also on cart page(bottom)
        </label>
      </div>
      <div>
        <input type="submit" name="submit_settings" value="Save Settings" class="button" />
      </div>
    </fieldset>
  </form>
</fieldset>
