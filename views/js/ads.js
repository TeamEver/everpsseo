$(document).ready(function() {
  if (typeof prestashop !== 'undefined') {
    prestashop.on('updateCart', function(event) {
      var eventDatas = {};
      if (event && event.reason && event.reason.linkAction && event.reason.linkAction == 'add-to-cart') {
        var products = event.reason.cart.products;
        var cartValue = 0;
        for (var i = 0; i < products.length; i++) {
          trackAddToCart(products[i].price);
        }
      }
    });
  }
});

// Fonction de suivi d'ajout au panier
function trackAddToCart(value) {
  // Appeler la fonction de suivi de conversion de Google Ads
  gtag('event', 'conversion', {'send_to': adwords_add_to_cart,
    'value': value,
    'currency': ever_currency_sign // Remplacez par la devise appropriée
  });
}
