<?php
if (!defined('_PS_VERSION_'))
  exit;

class Rees46RecommendersModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    $ids = array_map('intval', explode(',', $_REQUEST['product_ids']));
    $products = array();

    foreach ($ids as $id) {
      $product = new Product($id, false);
      $link = new Link();
      $product_link = $link->getProductLink($product);
      $query = parse_url($product_link, PHP_URL_QUERY);

      if( $query ) {
          $recommend_product_link = $product_link.'&recommended_by=';
      }
      else {
          $recommend_product_link = $product_link.'?recommended_by=';
      }
      $img_link = $link->getImageLink($product->link_rewrite, Product::getCover($product->id)['id_image'], 'home_default');
      $parsed_link = parse_url($img_link);
      if (array_key_exists('query', $parsed_link))
        $img_path = $parsed_link['path'] + '?' + $parsed_link['query'];
      else
        $img_path = $parsed_link['path'];

      $p = Array(
        'name' => array_values($product->name)[0],
        'url' => $recommend_product_link,
        'price' => $product->getPrice(!Tax::excludeTaxeOption(), null, 2),
        'image_url' => $img_path
      );
      array_push($products, $p);
    }

    header('Content-Type: application/json');
    die(json_encode(Array('products' => $products)));
  }
}
