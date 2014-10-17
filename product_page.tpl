<script type="text/javascript">
  $(function(){
    var priceDisplay = '{$priceDisplay}';
    var productQuantity = '{$product->quantity}';

    if (priceDisplay == '' || priceDisplay == '0')
      var productPrice = '{$product->getPrice(true)}';
    else
      var productPrice = '{$product->getPrice(false)}';

    if (productQuantity == '0')
      var isAvailable = productQuantity;
    else
      var isAvailable = '1';
    REES46.addReadyListener(function() {
      REES46.pushData('view', {
        item_id: '{$product->id}',
        price: productPrice,
        is_available: isAvailable,
        categories: new Array('{$category->id}'),
        name: "{$product->name|escape:'html':'UTF-8'}",
        description: "{$product->description|escape:'html':'UTF-8'}",
        url: "{$link->getProductLink($product)}",
        image_url: "{$link->getImageLink($product->link_rewrite, $cover.id_image, 'home_default')|escape:'html':'UTF-8'}"
      });
    });
  })
</script>
