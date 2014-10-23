{$message}
<fieldset>
  <legend>Settings</legend>
  <form method="post">
    <p>
      <label for="MOD_REES46_SHOP_ID">Shop id:</label>
      <input id="MOD_REES46_SHOP_ID" name="MOD_REES46_SHOP_ID" type="text" value="{$MOD_REES46_SHOP_ID}" />
    </p>
    <p>
      <label for="MOD_REES46_SECRET_KEY">Secret key:</label>
      <input id="MOD_REES46_SECRET_KEY" name="MOD_REES46_SECRET_KEY" type="text" value="{$MOD_REES46_SECRET_KEY}" />
    </p>
    <p>
      <label>&nbsp;</label>
      <input id="submit_{$module_name}" name="submit_{$module_name}" type="submit" value="Save" class="button" />
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
        <input type="checkbox" {$checked_for_home_page_popular} name="home_page_popular" id="home_page_popular">
        <label for="home_page_popular" style="float: none;">
          Popular on home page
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_popular} name="category_page_popular" id="category_page_popular">
        <label for="category_page_popular" style="float: none;">
          Popular on category page(top)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_recently_viewed} name="category_page_recently_viewed" id="category_page_recently_viewed">
        <label for="category_page_recently_viewed" style="float: none;">
          Recently viewed on category page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_category_page_interesting} name="category_page_interesting" id="category_page_interesting">
        <label for="category_page_interesting" style="float: none;">
          Interesting on category page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_also_bought} name="product_page_also_bought" id="product_page_also_bought">
        <label for="product_page_also_bought" style="float: none;">
          Also bought on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_similar} name="product_page_similar" id="product_page_similar">
        <label for="product_page_similar" style="float: none;">
          Similar on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_product_page_ineresting} name="product_page_ineresting" id="product_page_ineresting">
        <label for="product_page_ineresting" style="float: none;">
          Interesting on product page(bottom)
        </label>
      </div>
      <div>
        <input type="checkbox" {$checked_for_cart_page_see_also} name="cart_page_see_also" id="cart_page_see_also">
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
