(function ($, Drupal) {
  'use strict';
  
  Drupal.behaviors.ocbotScenario = {
    attach: function (context) {
      // Динамічна логіка для полів
      $('#edit-field-ocbot-scenario-description', context).once('ocbot').each(function () {
        console.log('Field initialized');
      });
    }
  };
})(jQuery, Drupal);