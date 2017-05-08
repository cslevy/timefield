/**
 * @file
 * Javascript for Timefield.
 */
(function ($) {
  Drupal.behaviors.timefield = {
    attach: function(context, settings) {
      console.log('here');
      // Iterate over timefield settings, which keyed by input class.
      for (var element in drupalSettings.timefield) {
        // Attach timepicker behavior to each matching element.
        $("input.edit-timefield-timepicker." + element, context).each(function(index) {
          $(this).timepicker(settings.timefield[element]);
        });
      }
    }
  };
})(jQuery);
