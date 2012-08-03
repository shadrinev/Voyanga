/* =========================================================
 * expiredNotification.js v0.0.1
 * =========================================================
 * Copyright 2012 EasyTrip, LLC.
 *
 * ========================================================= */


!function ($) {

    "use strict"; // jshint ;_;


    /* ExpiredNotification CLASS DEFINITION
     * ====================== */

    var ExpiredNotification = function (options) {
        this.options = options
        this.$element = $(options.modalId)
        this.time = options.time
        this.$element.appendTo($('body'))
    }

    ExpiredNotification.prototype = {

        constructor: ExpiredNotification

        , setExpirationTime: function () {
            var that = this
              , timeout = setTimeout(function () {
                  that.$element.modal('show')
            }, this.time)
        }
    }


    /* Expired Notification PLUGIN DEFINITION
     * ======================= */

    $.fn.expiredNotification = function (option) {
        console.log('expiration plugin attached')
        var $this = $(this)
            , options = $.extend({}, $.fn.expiredNotification.defaults, typeof option == 'object' && option)
            , data = new ExpiredNotification(options);
            data.setExpirationTime()
    }

    $.fn.expiredNotification.defaults = {
        time: 60
        , modalId: false
    }

    $.fn.expiredNotification.Constructor = ExpiredNotification

}(window.jQuery);
