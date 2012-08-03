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
        return this.each(function () {
            var $this = $(this)
                , options = $.extend({}, $.fn.modal.defaults, typeof option == 'object' && option)
                , data = new ExpiredNotification(options);
            data.setExpirationTime()
        })
    }

    $.fn.modal.defaults = {
        time: 60
        , modalId: false
    }

    $.fn.expiredNotification.Constructor = ExpiredNotification

}(window.jQuery);
