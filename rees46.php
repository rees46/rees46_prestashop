<?php
if (!defined('_PS_VERSION_'))
  exit;

class rees46 extends Module
{
  private $hooks = array('displayHeader', 'displayProductButtons', 'actionValidateOrder');

  function __construct() {
    $this->name = 'rees46';
    $this->tab = 'front_office_features';
    $this->version = '1';
    $this->author = 'mkechinov';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l('REES46');
    $this->description = $this->l('Система рекомендаций для вашего магазина');
    $this->context->smarty->assign('module_name', $this->name);
  }

  public function registerHooks() {
    foreach ($this->hooks as $hook) {
      if (!$this->registerHook($hook)) {
        $this->_errors[] = "Failed to install hook '$hook'<br />\n";
        return false;
      }
    }
    return true;
  }

  public function unregisterHooks() {
    foreach ($this->hooks as $hook) {
      if (!$this->unregisterHook($hook)) {
        $this->_errors[] = "Failed to uninstall hook '$hook'<br />\n";
        return false;
      }
    }
    return true;
  }

  public function install() {
    return (parent::install()
      && $this->registerHooks()
    );
  }

  public function uninstall() {
    return (parent::uninstall()
      && $this->unregisterHooks()
    );
  }

  public function hookDisplayHeader() {
    if ($id_product = (int)Tools::getValue('id_product'))
      $this->context->smarty->assign(array(
        'item_id' => $id_product
      ));
    else
      $this->context->smarty->assign(array(
        'item_id' => null
      ));

    if ($id_category = (int)Tools::getValue('id_category'))
      $this->context->smarty->assign(array(
        'category_id' => $id_category
      ));
    else
      if ($id_product = (int)Tools::getValue('id_product')) {
        $product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        $this->context->smarty->assign(array(
          'category_id' => $product->id_category_default
        ));
      } else
        $this->context->smarty->assign(array(
          'category_id' => null
        ));

    if ($id_cart = (int)$this->context->cookie->id_cart) {
      $cart = new Cart($this->context->cookie->id_cart);
      $products = $cart->getProducts();
      $p_ids = array();
      foreach ($products as $prod) {
        $p_ids[] = $prod['id_product'];
      }
      $this->context->smarty->assign(array(
        'items_in_cart_ids' => $p_ids
      ));
    } else
      $this->context->smarty->assign(array(
        'items_in_cart_ids' => []
      ));

    if ($this->context->cookie->isLogged()) {
      $this->context->smarty->assign(array(
        'user_id' => (int)$this->context->cookie->id_customer,
        'user_email' => $this->context->cookie->email
      ));
    } else {
      $this->context->smarty->assign(array(
        'user_id' => null,
        'user_email' => null
      ));
    }
    $this->context->smarty->assign(array(
      'rees46_shop_id' => Configuration::get('MOD_REES46_SHOP_ID'),
      'rees46_secret_key' => Configuration::get('MOD_REES46_SECRET_KEY')
    ));
    $this->context->controller->addJS('//cdn.rees46.com/rees46_script2.js', 'all');
    return $this->display(__FILE__, 'init_rees46.tpl');
  }

  public function hookDisplayProductButtons() {
    return $this->display(__FILE__, 'product_page.tpl');
  }

  public function hookActionValidateOrder($params) {
    $order_id = $params['order']->id;
    $product_info = array();
    foreach ($params['order']->product_list as $order_product) {
      $item = array();
      $item['item_id'] = $order_product['id_product'];
      $product = new Product($order_product['id_product'], false);
      $item['price'] = $product->getPrice(!Tax::excludeTaxeOption());
      if ($order_product['in_stock'] == true)
        $item['is_available'] = 1;
      else
        $item['is_available'] = 0;
      $item['categories'] = $product->getCategories();
      $item['name'] = $order_product['name'];
      $item['description'] = $order_product['description_short'];
      $link = new Link();
      $item['link'] = $link->getProductLink($product);
      $item['image_url'] = $link->getImageLink($product->link_rewrite, Product::getCover($product->id)['id_image'], 'home_default');
      $product_info[] = $item;
    }
    $cookie_info = array();
    $cookie_info['items'] = $product_info;
    $cookie_info['order_id'] = $order_id;
    setcookie( 'rees46_track_purchase', json_encode($cookie_info), 0, '/' );
  }

  public function getContent() {
    $message = '';

    if (Tools::isSubmit('submit_'.$this->name))
      $message = $this->_saveContent();

    $this->_displayContent($message);

    return $this->display(__FILE__, 'settings.tpl');
  }

  private function _saveContent() {
    $message = '';

    if (Configuration::updateValue('MOD_REES46_SHOP_ID', Tools::getValue('MOD_REES46_SHOP_ID')) &&
      Configuration::updateValue('MOD_REES46_SECRET_KEY', Tools::getValue('MOD_REES46_SECRET_KEY')))
      $message = $this->displayConfirmation($this->l('Ваши настройки сохранены'));
    else
      $message = $this->displayError($this->l('Были ошибки во время сохранения'));

    return $message;
  }

  private function _displayContent($message) {
    $this->context->smarty->assign(array(
      'message' => $message,
      'MOD_REES46_SHOP_ID' => Configuration::get('MOD_REES46_SHOP_ID'),
      'MOD_REES46_SECRET_KEY' => Configuration::get('MOD_REES46_SECRET_KEY'),
    ));
  }
}
