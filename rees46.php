<?php
/**
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
*/

if (!defined('_PS_VERSION_'))
	exit;
function_exists('curl_init');

class Rees46 extends Module
{
	private $hooks = array('displayHeader', 'displayProductButtons', 'actionValidateOrder',
												'displayHome', 'displayTop', 'displayFooterProduct', 'displayShoppingCartFooter');

	public function __construct()
	{
		$this->name = 'rees46';
		$this->tab = 'front_office_features';
		$this->version = '1.0.4';
		$this->author = 'Servilovskiy Sergey <sergey.servilovsky@mkechinov.ru>';
		$this->need_instance = 0;
		$this->module_key = 'b62df9df084ba63e7aa2ef146fe85c84';

		parent::__construct();

		$this->displayName = $this->l('REES46');
		$this->description = $this->l('Система рекомендаций для вашего магазина');
		$this->context->smarty->assign('module_name', $this->name);
	}

	public function registerHooks()
	{
		foreach ($this->hooks as $hook)
		{
			if (!$this->registerHook($hook))
			{
				$this->_errors[] = "Failed to install hook '$hook'<br />\n";
				return false;
			}
		}
		return true;
	}

	public function unregisterHooks()
	{
		foreach ($this->hooks as $hook)
		{
			if (!$this->unregisterHook($hook))
			{
				$this->_errors[] = "Failed to uninstall hook '$hook'<br />\n";
				return false;
			}
		}
		return true;
	}

	public function install()
	{
		return (parent::install()
			&& $this->registerHooks()
			&& Configuration::updateValue('home_page_popular', 1)
			&& Configuration::updateValue('category_page_popular', 1)
			&& Configuration::updateValue('category_page_recently_viewed', 1)
			&& Configuration::updateValue('category_page_interesting', 1)
			&& Configuration::updateValue('product_page_also_bought', 1)
			&& Configuration::updateValue('product_page_similar', 1)
			&& Configuration::updateValue('product_page_ineresting', 1)
			&& Configuration::updateValue('cart_page_see_also', 1)
		);
	}

	public function uninstall()
	{
		return (parent::uninstall()
			&& $this->unregisterHooks()
		);
	}

	public function hookDisplayHome()
	{
		if (Configuration::get('home_page_popular') == 1)
			return $this->display(__FILE__, 'views/templates/hook/home_popular_recommender.tpl');
	}

	public function hookDisplayHeader()
	{
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
			if ($id_product = (int)Tools::getValue('id_product'))
			{
				$product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
				$this->context->smarty->assign(array(
					'category_id' => $product->id_category_default
				));
			}
			else
				$this->context->smarty->assign(array(
					'category_id' => null
				));

		if ($id_cart = (int)$this->context->cookie->id_cart)
		{
			$cart = new Cart($id_cart);
			$products = $cart->getProducts();
			$p_ids = array();
			foreach ($products as $prod)
				$p_ids[] = $prod['id_product'];
			$this->context->smarty->assign(array(
				'items_in_cart_ids' => $p_ids
			));
		}
		else
			$this->context->smarty->assign(array(
				'items_in_cart_ids' => array()
			));

		if ($this->context->customer->isLogged())
			$this->context->smarty->assign(array(
				'user_id' => (int)$this->context->cookie->id_customer,
				'user_email' => $this->context->cookie->email
			));
		else
			$this->context->smarty->assign(array(
				'user_id' => null,
				'user_email' => null
			));
		$this->context->smarty->assign(array(
			'rees46_shop_id' => Configuration::get('MOD_REES46_SHOP_ID'),
			'rees46_secret_key' => Configuration::get('MOD_REES46_SECRET_KEY')
		));
		return $this->display(__FILE__, 'views/templates/hook/init_rees46.tpl');
	}

