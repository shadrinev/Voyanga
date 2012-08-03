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

        , startTimer: function () {
            var that = this
            that.timeout = setTimeout(function () {
                  that.$element.modal('show')
            }, this.time)
        }

        , stopTimer: function () {
            var that = this
            clearTimeout(that.timeout)
        }

        , resetTimer: function () {
            var that = this
            this.stopTimer
            this.startTimer
        }
    }


    /* Expired Notification PLUGIN DEFINITION
     * ======================= */

    $.fn.expiredNotification = function (option) {
        var $this = $(this)
            , options = $.extend({}, $.fn.expiredNotification.defaults, typeof option == 'object' && option)
            , data = new ExpiredNotification(options);
        if (typeof option == 'string') data[option]()
        if (options.autoStart)
            data.setExpirationTime()
    }

    $.fn.expiredNotification.defaults = {
        time: 60
        , modalId: false
        , autoStart: true
    }

    $.fn.expiredNotification.Constructor = ExpiredNotification

}(window.jQuery);
