<script type="text/javascript">
  $(function(){
    REES46.addReadyListener(function() {
      REES46.pushData('view', {
        item_id: '{$productId}',
        price: '{$productPrice}',
        is_available: '{$isAvailable}',
        categories: new Array('{$category->id}'),
        name: "{$productName}",
        description: "{$productDescription}",
        url: "{$productLink}",
        image_url: "{$productImageLink}"
      });
    });
  })
</script>
