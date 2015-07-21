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

class Rees46RecommendersModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		$ids = array_map('intval', explode(',', $_REQUEST['product_ids']));
		$products = array();
		$disabled_product_ids = array();

		foreach ($ids as $id)
		{
			$product = new Product($id, false);
			if ($product->name == null || !$product->active || !$product->available_for_order)
			{
				array_push($disabled_product_ids, $id);
				continue;
			}
			$link = new Link();
			$cover = Product::getCover($product->id);
			$rewrite = $product->link_rewrite;
			if (is_array($rewrite))
				$rewrite = reset($rewrite);
			if ((bool)Configuration::get('PS_REWRITING_SETTINGS'))
				$product_link = $link->getProductLink((int)$id, $rewrite, $product->category, $product->ean13, null, null, 0, true);
			else
				$product_link = $link->getProductLink($product);
			$query = parse_url($product_link, PHP_URL_QUERY);

			if ($query)
				$recommend_product_link = $product_link.'&recommended_by=';
			else
				$recommend_product_link = $product_link.'?recommended_by=';
			$img_link = $link->getImageLink($rewrite, $cover['id_image']);
			if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
				$img_link = 'https://'.$img_link;
			else
				$img_link = 'http://'.$img_link;
			$arr_name = array_values($product->name);
			$product_price = 0;
			if ($product->getPrice(!Tax::excludeTaxeOption(), null, 2) > 100)
				$product_price = round($product->getPrice(!Tax::excludeTaxeOption(), null, 2));
			else
				$product_price = $product->getPrice(!Tax::excludeTaxeOption(), null, 2);
			$p = Array(
				'name' => $arr_name[0],
				'url' => $recommend_product_link,
				'price' => $product_price,
				'image_url' => $img_link
			);
			array_push($products, $p);
		}
		if (!empty($disabled_product_ids))
		{
			$options = array(
				CURLOPT_URL            => 'http://api.rees46.com/import/disable?
																	shop_id='.Configuration::get('MOD_REES46_SHOP_ID').'&
																	shop_secret='.Configuration::get('MOD_REES46_SECRET_KEY').'&
																	item_ids='.implode(',', $disabled_product_ids),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_ENCODING       => '',
				CURLOPT_USERAGENT      => 'spider',
				CURLOPT_AUTOREFERER    => true,
				CURLOPT_CONNECTTIMEOUT => 2,
				CURLOPT_TIMEOUT        => 2,
				CURLOPT_MAXREDIRS      => 10
			);
			$ch = curl_init();
			curl_setopt_array( $ch, $options );
			curl_exec($ch);
		}

		header('Content-Type: application/json');
		die(Tools::jsonEncode(Array('products' => $products)));
	}
}
