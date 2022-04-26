/**
 * @file
 */

(function ($, Drupal) {

    'use strict';

    /**
     * @type {Drupal~behavior}
     */
    Drupal.behaviors.atsnj_media_modal = {
        attach: function (context, settings) {

            $('body', context).once('openModal').each(function () {
                jQuery('a.use-ajax').click()
            });

        }
    };

})(jQuery, Drupal);
