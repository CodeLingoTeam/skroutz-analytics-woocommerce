jQuery(document).ready(function($) {
  var updateLink = function(link, flavor) {
    link.text(flavor);
    link.attr('href', wc_skroutz_analytics.flavors[flavor+'_merchants_url']);
  },
  $link = $('#merchants_link'),
  $flavorDropdown = $('#woocommerce_wc_skroutz_analytics_sa_flavor'),
  updateMerchantLink = function(flavor) { return updateLink($link, flavor); };

  // Initialize
  updateMerchantLink($flavorDropdown.val());

  // Listen for flavor changes
  $flavorDropdown.on('change', function() { updateMerchantLink(this.value); });

  var $customIdCheckbox = $('#woocommerce_wc_skroutz_analytics_sa_items_custom_id_enabled'),
  $customId = $('#woocommerce_wc_skroutz_analytics_sa_items_custom_id').closest('tr'),
  showHideCustomId = function() { $customIdCheckbox.is(':checked') ? $customId.show() : $customId.hide(); };

  var $objectNameCheckbox = $('#woocommerce_wc_skroutz_analytics_sa_custom_global_object_name_enabled'),
  $objectName = $('#woocommerce_wc_skroutz_analytics_sa_custom_global_object_name').closest('tr'),
  showHideGlobalObjectName = function() { $objectNameCheckbox.is(':checked') ? $objectName.show() : $objectName.hide(); };

  // Initialize
  showHideCustomId();
  showHideGlobalObjectName();

  // Listen for checkbox changes
  $customIdCheckbox.on('change', function() { showHideCustomId(); });
  $objectNameCheckbox.on('change', function() { showHideGlobalObjectName(); });
});
