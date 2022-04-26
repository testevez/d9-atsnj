/**
* @file
*/

(function ($, Drupal) {

    'use strict';

    /**
     * @type {Drupal~behavior}
     */
    Drupal.behaviors.landscapeAdsBlock = {
        attach: function (context, settings) {

            function landscapeAdsBlockSlideShow() {
                var showing = $('#landscape-ad-block-wrap .is-showing');
                var next = showing.next().length ? showing.next() : showing.parent().children(':first');
                var interval = drupalSettings.atsnj_ads.landscapeAdsBlock.interval;
                showing.fadeOut(800, function() {next.fadeIn(800).addClass('is-showing');}).removeClass('is-showing');
                setTimeout(landscapeAdsBlockSlideShow, interval);
            }
            landscapeAdsBlockSlideShow();
        }
    };

})(jQuery, Drupal);
