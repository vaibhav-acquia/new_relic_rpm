(function (Drupal, drupalSettings) {
  Drupal.behaviors.NRRumFooter = {
    attach: function (context, settings) {
      once('NRRumFooterInsert', 'html').forEach(function (element) {
        eval(drupalSettings.rum_footer.markup);
      })
    }
  };
})(Drupal, drupalSettings);
