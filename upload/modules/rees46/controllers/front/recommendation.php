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

use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class Rees46RecommendationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('ajax') && Tools::getValue('module_id') && Tools::getValue('product_ids')) {
            die($this->getProducts(Tools::getValue('module_id'), Tools::getValue('product_ids')));
        }
    }

    protected function getProducts($module_id, $ids)
    {
        $products = array();

        $product_ids = explode(',', $ids);

        if (!empty($product_ids)) {
            $module_values = Tools::jsonDecode(Configuration::get('REES46_MODULE_' . $module_id), true);

            if ($module_values['title'][$this->context->language->id] == '') {
                $title = $this->module->l($this->module->recommends[$module_values['type']]);
            } else {
                $title = $module_values['title'][$this->context->language->id];
            }

            if ($module_values['template'] == 'product-list') {
                $this->context->smarty->assign(
                    array(
                        'page_name' => 'index',
                    )
                );

                $template = 'custom';
            } else {
                $template = $module_values['template'];
            }

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $dir = '15/';
            } elseif (version_compare(_PS_VERSION_, '1.7', '<')) {
                $dir = '16/';
            } else {
                $dir = '';
            }

            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $template_file = 'views/templates/front/' . $dir . 'recommendation_' . $template . '.tpl';
            } else {
                $template_file = 'module:rees46/views/templates/front/' . $dir . 'recommendation_' . $template . '.tpl';
            }

            $cache_id = 'rees46|' . $module_id . '|' . $template . '|' . implode('|', $product_ids);

            if (!$this->context->smarty->isCached($template_file, $this->context->smarty->getCacheId($cache_id))) {
                if (version_compare(_PS_VERSION_, '1.7', '<')) {
                    foreach ($product_ids as $product_id) {
                        $product = new Product(
                            (int)$product_id,
                            true,
                            $this->context->language->id,
                            $this->context->shop->id
                        );

                        $image = Product::getCover($product->id);

                        if ($product->name != null && $product->active && $product->available_for_order) {
                            $link = $this->context->link->getProductLink(
                                (int)$product->id,
                                $product->link_rewrite,
                                $product->category,
                                $product->ean13,
                                $this->context->language->id,
                                $this->context->shop->id,
                                0,
                                false,
                                false,
                                false
                            );

                            if (parse_url($link, PHP_URL_QUERY)) {
                                $link = $link . '&recommended_by=' . $module_values['type'];
                            } else {
                                $link = $link . '?recommended_by=' . $module_values['type'];
                            }

                            $products[] = array(
                                'id_product' => $product->id,
                                'name' => $product->name,
                                'link' => $link,
                                'show_price' => $product->show_price,
                                'link_rewrite' => $product->link_rewrite,
                                'price' => $product->getPrice(!Tax::excludeTaxeOption()),
                                'price_without_reduction' => Product::getPriceStatic((int)$product->id),
                                'id_product_attribute' => Product::getDefaultAttribute($product->id),
                                'customizable' => $product->customizable,
                                'allow_oosp' => Product::isAvailableWhenOutOfStock($product->out_of_stock),
                                'quantity' => $product->quantity,
                                'image' => $this->context->link->getImageLink(
                                    $product->link_rewrite[$this->context->language->id],
                                    $image['id_image'],
                                    $module_values['image_type']
                                ),
                                'id_image' => $image['id_image'],
                                'description_short' => $product->description_short,
                                'available_for_order' => false,
                            );
                        } else {
                            $this->disableProduct($product_id);
                        }
                    }
                } else {
                    foreach ($product_ids as $product_id) {
                        $product = (new ProductDataProvider)->getProduct(
                            (int)$product_id,
                            true,
                            $this->context->language->id,
                            $this->context->shop->id
                        );

                        $id_image = $product->getCover($product_id);

                        $fix_product = new Product(
                            (int)$product_id,
                            true,
                            $this->context->language->id,
                            $this->context->shop->id
                        );

                        $cover = (new ImageRetriever($this->context->link))->getImage(
                            $fix_product,
                            (int)$id_image['id_image']
                        );

                        if ($product->name != null && $product->active && $product->available_for_order) {
                            $url = $this->context->link->getProductLink(
                                (int)$product_id,
                                $product->link_rewrite,
                                $product->category,
                                $product->ean13,
                                $this->context->language->id,
                                $this->context->shop->id,
                                0,
                                false,
                                false,
                                false
                            );

                            if (parse_url($url, PHP_URL_QUERY)) {
                                $url = $url . '&recommended_by=' . $module_values['type'];
                            } else {
                                $url = $url . '?recommended_by=' . $module_values['type'];
                            }

                            $products[] = array(
                                'id_product' => $product_id,
                                'name' => $product->name,
                                'url' => $url,
                                'cover' => $cover,
                                'id_product_attribute' => Product::getDefaultAttribute($product->id),
                                'available_for_order' => (bool)$product->available_for_order,
                                'show_price' => (bool)$product->show_price,
                                'price' => Tools::displayPrice(Tools::convertPrice($product->getPrice(
                                    !Tax::excludeTaxeOption()
                                ))),
                                'online_only' => (bool)$product->online_only,
                                'description_short' => $product->description_short,
                                'main_variants' => false,
                                'has_discount' => false,
                                'flags' => false,
                            );
                        } else {
                            $this->disableProduct($product_id);
                        }
                    }
                }

                if (!empty($products)) {
                    $this->context->smarty->assign(
                        array(
                            'rees46_module_id' => $module_id,
                            'rees46_title' => $title,
                            'rees46_more' => $this->module->l('More'),
                            'rees46_quick' => $this->module->l('Quick view'),
                            'rees46_products' => $products,
                            'rees46_template' => $module_values['template'],
                        )
                    );

                    if (version_compare(_PS_VERSION_, '1.7', '<')) {
                        return $this->module->display(
                            _PS_MODULE_DIR_ . 'rees46/',
                            $template_file,
                            $this->context->smarty->getCacheId($cache_id)
                        );
                    } else {
                        return $this->module->fetch($template_file, $this->context->smarty->getCacheId($cache_id));
                    }
                }
            } else {
                if (version_compare(_PS_VERSION_, '1.7', '<')) {
                    return $this->module->display(
                        _PS_MODULE_DIR_ . 'rees46/',
                        $template_file,
                        $this->context->smarty->getCacheId($cache_id)
                    );
                } else {
                    return $this->module->fetch($template_file, $this->context->smarty->getCacheId($cache_id));
                }
            }
        }
    }

    protected function disableProduct($product_id)
    {
        $curl_data = array();

        $curl_data['shop_id'] = Configuration::get('REES46_STORE_KEY');
        $curl_data['shop_secret'] = Configuration::get('REES46_SECRET_KEY');
        $curl_data['item_ids'] = $product_id;

        $return = $this->module->curl('POST', 'http://api.rees46.com/import/disable', Tools::jsonEncode($curl_data));

        if (Configuration::get('REES46_LOG_STATUS')) {
            if ($return['info']['http_code'] < 200 || $return['info']['http_code'] >= 300) {
                if (version_compare(_PS_VERSION_, '1.6', '<')) {
                    Logger::addLog(
                        'REES46: Excluded of recomended product_id [' . $product_id . ']',
                        3,
                        $return['info']['http_code'],
                        null,
                        null,
                        true
                    );
                } else {
                    PrestaShopLogger::addLog(
                        'REES46: Excluded of recomended product_id [' . $product_id . ']',
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
                        'REES46: Excluded of recomended product_id [' . $product_id . ']',
                        1,
                        null,
                        null,
                        null,
                        true
                    );
                } else {
                    PrestaShopLogger::addLog(
                        'REES46: Excluded of recomended product_id [' . $product_id . ']',
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
