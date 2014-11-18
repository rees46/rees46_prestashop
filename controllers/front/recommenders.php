<?php
/**
* 2007-2014 PrestaShop
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
*  @copyright 2007-2014 PrestaShop SA
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

		foreach ($ids as $id)
		{
			$product = new Product($id, false);
			$link = new Link();
			$product_link = $link->getProductLink($product);
			$query = parse_url($product_link, PHP_URL_QUERY);

			if ($query)
				$recommend_product_link = $product_link.'&recommended_by=';
			else
				$recommend_product_link = $product_link.'?recommended_by=';
			$cover = Product::getCover($product->id);
			$rewrite = $product->link_rewrite;
			if (is_array($rewrite))
				$rewrite = reset($rewrite);
			$img_link = $link->getImageLink($rewrite, $cover['id_image']);
			if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
				$img_link = 'https://'.$img_link;
			else
				$img_link = 'http://'.$img_link;
			$arr_name = array_values($product->name);
			$p = Array(
				'name' => $arr_name[0],
				'url' => $recommend_product_link,
				'price' => $product->getPrice(!Tax::excludeTaxeOption(), null, 2),
				'image_url' => $img_link
			);
			array_push($products, $p);
		}

		header('Content-Type: application/json');
		die(Tools::jsonEncode(Array('products' => $products)));
	}
}