	public function hookDisplayProductButtons($params)
	{
		$link = new Link();
		$product = $params['product'];
		$is_available = 1;
		if ($product->quantity == 0)
			$is_available = 0;
		$arr_name = array_values($product->name);
		$name = $arr_name[0];
		$arr_description = array_values($product->description);
		$description = $arr_description[0];

		$product_link = $link->getProductLink($product);

		$cover = Product::getCover($product->id);
		$rewrite = $product->link_rewrite;
		if (is_array($rewrite))
			$rewrite = reset($rewrite);
		$img_link = $link->getImageLink($rewrite, $cover['id_image']);
		if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
			$img_link = 'https://'.$img_link;
		else
			$img_link = 'http://'.$img_link;

		$this->context->smarty->assign(array(
			'productPrice' => $product->getPrice(!Tax::excludeTaxeOption()),
			'isAvailable' => $is_available,
			'productId' => $product->id,
			'productName' => $name,
			'productDescription' => $description,
			'productLink' => $product_link,
			'productImageLink' => $img_link,
		));

		return $this->display(__FILE__, 'views/templates/hook/product_page.tpl');
	}

	public function hookDisplayFooterProduct()
	{
		return $this->display(__FILE__, 'views/templates/hook/product_page_recommender.tpl');
	}

	public function hookDisplayTop()
	{
		return $this->display(__FILE__, 'views/templates/hook/category_page_recommender.tpl');
	}

	public function hookDisplayShoppingCartFooter()
	{
		if (Configuration::get('cart_page_see_also') == 1)
			return $this->display(__FILE__, 'views/templates/hook/cart_page_recommender.tpl');
	}

	public function hookActionValidateOrder($params)
	{
		$order_id = $params['order']->id;
		$product_info = array();
		foreach ($params['order']->product_list as $order_product)
		{
			$item = array();
			$item['item_id'] = $order_product['id_product'];
			$item['amount'] = $order_product['cart_quantity'];
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

			$cover = Product::getCover($product->id);
			$rewrite = $product->link_rewrite;
			if (is_array($rewrite))
				$rewrite = reset($rewrite);
			$img_link = $link->getImageLink($rewrite, $cover['id_image']);
			if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
				$img_link = 'https://'.$img_link;
			else
				$img_link = 'http://'.$img_link;
			$item['image_url'] = $img_link;
			$product_info[] = $item;
		}
		$cookie_info = array();
		$cookie_info['items'] = $product_info;
		$cookie_info['order_id'] = $order_id;
		setcookie( 'rees46_track_purchase', Tools::jsonEncode($cookie_info), 0, '/' );
	}

