/*
 * CZA app js tools
 * Usage example: 
 * init jQuery.fn.czaTools('init', options);
 * $.fn..czaTools('init', {a:2}).czaTools('show');
 * $(document).czaTools('init', {a:2}).czaTools('show');
 */
(function ($) {
    var settings = {version: '1.0'};
    var methods = {
        init: function (options) {
            if (options) {
                $.extend(settings, options);
            }
//            console.log(settings);
            return this;
        },
        showLoading: function (options) {
            var defaults = {selector: '', msg: '&nbsp;'};
            if (options) {
                $.extend(defaults, options);
            }

            if (defaults.selector === '')
            {
                $.blockUI({
                    message: "<div class='cza-loading'></div>"
//                    message: "<i class='fa fa-refresh fa-spin'></i>"
                    
                });
            } else
            {
                $(defaults.selector).block({
                    css: {
                        padding: 0,
                        margin: 0,
                        width: '60%',
                        top: '100%',
                        left: '100%',
                        textAlign: 'center',
                        color: '#000',
                        border: '0px solid #aaa',
                        cursor: 'wait',
                        backgroundColor: 'transparent'
                    },
                    message: "<div class='cza-loading'></div>"
//                    message: "<i class='fa fa-refresh fa-spin'></i>"
                });
            }
            return this;
        },
        hideLoading: function (options) {
            var defaults = {selector: ''};
            if (options) {
                $.extend(defaults, options);
            }
            if (defaults.selector === '') {
                $.unblockUI();
            } else {
                $(defaults.selector).unblock();
            }
            return this;
        },
        update: function () {
            console.log('update');
            return this;
        }
    };
    $.fn.czaTools = function (method) {
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.tooltip');
        }
    }
}
)(jQuery);
