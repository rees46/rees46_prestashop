<script type="text/javascript">
  if ('{$item_id}' == '')
    var itemId = null;
  else
    var itemId = '{$item_id}';

  if ('{$category_id}' == '')
    var categoryId = null;
  else
    var categoryId = '{$category_id}';

  var itemsInCartIds = [];
  {foreach $items_in_cart_ids as $id}
    itemsInCartIds.push({$id})
  {/foreach}
  if (itemsInCartIds.length == 0)
    itemsInCartIds = null;

  function initREES46() {
    $(function() {
      REES46.addStyleToPage();

      var shopId = '{$rees46_shop_id}';
      if (shopId != '') {
        var logged = '{$logged}';
        if (logged != '') {
          var userInfo = {
            'id': '{$user_id}',
            'email': '{$user_email}'
          };
          REES46.init(shopId, userInfo);
        } else {
          REES46.init(shopId, null);
        }
      }

      REES46.addReadyListener(function () {
        $('.rees46').each(function() {
          var recommenderBlock = $(this);
          var recommenderType = recommenderBlock.data('type');
          var recommenderTitle = recommenderBlock.data('title');
          var recommenderLimit = recommenderBlock.data('limit');
          if (recommenderLimit == null || recommenderLimit == undefined)
            recommenderLimit = 5;

          var recomend_products_tpl = ''
          if (recommenderType) {
            REES46.recommend({
              recommender_type: recommenderType,
              category: categoryId,
              item: itemId,
              cart: itemsInCartIds,
              limit: recommenderLimit
            }, function(ids) {
              if (ids.length == 0) {
                return;
              }

              $.getJSON('index.php?fc=module&module=rees46&controller=recommenders&product_ids=' + ids.join(','), function(data) {
                var products = data.products;

                $(products).each(function() {
                  if (this.name != '' && this.name != null) {
                    var recommend_url = this.url + recommenderType;
                    recomend_products_tpl += '<div class="recommended-item">' +
                                              '<div class="recommended-item-photo">' +
                                                '<a href="' + recommend_url + '"><img src="' + this.image_url + '" class="item_img" /></a>' +
                                              '</div>' +
                                              '<div class="recommended-item-title">' +
                                                '<a href="' + recommend_url + '">' + this.name + '</a>' +
                                              '</div>' +
                                              '<div class="recommended-item-price">' +
                                                this.price + ' ' + REES46.currency +
                                              '</div>' +
                                              '<div class="recommended-item-action">' +
                                                '<a href="' + recommend_url + '">Подробнее</a>'+
                                              '</div>'+
                                             '</div>';
                  }
                });

                var recommender_titles = {
                  interesting: 'Вам это будет интересно',
                  also_bought: 'С этим также покупают',
                  similar: 'Похожие товары',
                  popular: 'Популярные товары',
                  see_also: 'Посмотрите также',
                  recently_viewed: 'Вы недавно смотрели'
                };

                if (recommenderTitle == null || recommenderTitle == undefined)
                  recommenderTitle = recommender_titles[recommenderType]
                if (recomend_products_tpl != '') {
                  template = '<div class="recommender-block-title">' + recommenderTitle + '</div><div class="recommended-items">' + recomend_products_tpl + '</div>'

                  if (REES46.showPromotion) {
                    template = template + REES46.getPromotionBlock();
                  }

                  recommenderBlock.html(template);
                }
              });
            });
          }
        });
      });
    });

    $(document).ajaxSend(function(event, jqxhr, settings) {
      var data = settings.data;
      if (data){
        var arr_data = data.split('&');
        var controller = null;
        var method_add = false;
        var method_delete = false;
        var product_id = null;
        var rating = null;
        $.each(arr_data, function(idx, param) {
          if (param.indexOf("controller") == 0) {
            var arr_param = param.split('=');
            controller = arr_param[1];
          }
          if (param.indexOf("id_product") == 0) {
            var arr_param = param.split('=');
            product_id = arr_param[1];
          }
          if (param.indexOf("add") == 0) {
            var arr_param = param.split('=');
            if (arr_param[0] == 'add' && (arr_param[1] == '1' || arr_param[1] == 'true'))
              method_add = true;
          }
          if (param.indexOf("delete") == 0) {
            var arr_param = param.split('=');
            if (arr_param[0] == 'delete' && (arr_param[1] == '1' || arr_param[1] == 'true'))
              method_delete = true;
          }
          if (param.indexOf("criterion") == 0) {
            var arr_param = param.split('=');
            rating = arr_param[1];
          }
        });
        if (controller == 'cart' && method_add == true && product_id != null) {
          REES46.addReadyListener(function() {
            REES46.pushData('cart', {
              item_id: product_id
            });
          });
        }
        if (controller == 'cart' && method_delete == true && product_id != null) {
          REES46.addReadyListener(function() {
            REES46.pushData('remove_from_cart', {
              item_id: product_id
            });
          });
        }
      }
    });
  };

  var script = document.createElement('script');
  script.src = '//cdn.rees46.com/rees46_script2.js';
  script.async = true;
  script.onload = function() {
    initREES46();
    {if $page_name == 'product'}
      viewProductREES46();
    {/if}
  };
  document.getElementsByTagName('head')[0].appendChild(script);
</script>
