<?php
/**
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
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Rees46 extends Module
{
    protected $fields = array(
        'REES46_ACTION_AUTH',
        'REES46_ACTION_XML',
        'REES46_ACTION_ORDER',
        'REES46_ACTION_CUSTOMER',
        'REES46_ACTION_FILE1',
        'REES46_ACTION_FILE2',
        'REES46_API_CATEGORY',
        'REES46_API_KEY',
        'REES46_API_SECRET',
        'REES46_STORE_KEY',
        'REES46_SECRET_KEY',
        'REES46_ORDER_CREATED',
        'REES46_ORDER_COMPLETED',
        'REES46_ORDER_CANCELLED',
        'REES46_XML_CURRENCY',
        'REES46_XML_CRON',
        'REES46_LOG_STATUS',
    );

    protected $hooks = array(
        'header',
        'actionProductAdd',
        'actionProductUpdate',
        'actionProductDelete',
        'actionCartSave',
        'actionValidateOrder',
        'actionOrderStatusPostUpdate',
        'displayHome',
        'displayLeftColumn',
        'displayRightColumn',
        'displayFooterProduct',
        'displayRightColumnProduct',
        'displayLeftColumnProduct',
        'displayShoppingCartFooter',
        'displayOrderConfirmation',
        'displaySearch',
    );

    public $recommends = array(
        'interesting' => 'You may like it',
        'also_bought' => 'Also bought with this product',
        'similar' => 'Similar products',
        'popular' => 'Popular products',
        'see_also' => 'See also',
        'recently_viewed' => 'Recently viewed',
        'buying_now' => 'Right now bought',
        'search' => 'Customers who looked for this product also bought',
    );

    public function __construct()
    {
        $this->name = 'rees46';
        $this->tab = 'front_office_features';
        $this->version = '3.0.6';
        $this->author = 'REES46';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->display = 'view';
        $this->module_key = 'b62df9df084ba63e7aa2ef146fe85c84';
        $this->ps_versions_compliancy = array(
            'min' => '1.5.0.0',
            'max' => _PS_VERSION_,
        );

        parent::__construct();

        $this->displayName = $this->l('REES46 eCommerce Marketing Suite for PrestaShop');
        $this->description = $this->l('The ultimate plug-n-play marketing suite for your online store growth.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        $this->_clearCache('*');

        if (parent::install() && $this->updateFields() && $this->registerHooks()) {
            return true;
        } else {
            return false;
        }
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        if (parent::uninstall() && $this->deleteFields() && $this->unregisterHooks()) {
            return true;
        } else {
            return false;
        }
    }

    protected function updateFields()
    {
        foreach ($this->fields as $field) {
            if (!Configuration::updateValue($field, '')) {
                $this->_errors[] = Tools::displayError('Failed to update value: ' . $field . '.');

                return false;
            }
        }

        return true;
    }

    protected function deleteFields()
    {
        foreach ($this->fields as $field) {
            if (!Configuration::deleteByName($field)) {
                $this->_errors[] = Tools::displayError('Failed to delete value: ' . $field . '.');

                return false;
            }
        }

        for ($id_module = 1; $id_module <= Configuration::get('REES46_MODULE_ID'); $id_module++) {
            Configuration::deleteByName('REES46_MODULE_' . $id_module);
        }

        Configuration::deleteByName('REES46_MODULE_ID');

        return true;
    }

    protected function registerHooks()
    {
        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                $this->_errors[] = Tools::displayError('Failed to install hook: ' . $hook . '.');

                return false;
            }
        }

        return true;
    }

    protected function unregisterHooks()
    {
        foreach ($this->hooks as $hook) {
            if (!$this->unregisterHook($hook)) {
                $this->_errors[] = Tools::displayError('Failed to uninstall hook: ' . $hook . '.');

                return false;
            }
        }

        return true;
    }

    public function hookActionProductAdd($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete($params)
    {
        $this->_clearCache('*');
    }

    public function hookHeader()
    {
        if (Configuration::get('REES46_STORE_KEY') != ''
            && Configuration::get('REES46_SECRET_KEY') != ''
            && $_SERVER['REQUEST_METHOD'] != 'POST'
        ) {
            $js = '<script type="text/javascript">';
            $js .= '(function(r){window.r46=window.r46||function(){(r46.q=r46.q||[]).push(arguments)};';
            $js .= 'var s=document.getElementsByTagName(r)[0],rs=document.createElement(r);rs.async=1;';
            $js .= 'rs.src=\'//cdn.rees46.com/v3.js\';s.parentNode.insertBefore(rs,s);})(\'script\');' . "\n";
            $js .= 'r46(\'init\', \'' . Configuration::get('REES46_STORE_KEY') . '\');' . "\n";

            if ($this->context->customer->isLogged() && (!isset($this->context->cookie->rees46_customer)
                    || (isset($this->context->cookie->rees46_customer)
                    && $this->context->cookie->rees46_customer != $this->context->customer->id))
                ) {
                if ($this->context->customer->id_gender) {
                    $gender = new Gender((int)$this->context->customer->id_gender, $this->context->language->id);

                    if ($gender->type) {
                        $customer_gender = 'f';
                    } else {
                        $customer_gender = 'm';
                    }
                } else {
                    $customer_gender = null;
                }

                if ($this->context->customer->birthday != '0000-00-00') {
                    $customer_birthday = $this->context->customer->birthday;
                } else {
                    $customer_birthday = null;
                }

                $js .= 'r46(\'profile\', \'set\', {';
                $js .= ' id: ' . (int)$this->context->customer->id . ',';
                $js .= ' email: \'' . $this->context->customer->email . '\',';
                $js .= ' gender: \'' . $customer_gender . '\',';
                $js .= ' birthday: \'' . $customer_birthday . '\'';
                $js .= '});' . "\n";

                $this->context->cookie->__set('rees46_customer', $this->context->customer->id);
            } elseif (!$this->context->customer->isLogged() && $this->context->cookie->email) {
                $js .= 'r46(\'profile\', \'set\', {';
                $js .= ' email: \'' . $this->context->customer->email . '\'';
                $js .= '});' . "\n";
            } elseif (!$this->context->customer->isLogged()) {
                unset($this->context->cookie->rees46_customer);
            }

            if (Tools::getValue('id_product')) {
                $product = new Product(
                    (int)Tools::getValue('id_product'),
                    true,
                    $this->context->language->id,
                    $this->context->shop->id
                );

                $img = Product::getCover($product->id);

                if ($product->quantity) {
                    $stock = true;
                } else {
                    $stock = false;
                }

                $image = $this->context->link->getImageLink(
                    $product->link_rewrite[$this->context->language->id],
                    $img['id_image']
                );

                $js .= 'r46(\'track\', \'view\', {';
                $js .= ' id: ' . (int)$product->id . ',';
                $js .= ' stock: ' . (int)$stock . ',';
                $js .= ' price: \'' . $product->getPrice(!Tax::excludeTaxeOption()) . '\',';
                $js .= ' name: \'' . $product->name . '\',';
                $js .= ' categories: ' . Tools::jsonEncode($product->getCategories()) . ',';
                $js .= ' image: \'' . $image . '\',';
                $js .= ' url: \'' . $this->context->link->getProductLink($product->id) . '\',';
                $js .= '});' . "\n";
            }

            if (isset($this->context->cookie->rees46_cart)) {
                $js .= $this->context->cookie->rees46_cart;

                unset($this->context->cookie->rees46_cart);
            }

            if (isset($this->context->cookie->rees46_purchase)) {
                $js .= $this->context->cookie->rees46_purchase;

                unset($this->context->cookie->rees46_purchase);
            }

            $js .= '</script>';

            return $js;
        }
    }

    public function hookActionCartSave()
    {
        if (isset($this->context->cart)
            && Tools::getValue('action') != 'productrefresh'
            && Tools::getValue('id_product') == true
            && Configuration::get('REES46_STORE_KEY') != ''
            && Configuration::get('REES46_SECRET_KEY') != ''
        ) {
            $js = '';
            $product_id = (int)Tools::getValue('id_product');
            $quantity = (int)Tools::getValue('qty');
            $add = Tools::getValue('add');
            $delete = Tools::getValue('delete');
            $op = Tools::getValue('op');

            if ($op && $op == 'up' && $product_id) {
                $js .= 'r46(\'track\', \'cart\', {id: ' . $product_id . ', amount: 1});' . "\n";
            } elseif ($op && $op == 'down' && $product_id) {
                $cart = array();

                foreach ($this->context->cart->getProducts() as $product) {
                    $cart[] = array(
                        'id' => $product['id_product'],
                        'amount' => $product['quantity'],
                    );
                }

                $js .= 'r46(\'track\', \'cart\', ' . Tools::jsonEncode($cart) . ');' . "\n";
            } elseif ($add && $product_id && $quantity) {
                $js .= 'r46(\'track\', \'cart\', {id: ' . $product_id . ', amount: ' . $quantity . '});' . "\n";
            } elseif ($delete && $product_id) {
                $js .= 'r46(\'track\', \'remove_from_cart\', ' . $product_id . ');' . "\n";
            }

            if ($js != '') {
                $this->context->cookie->__set('rees46_cart', $this->context->cookie->rees46_cart . $js);
            }
        }
    }

    public function hookActionValidateOrder($params)
    {
        if (Configuration::get('REES46_STORE_KEY') != ''
            && Configuration::get('REES46_SECRET_KEY') != ''
        ) {
            $js_data = array();

            $js_data['order'] = $params['order']->id;
            $js_data['order_price'] = $params['order']->total_paid;

            foreach ($params['order']->product_list as $order_product) {
                $product = new Product($order_product['id_product'], false);

                $js_data['products'][] = array(
                    'id' => $order_product['id_product'],
                    'price' => $product->getPrice(!Tax::excludeTaxeOption()),
                    'amount' => $order_product['cart_quantity'],
                );
            }

            $js = 'r46(\'track\', \'purchase\', ' . Tools::jsonEncode($js_data) . ');';

            $this->context->cookie->__set('rees46_purchase', $this->context->cookie->rees46_purchase . $js);
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (Configuration::get('REES46_STORE_KEY') != ''
            && Configuration::get('REES46_SECRET_KEY') != ''
        ) {
            $order_id = $params['id_order'];
            $order_status_id = $params['newOrderStatus']->id;

            $rees46_order_created = Tools::jsonDecode(Configuration::get('REES46_ORDER_CREATED'), true);
            $rees46_order_completed = Tools::jsonDecode(Configuration::get('REES46_ORDER_COMPLETED'), true);
            $rees46_order_cancelled = Tools::jsonDecode(Configuration::get('REES46_ORDER_CANCELLED'), true);

            if ($rees46_order_created && in_array($order_status_id, $rees46_order_created)) {
                $status = 0;
            } elseif ($rees46_order_completed && in_array($order_status_id, $rees46_order_completed)) {
                $status = 1;
            } elseif ($rees46_order_cancelled && in_array($order_status_id, $rees46_order_cancelled)) {
                $status = 2;
            }

            if (isset($status)) {
                $data = array();

                $data[] = array(
                    'id' => $order_id,
                    'status' => $order_status_id,
                );

                $curl_data = array();

                $curl_data['shop_id'] = Configuration::get('REES46_STORE_KEY');
                $curl_data['shop_secret'] = Configuration::get('REES46_SECRET_KEY');
                $curl_data['orders'] = $data;

                $url = 'http://api.rees46.com/import/sync_orders';

                $return = $this->curl('POST', $url, Tools::jsonEncode($curl_data));

                if (Configuration::get('REES46_LOG_STATUS')) {
                    if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                        if (version_compare(_PS_VERSION_, '1.6', '<')) {
                            Logger::addLog(
                                'REES46: autoexport status [' . $order_status_id . '] of order_id [' . $order_id . ']',
                                3,
                                $return['info']['http_code'],
                                null,
                                null,
                                true
                            );
                        } else {
                            PrestaShopLogger::addLog(
                                'REES46: autoexport status [' . $order_status_id . '] of order_id [' . $order_id . ']',
                                3,
                                $return['info']['http_code'],
                                null,
                                null,
                                true
                            );
                        }
                    } else {
                        if (version_compare(_PS_VERSION_, '1.6', '<')) {
                            Logger::addLog(
                                'REES46: autoexport status [' . $order_status_id . '] of order_id [' . $order_id . ']',
                                1,
                                null,
                                null,
                                null,
                                true
                            );
                        } else {
                            PrestaShopLogger::addLog(
                                'REES46: autoexport status [' . $order_status_id . '] of order_id [' . $order_id . ']',
                                1,
                                null,
                                null,
                                null,
                                true
                            );
                        }
                    }
                }
            }
        }
    }

    public function hookDisplayHome($params)
    {
        return $this->getModules('displayHome');
    }

    public function hookDisplayLeftColumn($params)
    {
        return $this->getModules('displayLeftColumn');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->getModules('displayRightColumn');
    }

    public function hookDisplayFooterProduct($params)
    {
        return $this->getModules('displayFooterProduct');
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->getModules('displayRightColumnProduct');
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        return $this->getModules('displayLeftColumnProduct');
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        return $this->getModules('displayShoppingCartFooter');
    }

    public function hookDisplayOrderConfirmation($params)
    {
        return $this->getModules('displayOrderConfirmation');
    }

    public function hookDisplaySearch($params)
    {
        return $this->getModules('displaySearch');
    }

    protected function getModules($hook)
    {
        if (Configuration::get('REES46_STORE_KEY') != ''
            && Configuration::get('REES46_SECRET_KEY') != ''
            && Configuration::get('REES46_MODULE_ID')
        ) {
            if (Tools::getValue('id_product')) {
                $item = (int)Tools::getValue('id_product');

                $product = new Product($item, true, $this->context->language->id, $this->context->shop->id);

                $category = (int)$product->id_category_default;
            }

            if (Tools::getValue('id_category')) {
                $category = (int)Tools::getValue('id_category');
            }

            if ($this->context->cart->getProducts()) {
                $cart = array();

                foreach ($this->context->cart->getProducts() as $product) {
                    $cart[] = $product['id_product'];
                }
            }

            if (Tools::getValue('s')) {
                $search_query = Tools::getValue('s');
            } elseif (Tools::getValue('search_query')) {
                $search_query = Tools::getValue('search_query');
            }

            $modules = array();

            for ($id_module = 1; $id_module <= Configuration::get('REES46_MODULE_ID'); $id_module++) {
                $settings = Tools::jsonDecode(Configuration::get('REES46_MODULE_' . $id_module), true);

                if ($settings['hook'] == $hook && $settings['status']) {
                    $modules[] = $settings;

                    $css = false;

                    if ($settings['template'] == 'basic') {
                        $css = true;
                    } elseif ($settings['template'] == 'product-list') {
                        $this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
                    }
                }
            }

            if (!empty($modules)) {
                foreach ($modules as $key => $module) {
                    $params = array();

                    if ($module['limit'] > 0) {
                        $params['limit'] = (int)$module['limit'];
                    } else {
                        $params['limit'] = 6;
                    }

                    $params['discount'] = (int)$module['discount'];

                    $manufacturers = Tools::jsonDecode($module['manufacturers'], true);

                    if (!empty($manufacturers)) {
                        $params['brands'] = array();

                        foreach ($manufacturers as $manufacturer) {
                             $params['brands'][] = Manufacturer::getNameById($manufacturer);
                        }
                    }

                    $manufacturers_exclude = Tools::jsonDecode($module['manufacturers_exclude'], true);

                    if (!empty($manufacturers_exclude)) {
                        $params['exclude_brands'] = array();

                        foreach ($manufacturers_exclude as $manufacturer) {
                            $params['exclude_brands'][] = Manufacturer::getNameById($manufacturer);
                        }
                    }

                    if ($module['type'] == 'interesting') {
                        if (isset($item)) {
                            $params['item'] = $item;
                        }

                        $modules[$key]['params'] = $params;
                    } elseif ($module['type'] == 'also_bought') {
                        if (isset($item)) {
                            $params['item'] = $item;

                            $modules[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'similar') {
                        if (isset($item) && isset($cart)) {
                            $params['item'] = $item;
                            $params['cart'] = $cart;

                            if (isset($category)) {
                                $params['category'] = $category;
                            }

                            $modules[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'popular') {
                        if (isset($category)) {
                            $params['category'] = $category;
                        }

                        $modules[$key]['params'] = $params;
                    } elseif ($module['type'] == 'see_also') {
                        if (isset($cart)) {
                            $params['cart'] = $cart;

                            $modules[$key]['params'] = $params;
                        }
                    } elseif ($module['type'] == 'recently_viewed') {
                        $modules[$key]['params'] = $params;
                    } elseif ($module['type'] == 'buying_now') {
                        if (isset($item)) {
                            $params['item'] = $item;
                        }

                        if (isset($cart)) {
                            $params['cart'] = $cart;
                        }

                        $modules[$key]['params'] = $params;
                    } elseif ($module['type'] == 'search') {
                        if (isset($search_query)) {
                            $params['search_query'] = $search_query;

                            if (isset($cart)) {
                                $params['cart'] = $cart;
                            }

                            $modules[$key]['params'] = $params;
                        }
                    }

                    $modules[$key]['link'] = $this->context->link->getPageLink(
                        'index',
                        true
                    );
                }

                uasort($modules, function ($a, $b) {
                    return ($a['position'] - $b['position']);
                });

                $this->context->smarty->assign(
                    array(
                        'rees46_modules' => $modules,
                        'rees46_css' => $css,
                    )
                );

                return $this->display(__FILE__, 'views/templates/hook/init.tpl');
            }
        }
    }

    public function getContent()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addJS($this->_path.'views/js/admin/15/rees46.js');
        } else {
            $this->context->controller->addJS($this->_path.'views/js/admin/rees46.js');
        }

        $output = null;

        if (Configuration::get('REES46_AUTH') == ''
            && Configuration::get('REES46_STORE_KEY') == ''
            && Configuration::get('REES46_SECRET_KEY') == ''
        ) {
            $url = 'https://rees46.com/trackcms/prestashop?';

            $params = array(
                'website' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'email' => Configuration::get('PS_SHOP_EMAIL'),
                'first_name' => $this->context->employee->firstname,
                'last_name' => $this->context->employee->lastname,
            );

            if (Configuration::get('PS_SHOP_PHONE') != '') {
                $params['phone'] = Configuration::get('PS_SHOP_PHONE');
            }

            if (Configuration::get('PS_SHOP_CITY') != '') {
                $params['city'] = Configuration::get('PS_SHOP_CITY');
            }

            if (Configuration::get('PS_SHOP_COUNTRY') != '') {
                $params['country'] = Configuration::get('PS_SHOP_COUNTRY');
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_URL, $url . http_build_query($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_exec($ch);
            curl_close($ch);

            $output .= $this->displayAuth()
                . $this->renderFormAuth()
                . $this->renderFormHelp();
        } elseif (Tools::isSubmit('submit' . $this->name)) {
            $this->_clearCache('*');

            foreach ($this->fields as $field) {
                if ('REES46_ORDER' == Tools::substr($field, 0, 12)) {
                    Configuration::updateValue($field, Tools::jsonEncode(Tools::getValue($field)));
                } else {
                    Configuration::updateValue($field, Tools::getValue($field));
                }
            }

            $output .= $this->displayConfirmation($this->l('The settings have been successfully updated.'));

            $output .= $this->renderFormHelp()
                . $this->renderFormGeneral()
                . $this->renderListBlocks()
                . $this->displayActions();
        } elseif (Tools::isSubmit('submit_module')) { // save module
            Configuration::updateValue('REES46_MODULE_' . Tools::getValue('id_module'), Tools::jsonEncode(
                $this->getModuleValues()
            ));

            $output .= $this->displayConfirmation($this->l('The settings have been successfully updated.'));

            $output .= $this->renderFormHelp()
                . $this->renderFormGeneral()
                . $this->renderListBlocks()
                . $this->displayActions();
        } elseif (Tools::isSubmit('deleteblocks')
            && Tools::isSubmit('id_module')
            && Configuration::get('REES46_MODULE_' . Tools::getValue('id_module'))
        ) { // delete module
            Configuration::deleteByName('REES46_MODULE_' . Tools::getValue('id_module'));

            $output .= $this->displayConfirmation($this->l('The settings have been successfully updated.'));

            $output .= $this->renderFormHelp()
                . $this->renderFormGeneral()
                . $this->renderListBlocks()
                . $this->displayActions();
        } elseif (Tools::isSubmit('new_module')
            || (Tools::isSubmit('id_module')
            && Configuration::get('REES46_MODULE_' . Tools::getValue('id_module')))
        ) { // view module
            $output .= $this->renderFormModule();
        } else {
            $output .= $this->renderFormHelp()
                . $this->renderFormGeneral()
                . $this->renderListBlocks()
                . $this->displayActions();
        }

        return $output;
    }

    protected function renderFormAuth()
    {
        $fields_form = array();

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Authorization Form'),
                'icon' => 'icon-key',
            ),
            'description' => $this->l('To authorize, please log in on rees46.com and copy & paste Store Key and Secret')
                . $this->l(' Key from Store Settings (Dashboard > Settings > Store Settings) to the fields below.'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Store Key'),
                    'name' => 'auth_store_key',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Secret Key'),
                    'name' => 'auth_secret_key',
                    'required' => true,
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Send'),
                    'icon' => 'icon-check',
                    'name' => 'submitShopLogin',
                    'id' => 'submitShopLogin',
                    'class' => 'button btn btn-default pull-right',
                ),
            ),
        );

        $countries = Country::getCountries($this->context->language->id);

        unset($countries['231']);

        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Registration Form'),
                'icon' => 'icon-plus-sign',
            ),
            'description' => $this->l('To register, please fill out the form below. ')
                . $this->l('Authorization, in this case, is performed automatically.'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'auth_email',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone Number'),
                    'name' => 'auth_phone',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First Name'),
                    'name' => 'auth_first_name',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last Name'),
                    'name' => 'auth_last_name',
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'auth_country_code',
                    'options' => array(
                        'query' => $countries,
                        'id' => 'iso_code',
                        'name' => 'name',
                    ),
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Category'),
                    'name' => 'auth_category',
                    'options' => array(
                        'query' => $this->rees46ShopCategories(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'required' => true,
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Send'),
                    'icon' => 'icon-check',
                    'name' => 'submitUserRegister',
                    'id' => 'submitUserRegister',
                    'class' => 'button btn btn-default pull-right',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->table = 'auth';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')?Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG'):0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAuth' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'auth_store_key' => '',
                'auth_secret_key' => '',
                'auth_email' => Configuration::get('PS_SHOP_EMAIL'),
                'auth_phone' => Configuration::get('PS_SHOP_PHONE'),
                'auth_first_name' => $this->context->employee->firstname,
                'auth_last_name' => $this->context->employee->lastname,
                'auth_country_code' => Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
                'auth_category' => '',
            ),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $helper->toolbar_btn = array(
                'new' => array(
                    'desc' => $this->l('Send'),
                    'name' => 'submitShopLogin',
                ),
                'newAttributes' => array(
                    'desc' => $this->l('Send'),
                    'name' => 'submitUserRegister',
                ),
            );
        }

        return $helper->generateForm($fields_form);
    }

    protected function renderFormGeneral()
    {
        $order_statuses = array();

        foreach (OrderState::getOrderStates((int)$this->context->language->id) as $order_status) {
            $order_statuses[] = array(
                'id' => (int)$order_status['id_order_state'],
                'val' => (int)$order_status['id_order_state'],
                'name' => $order_status['name'],
            );
        }

        $fields_form = array();

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('General'),
                'icon' => 'icon-cog',
            ),
            'description' => (Configuration::get('REES46_STORE_KEY') == ''
                || Configuration::get('REES46_SECRET_KEY') == '')
                ? $this->l('To start using this module, please register an account on')
                . ' <a href="https://rees46.com/prestashop" target="_blank">rees46.com'
                . ' <i class="icon-external-link"></i></a> '
                . $this->l('and get API keys for this form.')
                : false,
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_AUTH',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_XML',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_ORDER',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_CUSTOMER',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_FILE1',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_ACTION_FILE2',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_API_KEY',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'REES46_API_SECRET',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Store Key'),
                    'name' => 'REES46_STORE_KEY',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Secret Key'),
                    'name' => 'REES46_SECRET_KEY',
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Created Order Status'),
                    'name' => 'REES46_ORDER_CREATED[]',
                    'values' => array(
                        'query' => $order_statuses,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'text' => $this->l('show'),
                            'icon' => 'plus-sign-alt',
                        ),
                        'hide' => array(
                            'text' => $this->l('hide'),
                            'icon' => 'minus-sign-alt',
                        ),
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Completed Order Status'),
                    'name' => 'REES46_ORDER_COMPLETED[]',
                    'values' => array(
                        'query' => $order_statuses,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'text' => $this->l('show'),
                            'icon' => 'plus-sign-alt',
                        ),
                        'hide' => array(
                            'text' => $this->l('hide'),
                            'icon' => 'minus-sign-alt',
                        ),
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Cancelled Order Status'),
                    'name' => 'REES46_ORDER_CANCELLED[]',
                    'values' => array(
                        'query' => $order_statuses,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'text' => $this->l('show'),
                            'icon' => 'plus-sign-alt',
                        ),
                        'hide' => array(
                            'text' => $this->l('hide'),
                            'icon' => 'minus-sign-alt',
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product Сurrency'),
                    'name' => 'REES46_XML_CURRENCY',
                    'options' => array(
                        'query' => Currency::getCurrenciesByIdShop((int)Tools::getValue('id_shop')),
                        'id' => 'id_currency',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Cron Task Link'),
                    'name' => 'REES46_XML_CRON',
                    'readonly' => true,
                ),
                array(
                    'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
                    'label' => $this->l('Logging'),
                    'name' => 'REES46_LOG_STATUS',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'class' => 't',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'icon' => 'icon-save',
                'class' => 'button btn btn-default pull-right',
            ),
        );

        $helper = new HelperForm();
        $helper->table = 'general';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')?Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG'):0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;

        foreach ($this->fields as $field) {
            if ('REES46_XML_CRON' == $field) {
                $xml_cron = _PS_BASE_URL_ . __PS_BASE_URI__ . 'index.php?fc=module&module=rees46&controller=cron';

                $helper->tpl_vars['fields_value'][$field] = $xml_cron;
            } elseif ('REES46_ORDER' == Tools::substr($field, 0, 12)) {
                if (is_array(Tools::jsonDecode(Configuration::get($field), true))) {
                    foreach (OrderState::getOrderStates((int)$this->context->language->id) as $order_status) {
                        $helper->tpl_vars['fields_value'][$field . '[]_' . $order_status['id_order_state']] =
                            in_array(
                                $order_status['id_order_state'],
                                Tools::jsonDecode(Configuration::get($field), true)
                            ) ? true : false;
                    }
                } else {
                    $helper->tpl_vars['fields_value'][$field] = array();
                }
            } else {
                $helper->tpl_vars['fields_value'][$field] = Configuration::get($field);
            }
        }

        return $helper->generateForm($fields_form);
    }

    protected function renderListBlocks()
    {
        $content = $this->getListBlocksValues();

        $fields_list = array(
            'id_module' => array(
                'title' => $this->l('ID'),
                'orderby' => false,
                'search' => false,
                'align' => 'text-left',
            ),
            'hook' => array(
                'title' => $this->l('Hook'),
                'orderby' => false,
                'search' => false,
            ),
            'type' => array(
                'title' => $this->l('Block Type'),
                'orderby' => false,
                'search' => false,
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'orderby' => false,
                'search' => false,
                'align' => 'text-center',
            ),
            'status' => array(
                'title' => $this->l('Block Status'),
                'orderby' => false,
                'search' => false,
                'align' => 'text-center',
                'icon' => array(
                    0 => 'disabled.gif',
                    1 => 'enabled.gif',
                ),
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->listTotal = count($content);
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->table = 'blocks';
        $helper->title = '<i class="icon-puzzle-piece"></i> ' . $this->l('Product Recommendations Blocks');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->identifier = $this->identifier;
        $helper->actions = array('edit', 'delete',);
        $helper->module = $this;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->toolbar_btn = array(
            'new' => array(
                'href' => AdminController::$currentIndex
                    . '&configure=' . $this->name
                    . '&new_module=1'
                    . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Add'),
                'icon' => 'process-icon-new',
            ),
        );

        return $helper->generateList($content, $fields_list);
    }

    protected function getListBlocksValues()
    {
        $list_values = array();

        if (!Configuration::get('REES46_MODULE_ID')) {
            Configuration::updateValue('REES46_MODULE_ID', 0);
        }

        for ($id_module = 1; $id_module <= Configuration::get('REES46_MODULE_ID'); $id_module++) {
            if (Configuration::get('REES46_MODULE_' . $id_module)) {
                $module_values = Tools::jsonDecode(Configuration::get('REES46_MODULE_' . $id_module), true);

                $list_values[] = array(
                    'id_module' => $module_values['id_module'],
                    'hook' => $module_values['hook'],
                    'type' => $this->l($this->recommends[$module_values['type']]),
                    'position' => $module_values['position'],
                    'status' => $module_values['status'],
                );
            } else {
                continue;
            }
        }

        return $list_values;
    }

    protected function renderFormHelp()
    {
        $fields_form = array();

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Help'),
                'icon' => 'icon-comments',
            ),
            'description' => $this->l('Go to your REES46 store dashboard to get the access to:')
                . '<br><ul>'
                . '<li>' . $this->l('Triggered emails') . '</li>'
                . '<li>' . $this->l('Email marketing tool') . '</li>'
                . '<li>' . $this->l('Personalized search') . '</li>'
                . '<li>' . $this->l('Web push triggered notifications') . '</li>'
                . '<li>' . $this->l('Instant web push notifications') . '</li>'
                . '<li>' . $this->l('Audience segmentation') . '</li>'
                . '<li>' . $this->l('Abandoned cart remarketing tool') . '</li>'
                . '</ul><br>'
                . '<a href="https://rees46.com/customers/sign_in" target="_blank" class="button btn btn-primary">'
                . $this->l('REES46 dashboard')
                . '</a><br><br>'
                . $this->l('Documentation:')
                . ' <a href="'
                . $this->l('http://docs.rees46.com/display/en/PrestaShop+Module')
                . '" target="_blank">'
                . $this->l('http://docs.rees46.com/display/en/PrestaShop+Module')
                . ' <i class="icon-external-link"></i></a><br><br>'
                . $this->l('Support:')
                . ' <a href="'
                . $this->l('https://addons.prestashop.com/en/contact-us?id_product=18056')
                . '" target="_blank">'
                . $this->l('https://addons.prestashop.com/en/contact-us?id_product=18056')
                . ' <i class="icon-external-link"></i></a>',
        );

        $helper = new HelperForm();
        $helper->table = 'help';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')?Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG'):0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;

        return $helper->generateForm($fields_form);
    }

    protected function renderFormModule()
    {
        $images_types = ImageType::getImagesTypes('products');

        $manufacturers = array();

        foreach (Manufacturer::getManufacturers() as $manufacturer) {
            $manufacturers[] = array(
                'id' => (int)$manufacturer['id_manufacturer'],
                'val' => (int)$manufacturer['id_manufacturer'],
                'name' => htmlspecialchars(trim($manufacturer['name'])),
            );
        }

        $fields_form = array();

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Product Recommendations Block'),
                'icon' => 'icon-puzzle-piece',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_module',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook'),
                    'name' => 'hook',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'displayHome',
                                'name' => 'displayHome',
                            ),
                            array(
                                'id' => 'displayLeftColumn',
                                'name' => 'displayLeftColumn',
                            ),
                            array(
                                'id' => 'displayRightColumn',
                                'name' => 'displayRightColumn',
                            ),
                            array(
                                'id' => 'displayFooterProduct',
                                'name' => 'displayFooterProduct',
                            ),
                            array(
                                'id' => 'displayRightColumnProduct',
                                'name' => 'displayRightColumnProduct',
                            ),
                            array(
                                'id' => 'displayLeftColumnProduct',
                                'name' => 'displayLeftColumnProduct',
                            ),
                            array(
                                'id' => 'displayShoppingCartFooter',
                                'name' => 'displayShoppingCartFooter',
                            ),
                            array(
                                'id' => 'displayOrderConfirmation',
                                'name' => 'displayOrderConfirmation',
                            ),
                            array(
                                'id' => 'displaySearch',
                                'name' => 'displaySearch',
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Block Type'),
                    'name' => 'type',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'interesting',
                                'name' => $this->l('You may like it'),
                            ),
                            array(
                                'id' => 'also_bought',
                                'name' => $this->l('Also bought with this product'),
                            ),
                            array(
                                'id' => 'similar',
                                'name' => $this->l('Similar products'),
                            ),
                            array(
                                'id' => 'popular',
                                'name' => $this->l('Popular products'),
                            ),
                            array(
                                'id' => 'see_also',
                                'name' => $this->l('See also'),
                            ),
                            array(
                                'id' => 'recently_viewed',
                                'name' => $this->l('Recently viewed'),
                            ),
                            array(
                                'id' => 'buying_now',
                                'name' => $this->l('Right now bought'),
                            ),
                            array(
                                'id' => 'search',
                                'name' => $this->l('Customers who looked for this product also bought'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Block Title'),
                    'name' => 'title',
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Limit'),
                    'name' => 'limit',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Image Type'),
                    'name' => 'image_type',
                    'options' => array(
                        'query' => $images_types,
                        'id' => 'name',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Recommendation Block Template'),
                    'name' => 'template',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'home',
                                'name' => $this->l('Home'),
                            ),
                            array(
                                'id' => 'sidebar',
                                'name' => $this->l('Sidebar'),
                            ),
                            array(
                                'id' => 'product-list',
                                'name' => $this->l('Product List'),
                            ),
                            array(
                                'id' => 'basic',
                                'name' => $this->l('Basic REES46'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
                    'label' => $this->l('Show Only Special Offers'),
                    'name' => 'discount',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'class' => 't',
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Show Only Products of Following Brands'),
                    'name' => 'manufacturers[]',
                    'values' => array(
                        'query' => $manufacturers,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'text' => $this->l('show'),
                            'icon' => 'plus-sign-alt',
                        ),
                        'hide' => array(
                            'text' => $this->l('hide'),
                            'icon' => 'minus-sign-alt',
                        ),
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Exclude Products of Following Brands'),
                    'name' => 'manufacturers_exclude[]',
                    'values' => array(
                        'query' => $manufacturers,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'text' => $this->l('show'),
                            'icon' => 'plus-sign-alt',
                        ),
                        'hide' => array(
                            'text' => $this->l('hide'),
                            'icon' => 'minus-sign-alt',
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position Block Within the Hook'),
                    'name' => 'position',
                ),
                array(
                    'type' => version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch',
                    'label' => $this->l('Block Status'),
                    'name' => 'status',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'class' => 't',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'icon' => 'icon-save',
                'class' => 'button btn btn-default pull-right',
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')?Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG'):0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_module';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name='. $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getModuleValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }

    protected function getModuleValues()
    {
        $module_values = array();

        if (Tools::isSubmit('submit_module')) { // save module
            $module_values['id_module'] = Tools::getValue('id_module');
            $module_values['hook'] = Tools::getValue('hook');
            $module_values['type'] = Tools::getValue('type');
            $module_values['limit'] = Tools::getValue('limit');
            $module_values['template'] = Tools::getValue('template');
            $module_values['image_type'] = Tools::getValue('image_type');
            $module_values['discount'] = Tools::getValue('discount');
            $module_values['manufacturers'] = Tools::jsonEncode(Tools::getValue('manufacturers'));
            $module_values['manufacturers_exclude'] = Tools::jsonEncode(Tools::getValue('manufacturers_exclude'));
            $module_values['position'] = Tools::getValue('position');
            $module_values['status'] = Tools::getValue('status');

            $languages = Language::getLanguages(false);

            foreach ($languages as $lang) {
                $module_values['title'][$lang['id_lang']] = Tools::getValue('title_' . (int)$lang['id_lang']);
            }
        } elseif (Tools::isSubmit('id_module')
            && Configuration::get('REES46_MODULE_' . Tools::getValue('id_module'))
        ) { // view module
            $module_values = Tools::jsonDecode(Configuration::get('REES46_MODULE_'.Tools::getValue('id_module')), true);

            if (is_array(Tools::jsonDecode($module_values['manufacturers'], true))) {
                foreach (Manufacturer::getManufacturers() as $manufacturer) {
                    $module_values['manufacturers[]_' . $manufacturer['id_manufacturer']] =
                        in_array(
                            $manufacturer['id_manufacturer'],
                            Tools::jsonDecode($module_values['manufacturers'], true)
                        ) ? true : false;
                }
            } else {
                $module_values['manufacturers'] = array();
            }

            if (is_array(Tools::jsonDecode($module_values['manufacturers_exclude'], true))) {
                foreach (Manufacturer::getManufacturers() as $manufacturer) {
                    $module_values['manufacturers_exclude[]_' . $manufacturer['id_manufacturer']] =
                        in_array(
                            $manufacturer['id_manufacturer'],
                            Tools::jsonDecode($module_values['manufacturers_exclude'], true)
                        ) ? true : false;
                }
            } else {
                $module_values['manufacturers_exclude'] = array();
            }
        } else { // new module
            $id_module = Configuration::get('REES46_MODULE_ID') + 1;

            Configuration::updateValue('REES46_MODULE_ID', $id_module);

            $module_values['id_module'] = $id_module;
            $module_values['hook'] = 'displayHome';
            $module_values['type'] = 'interesting';
            $module_values['limit'] = '';
            $module_values['template'] = 'default';
            $module_values['image_type'] = '';
            $module_values['discount'] = 0;
            $module_values['manufacturers'] = array();
            $module_values['manufacturers_exclude'] = array();
            $module_values['position'] = 0;
            $module_values['status'] = 0;

            $languages = Language::getLanguages(false);

            foreach ($languages as $lang) {
                $module_values['title'][$lang['id_lang']] = '';
            }
        }

        return $module_values;
    }

    protected function displayAuth()
    {
        $this->context->smarty->assign(
            array(
                'rees46_authorize' => $this->l('Authorize'),
                'rees46_register' => $this->l('Register'),
            )
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            return $this->display(__FILE__, 'views/templates/admin/15/auth.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/auth.tpl');
        }
    }

    protected function displayActions()
    {
        $list_values = array();

        $list_values[] = array(
            'id' => 'submitShopXML',
            'name' => $this->l('Export XML Product Feed'),
            'status' => Configuration::get('REES46_ACTION_XML'),
        );

        $list_values[] = array(
            'id' => 'submitShopOrders',
            'name' => $this->l('Export Orders'),
            'status' => Configuration::get('REES46_ACTION_ORDER'),
        );

        $list_values[] = array(
            'id' => 'submitShopCustomers',
            'name' => $this->l('Export Customers'),
            'status' => Configuration::get('REES46_ACTION_CUSTOMER'),
        );

        $list_values[] = array(
            'id' => 'submitShopFile1',
            'name' => $this->l('Load manifest.json'),
            'status' => Configuration::get('REES46_ACTION_FILE1'),
        );

        $list_values[] = array(
            'id' => 'submitShopFile2',
            'name' => $this->l('Load push_sw.js'),
            'status' => Configuration::get('REES46_ACTION_FILE2'),
        );

        $this->context->smarty->assign(
            array(
                'rees46_lang_actions' => $this->l('Actions'),
                'rees46_lang_name' => $this->l('Name'),
                'rees46_lang_status' => $this->l('Status'),
                'rees46_lang_repeat' => $this->l('Repeat'),
                'rees46_list_values' => $list_values,
                'rees46_secure_key' => Tools::encrypt(Configuration::get('REES46_XML_CRON') . _PS_VERSION_),
            )
        );

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            return $this->display(__FILE__, 'views/templates/admin/15/actions.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/actions.tpl');
        }
    }

    public function ajaxProcessRees46UserRegister()
    {
        return $this->rees46UserRegister();
    }

    public function ajaxProcessRees46ShopRegister()
    {
        return $this->rees46ShopRegister();
    }

    public function ajaxProcessRees46ShopXML()
    {
        return $this->rees46ShopXML();
    }

    public function ajaxProcessRees46ShopOrders()
    {
        return $this->rees46ShopOrders();
    }

    public function ajaxProcessRees46ShopCustomers()
    {
        return $this->rees46ShopCustomers();
    }

    public function ajaxProcessRees46ShopFiles()
    {
        return $this->rees46ShopFiles();
    }

    public function ajaxProcessRees46ShopFinish()
    {
        return $this->rees46ShopFinish();
    }

    protected function rees46ShopCategories()
    {
        $json = array();

        $return = $this->curl('GET', 'https://rees46.com/api/categories');

        if (isset($return['result'])) {
            $json = $return['result'];
        }

        return Tools::jsonDecode($json, true);
    }

    protected function rees46UserRegister()
    {
        $json = array();

        if (!Validate::isEmail(Tools::getValue('email')) || Tools::getValue('email') == '') {
            $json['error'] = $this->l('Incorrect value for Email field.');
        }

        if (!Validate::isPhoneNumber(Tools::getValue('phone')) || Tools::getValue('phone') == '') {
            $json['error'] = $this->l('Incorrect value for Phone Number field.');
        }

        if (!Validate::isName(Tools::getValue('first_name')) || Tools::getValue('first_name') == '') {
            $json['error'] = $this->l('Incorrect value for First Name field.');
        }

        if (!Validate::isName(Tools::getValue('last_name')) || Tools::getValue('last_name') == '') {
            $json['error'] = $this->l('Incorrect value for Last Name field.');
        }

        if (!Validate::isName(Tools::getValue('country_code')) || Tools::getValue('country_code') == '') {
            $json['error'] = $this->l('Incorrect value for Country field.');
        }

        if (!Validate::isInt(Tools::getValue('category')) || Tools::getValue('category') == '') {
            $json['error'] = $this->l('Incorrect value for Product Category field.');
        }

        if (isset($json['error'])) {
            die(Tools::jsonEncode($json));
        }

        $curl_data = array();

        $curl_data['email'] = Tools::getValue('email');
        $curl_data['phone'] = Tools::getValue('phone');
        $curl_data['first_name'] = Tools::getValue('first_name');
        $curl_data['last_name'] = Tools::getValue('last_name');
        $curl_data['country_code'] = Tools::getValue('country_code');

        $return = $this->curl('POST', 'https://rees46.com/api/customers', Tools::jsonEncode($curl_data));

        $result = Tools::jsonDecode($return['result'], true);

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $json['error'] = $this->l('Could not register an account. Please, check the form was filled out correctly');

            if (Configuration::get('REES46_LOG_STATUS')) {
                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    Logger::addLog(
                        'REES46: could not register',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                } else {
                    PrestaShopLogger::addLog(
                        'REES46: could not register',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                }
            }
        } else {
            if (isset($result['duplicate'])) {
                $json['error'] = $this->l('Account already exists. Please, authorize.');
            } else {
                Configuration::updateValue('REES46_ACTION_AUTH', true);
                Configuration::updateValue('REES46_API_KEY', $result['api_key']);
                Configuration::updateValue('REES46_API_SECRET', $result['api_secret']);
                Configuration::updateValue('REES46_API_CATEGORY', Tools::getValue('category'));

                $json['success'] = $this->l('Account successfully registered.');
            }
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopRegister()
    {
        $json = array();

        if (Configuration::get('REES46_API_KEY') == '') {
            $json['error'] = $this->l('Incorrect value for Store Key field.');
        }

        if (Configuration::get('REES46_API_SECRET') == '') {
            $json['error'] = $this->l('Incorrect value for Secret Key field.');
        }

        if (Configuration::get('REES46_API_CATEGORY') == '') {
            $json['error'] = $this->l('Incorrect value for Product Category field.');
        }

        if (isset($json['error'])) {
            die(Tools::jsonEncode($json));
        }

        $curl_data = array();

        $curl_data['api_key'] = Configuration::get('REES46_API_KEY');
        $curl_data['api_secret'] = Configuration::get('REES46_API_SECRET');
        $curl_data['url'] = _PS_BASE_URL_ . __PS_BASE_URI__;
        $curl_data['name'] = Configuration::get('PS_SHOP_NAME');
        $curl_data['category'] = Configuration::get('REES46_API_CATEGORY');
        $curl_data['yml_file_url'] = _PS_BASE_URL_.__PS_BASE_URI__ . 'index.php?fc=module&module=rees46&controller=xml';
        $curl_data['cms_id'] = 16;

        $return = $this->curl('POST', 'https://rees46.com/api/shops', Tools::jsonEncode($curl_data));

        $result = Tools::jsonDecode($return['result'], true);

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $json['error'] = $this->l('Could not create a store.');

            if (Configuration::get('REES46_LOG_STATUS')) {
                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    Logger::addLog(
                        'REES46: could not create a store',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                } else {
                    PrestaShopLogger::addLog(
                        'REES46: could not create a store',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                }
            }
        } else {
            Configuration::updateValue('REES46_STORE_KEY', $result['shop_key']);
            Configuration::updateValue('REES46_SECRET_KEY', $result['shop_secret']);

            $json['success'] = $this->l('Store successfully created.');
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopXML()
    {
        $json = array();

        $curl_data = array();

        if (Configuration::get('REES46_STORE_KEY') != '') {
            $curl_data['store_key'] = Configuration::get('REES46_STORE_KEY');
        } elseif (Tools::getValue('store_key') != '') {
            $curl_data['store_key'] = Tools::getValue('store_key');
        } else {
            $json['error'] = $this->l('Incorrect value for Store Key field.');
        }

        if (Configuration::get('REES46_SECRET_KEY') != '') {
            $curl_data['store_secret'] = Configuration::get('REES46_SECRET_KEY');
        } elseif (Tools::getValue('secret_key') != '') {
            $curl_data['store_secret'] = Tools::getValue('secret_key');
        } else {
            $json['error'] = $this->l('Incorrect value for Secret Key field.');
        }

        if (isset($json['error'])) {
            die(Tools::jsonEncode($json));
        }

        $curl_data['yml_file_url'] = _PS_BASE_URL_.__PS_BASE_URI__ . 'index.php?fc=module&module=rees46&controller=xml';

        $return = $this->curl('PUT', 'https://rees46.com/api/shop/set_yml', Tools::jsonEncode($curl_data));

        if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
            $json['error'] = $this->l('Could not export product feed.');

            if (Configuration::get('REES46_LOG_STATUS')) {
                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    Logger::addLog(
                        'REES46: export xml',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                } else {
                    PrestaShopLogger::addLog(
                        'REES46: export xml',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                }
            }
        } else {
            Configuration::updateValue('REES46_ACTION_XML', true);
            Configuration::updateValue('REES46_STORE_KEY', $curl_data['store_key']);
            Configuration::updateValue('REES46_SECRET_KEY', $curl_data['store_secret']);

            $json['success'] = array(
                $this->l('Product feed successfully exported to REES46.'),
            );
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopOrders()
    {
        $json = array();

        $curl_data = array();

        if (Configuration::get('REES46_STORE_KEY') != '') {
            $curl_data['shop_id'] = Configuration::get('REES46_STORE_KEY');
        } else {
            $json['error'] = $this->l('Incorrect value for Store Key field.');
        }

        if (Configuration::get('REES46_SECRET_KEY') != '') {
            $curl_data['shop_secret'] = Configuration::get('REES46_SECRET_KEY');
        } else {
            $json['error'] = $this->l('Incorrect value for Secret Key field.');
        }

        if (isset($json['error'])) {
            die(Tools::jsonEncode($json));
        }

        $next = (int)Tools::getValue('next');

        $limit = 1000;

        $filter_data = array(
            'start' => ($next - 1) * $limit,
            'limit' => $limit,
        );

        if ($filter_data['start'] < 0) {
            $filter_data['start'] = 0;
        }

        $results_total = (int)$this->getTotalOrders();

        $results = $this->getOrders($filter_data);

        $export_data = array();

        if (!empty($results)) {
            foreach ($results as $result) {
                $order_products = array();

                $products = $this->getOrderProducts($result['id_order']);

                foreach ($products as $product) {
                    $categories = array();

                    $categories = Product::getProductCategories((int)$product['product_id']);

                    $order_products[] = array(
                        'id' => $product['product_id'],
                        'price' => $product['total_price_tax_incl'],
                        'categories' => $categories,
                        'is_available' => $product['quantity'],
                        'amount' => $product['product_quantity'],
                    );
                }

                $export_data[] = array(
                    'id' => $result['id_order'],
                    'user_id' => $result['id_customer'],
                    'user_email' => $result['email'],
                    'date' => strtotime($result['date_add']),
                    'items' => $order_products,
                );
            }

            $curl_data['orders'] = $export_data;

            $return = $this->curl('POST', 'http://api.rees46.com/import/orders', Tools::jsonEncode($curl_data));
 
            if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                $json['error'] = $this->l('Could not export orders.');

                if (Configuration::get('REES46_LOG_STATUS')) {
                    if (version_compare(_PS_VERSION_, '1.6', '<')) {
                        Logger::addLog(
                            'REES46: export orders (' . $results_total . ')',
                            3,
                            $return['info']['http_code'],
                            null,
                            null,
                            true
                        );
                    } else {
                        PrestaShopLogger::addLog(
                            'REES46: export orders (' . $results_total . ')',
                            3,
                            $return['info']['http_code'],
                            null,
                            null,
                            true
                        );
                    }
                }
            } else {
                Configuration::updateValue('REES46_ACTION_ORDER', true);

                if ($results_total > $next * $limit) {
                    $json['next'] = $next + 1;

                    $json['success'] = sprintf(
                        $this->l('%s out of %s orders successfully exported to REES46.'),
                        $next * $limit,
                        $results_total
                    );
                } else {
                    $json['success'] = sprintf(
                        $this->l('%s orders successfully exported to REES46.'),
                        $results_total
                    );
                }
            }
        } else {
            $json['error'] = $this->l('No available orders for export.');
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopCustomers()
    {
        $json = array();

        $curl_data = array();

        if (Configuration::get('REES46_STORE_KEY') != '') {
            $curl_data['shop_id'] = Configuration::get('REES46_STORE_KEY');
        } else {
            $json['error'] = $this->l('Incorrect value for Store Key field.');
        }

        if (Configuration::get('REES46_SECRET_KEY') != '') {
            $curl_data['shop_secret'] = Configuration::get('REES46_SECRET_KEY');
        } else {
            $json['error'] = $this->l('Incorrect value for Secret Key field.');
        }

        if (isset($json['error'])) {
            die(Tools::jsonEncode($json));
        }

        $next = (int)Tools::getValue('next');

        $limit = 1000;

        $filter_data = array(
            'start' => ($next - 1) * $limit,
            'limit' => $limit,
        );

        if ($filter_data['start'] < 0) {
            $filter_data['start'] = 0;
        }

        $results_total = (int)$this->getTotalCustomers();

        $results = $this->getCustomers($filter_data);

        $export_data = array();

        if (!empty($results)) {
            foreach ($results as $result) {
                $export_data[] = array(
                    'id' => $result['id_customer'],
                    'email' => $result['email'],
                );
            }

            $curl_data['audience'] = $export_data;

            $return = $this->curl('POST', 'http://api.rees46.com/import/audience', Tools::jsonEncode($curl_data));

            if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                $json['error'] = $this->l('Could not export customers.');

                if (Configuration::get('REES46_LOG_STATUS')) {
                    if (version_compare(_PS_VERSION_, '1.6', '<')) {
                        Logger::addLog(
                            'REES46: export customers (' . $results_total . ')',
                            3,
                            $return['info']['http_code'],
                            null,
                            null,
                            true
                        );
                    } else {
                        PrestaShopLogger::addLog(
                            'REES46: export customers (' . $results_total . ')',
                            3,
                            $return['info']['http_code'],
                            null,
                            null,
                            true
                        );
                    }
                }
            } else {
                Configuration::updateValue('REES46_ACTION_CUSTOMER', true);

                if ($results_total > $next * $limit) {
                    $json['next'] = $next + 1;

                    $json['success'] = sprintf(
                        $this->l('%s out of %s customers successfully exported to REES46.'),
                        $next * $limit,
                        $results_total
                    );
                } else {
                    $json['success'] = sprintf(
                        $this->l('%s customers successfully exported to REES46.'),
                        $results_total
                    );
                }
            }
        } else {
            $json['error'] = $this->l('No available customers for export.');
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopFiles()
    {
        $json = array();

        $dir = _PS_ROOT_DIR_ . '/';

        $files = array(
            'manifest.json',
            'push_sw.js'
        );

        foreach ($files as $key => $file) {
            if (!is_file($dir . $file)) {
                $ch = curl_init();

                $url = 'https://raw.githubusercontent.com/rees46/web-push-files/master/' . $file;

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                $info = curl_getinfo($ch);

                curl_close($ch);

                if ($info['http_code'] < 200 || $info['http_code'] >= 300) {
                    if (Configuration::get('REES46_LOG_STATUS')) {
                        if (version_compare(_PS_VERSION_, '1.6', '<')) {
                            Logger::addLog(
                                'REES46: could not load ' . $file,
                                3,
                                $info['http_code'],
                                null,
                                null,
                                true
                            );
                        } else {
                            PrestaShopLogger::addLog(
                                'REES46: could not load ' . $file,
                                3,
                                $info['http_code'],
                                null,
                                null,
                                true
                            );
                        }
                    }
                } else {
                    file_put_contents($dir . $file, $result);
                }
            }

            if (is_file($dir . $file)) {
                if ($file == 'manifest.json') {
                    Configuration::updateValue('REES46_ACTION_FILE1', true);
                } elseif ($file == 'push_sw.js') {
                    Configuration::updateValue('REES46_ACTION_FILE2', true);
                }

                $json['success'][$key] = sprintf($this->l('%s successfully loaded.'), $file);
            } else {
                $json['error'][$key] = sprintf($this->l('Could not load %s.'), $file);
            }
        }

        die(Tools::jsonEncode($json));
    }

    protected function rees46ShopFinish()
    {
        $url = 'https://rees46.com/api/customers/login';
        $api_key = Configuration::get('REES46_API_KEY');
        $api_secret = Configuration::get('REES46_API_SECRET');

        $json  = '<form action="' . $url . '" method="post" id="submitShopFinish" target="_blank">';
        $json .= '<input type="hidden" name="api_key" value="' . $api_key . '">';
        $json .= '<input type="hidden" name="api_secret" value="' . $api_secret . '">';
        $json .= '</form>';

        die(Tools::jsonEncode($json));
    }

    protected function getTotalOrders()
    {
        $query = new DbQuery();
        $query->select('COUNT(*) AS total');
        $query->from('orders', 'o');
        $query->where('DATE(o.`date_add`) > DATE_SUB(NOW(), INTERVAL 6 MONTH)');

        if (Context::getContext()->cookie->shopContext) {
            $query->where('o.`id_shop` = ' . (int)Context::getContext()->shop->id);
        }

        $rees46_statuses = array();

        $rees46_order_created = Tools::jsonDecode(Configuration::get('REES46_ORDER_CREATED'), true);
        $rees46_order_completed = Tools::jsonDecode(Configuration::get('REES46_ORDER_COMPLETED'), true);
        $rees46_order_cancelled = Tools::jsonDecode(Configuration::get('REES46_ORDER_CANCELLED'), true);

        if ($rees46_order_created) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_created);
        }

        if ($rees46_order_completed) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_completed);
        }

        if ($rees46_order_cancelled) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_cancelled);
        }

        $rees46_statuses = array_unique($rees46_statuses);

        if (!empty($rees46_statuses)) {
            $implode = array();

            foreach ($rees46_statuses as $order_status_id) {
                $implode[] = "o.`current_state` = '" . (int)$order_status_id . "'";
            }

            if ($implode) {
                $query->where(implode(' OR ', $implode));
            }
        }

        $query->orderBy('o.`id_order` ASC');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());

        return $result[0]['total'];
    }

    protected function getOrders($data = array())
    {
        $query = new DbQuery();
        $query->select('o.`id_order`, o.`id_customer`, c.`email`, o.`current_state`, o.`date_add`');
        $query->from('orders', 'o');
        $query->leftJoin('customer', 'c', 'c.`id_customer` = o.`id_customer`');
        $query->where('DATE(o.`date_add`) > DATE_SUB(NOW(), INTERVAL 6 MONTH)');

        if (Context::getContext()->cookie->shopContext) {
            $query->where('o.`id_shop` = ' . (int)Context::getContext()->shop->id);
        }

        $rees46_statuses = array();

        $rees46_order_created = Tools::jsonDecode(Configuration::get('REES46_ORDER_CREATED'), true);
        $rees46_order_completed = Tools::jsonDecode(Configuration::get('REES46_ORDER_COMPLETED'), true);
        $rees46_order_cancelled = Tools::jsonDecode(Configuration::get('REES46_ORDER_CANCELLED'), true);

        if ($rees46_order_created) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_created);
        }

        if ($rees46_order_completed) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_completed);
        }

        if ($rees46_order_cancelled) {
            $rees46_statuses = array_merge($rees46_statuses, $rees46_order_cancelled);
        }

        $rees46_statuses = array_unique($rees46_statuses);

        if (!empty($rees46_statuses)) {
            $implode = array();

            foreach ($rees46_statuses as $order_status_id) {
                $implode[] = "o.`current_state` = '" . (int)$order_status_id . "'";
            }

            if ($implode) {
                $query->where(implode(' OR ', $implode));
            }
        }

        $query->orderBy('o.`id_order` ASC');

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $query->limit((int)$data['limit'], (int)$data['start']);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
    }

    protected function getOrderProducts($id_order)
    {
        $query = new DbQuery();
        $query->select('od.`product_id`, od.`total_price_tax_incl`, od.`product_quantity`, sa.`quantity`');
        $query->from('order_detail', 'od');
        $query->leftJoin('product_shop', 'ps', 'ps.`id_product` = od.`product_id`');
        $query->leftJoin('stock_available', 'sa', 'sa.`id_product` = od.`product_id`');
        $query->where('od.`id_order` = ' . (int)$id_order);
        $query->where('ps.`id_shop` = od.`id_shop`');
        $query->where('sa.`id_product_attribute` = od.`product_attribute_id`');

        if (Context::getContext()->cookie->shopContext) {
            $query->where('od.`id_shop` = ' . (int)Context::getContext()->shop->id);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
    }

    protected function getTotalCustomers()
    {
        $query = new DbQuery();
        $query->select('COUNT(*) AS total');
        $query->from('customer', 'c');

        if (Context::getContext()->cookie->shopContext) {
            $query->where('c.`id_shop` = ' . (int)Context::getContext()->shop->id);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());

        return $result[0]['total'];
    }

    protected function getCustomers($data = array())
    {
        $query = new DbQuery();
        $query->select('c.`id_customer`, c.`email`');
        $query->from('customer', 'c');

        if (Context::getContext()->cookie->shopContext) {
            $query->where('c.`id_shop` = ' . (int)Context::getContext()->shop->id);
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $query->limit((int)$data['limit'], (int)$data['start']);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
    }

    public function curl($type, $url, $params = null)
    {
        $curl_data = array();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $curl_data['result'] = curl_exec($ch);
        $curl_data['info'] = curl_getinfo($ch);

        curl_close($ch);

        return $curl_data;
    }
}