	public function getContent()
	{
		$message = '';

		if (Tools::isSubmit('submit_'.$this->name))
			$message = $this->saveContent();
		// Выгрузка истории заказов
		if (Tools::isSubmit('submit_unloading_orders'))
		{
			$shop_id = Configuration::get('MOD_REES46_SHOP_ID');
			$shop_secret = Configuration::get('MOD_REES46_SECRET_KEY');
			if (($shop_id == '') || ($shop_secret == ''))
				$message = $this->displayError(
					$this->l('Для выгрузки заказов введите код и секретный ключ
										вашего магазина в настройках модуля.')
				);
			else
			{
				$sql = '
					SELECT o.`id_order`, o.`id_customer`, o.`date_add`
					FROM `'._DB_PREFIX_.'orders` o
					WHERE date_add >= \''.date('Y-m-d H:i:s', strtotime('-6 month')).'\'
					LIMIT 300
				';
				$res_orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				if (!$res_orders)
					$message = $this->displayError(
						$this->l('В магазине не нашлось ни одного заказа за последние 6 месяцев.')
					);
				else
				{
					$processed_orders = array();

					foreach ($res_orders as $o)
					{
						$order = new Order((int)$o['id_order']);
						$order_products = $order->getProducts();
						$items_formatted_info = array();
						foreach ($order_products as $p)
						{
							$product = new Product((int)$p['product_id']);
							$product_formatted = array(
								'id' =>  $p['product_id'],
								'price' => $product->getPrice(!Tax::excludeTaxeOption()),
								'categories' => $product->getCategories(),
								'amount' => $p['product_quantity']
							);

							array_push($items_formatted_info, $product_formatted);
						}

						$customer = new Customer((int)$o['id_customer']);
						$order_formatted_info = array(
							'id' => $o['id_order'],
							'user_id' => $o['id_customer'],
							'user_email' => $customer->email,
							'date' => strtotime($o['date_add']),
							'items' => $items_formatted_info
						);

						array_push($processed_orders, $order_formatted_info);
					}

					$result = array(
						'shop_id' => $shop_id,
						'shop_secret' => $shop_secret,
						'orders' => $processed_orders
					);

					$context = stream_context_create(array(
						'http' => array(
							'method' => 'POST',
							'header' => "Content-Type: application/json\r\n",
							'content' => Tools::jsonEncode($result)
						)
					));

					// Send the request
					$response = Tools::file_get_contents('http://api.rees46.com/import/orders.json', false, $context);

					// Check for errors
					if ($response === false)
						$message = $this->displayError($this->l('Данные не отправлены'));

					$message = $this->displayConfirmation($this->l('Выгрузка заказов в REES46 успешно инициирована.'));
				}
			}
		}
		if (Tools::isSubmit('submit_settings'))
		{
			Configuration::updateValue('home_page_popular', (Tools::getValue('home_page_popular') == 'on' ? 1 : 0));
			Configuration::updateValue('category_page_popular', (Tools::getValue('category_page_popular') == 'on' ? 1 : 0));
			Configuration::updateValue('category_page_recently_viewed', (Tools::getValue('category_page_recently_viewed') == 'on' ? 1 : 0));
			Configuration::updateValue('category_page_interesting', (Tools::getValue('category_page_interesting') == 'on' ? 1 : 0));
			Configuration::updateValue('product_page_also_bought', (Tools::getValue('product_page_also_bought') == 'on' ? 1 : 0));
			Configuration::updateValue('product_page_similar', (Tools::getValue('product_page_similar') == 'on' ? 1 : 0));
			Configuration::updateValue('product_page_ineresting', (Tools::getValue('product_page_ineresting') == 'on' ? 1 : 0));
			Configuration::updateValue('cart_page_see_also', (Tools::getValue('cart_page_see_also') == 'on' ? 1 : 0));

			$message = $this->displayConfirmation($this->l('Ваши настройки сохранены'));
		}
		$this->displayContent($message);

		$checked_for_home_page_popular = (Configuration::get('home_page_popular') == 1 ? 'checked="checked"' : '');
		$checked_for_category_page_popular = (Configuration::get('category_page_popular') == 1 ? 'checked="checked"' : '');
		$checked_for_category_page_recently_viewed = (Configuration::get('category_page_recently_viewed') == 1 ? 'checked="checked"' : '');
		$checked_for_category_page_interesting = (Configuration::get('category_page_interesting') == 1 ? 'checked="checked"' : '');
		$checked_for_product_page_also_bought = (Configuration::get('product_page_also_bought') == 1 ? 'checked="checked"' : '');
		$checked_for_product_page_similar = (Configuration::get('product_page_similar') == 1 ? 'checked="checked"' : '');
		$checked_for_product_page_ineresting = (Configuration::get('product_page_ineresting') == 1 ? 'checked="checked"' : '');
		$checked_for_cart_page_see_also = (Configuration::get('cart_page_see_also') == 1 ? 'checked="checked"' : '');

		$this->context->smarty->assign(array(
			'checked_for_home_page_popular' => $checked_for_home_page_popular,
			'checked_for_category_page_popular' => $checked_for_category_page_popular,
			'checked_for_category_page_recently_viewed' => $checked_for_category_page_recently_viewed,
			'checked_for_category_page_interesting' => $checked_for_category_page_interesting,
			'checked_for_product_page_also_bought' => $checked_for_product_page_also_bought,
			'checked_for_product_page_similar' => $checked_for_product_page_similar,
			'checked_for_product_page_ineresting' => $checked_for_product_page_ineresting,
			'checked_for_cart_page_see_also' => $checked_for_cart_page_see_also
		));

		return $this->display(__FILE__, 'views/templates/admin/settings.tpl');
	}

	private function saveContent()
	{
		$message = '';

		if (Configuration::updateValue('MOD_REES46_SHOP_ID', Tools::getValue('MOD_REES46_SHOP_ID')) &&
			Configuration::updateValue('MOD_REES46_SECRET_KEY', Tools::getValue('MOD_REES46_SECRET_KEY')))
			$message = $this->displayConfirmation($this->l('Ваши настройки сохранены'));
		else
			$message = $this->displayError($this->l('Были ошибки во время сохранения'));

		return $message;
	}

	private function displayContent($message)
	{
		$this->context->smarty->assign(array(
			'message' => $message,
			'MOD_REES46_SHOP_ID' => Configuration::get('MOD_REES46_SHOP_ID'),
			'MOD_REES46_SECRET_KEY' => Configuration::get('MOD_REES46_SECRET_KEY'),
		));
	}
}
