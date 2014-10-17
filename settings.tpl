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
</fieldset>
