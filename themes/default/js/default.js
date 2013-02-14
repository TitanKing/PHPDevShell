/**
 * Call a PHP function
 *
 * The function must be handled by the current controller (method name is "ajax" + functionName)
 *
 * Parameters are preferably passed through POST for two reasons:
 * - GET data maybe polluted for other reasons (sessions handling, ...) where POST are always under control
 * - GET data appear in URL therefore are limited in size and charset
 * @see http://www.cs.tut.fi/~jkorpela/forms/methods.html
 *
 * Note: only application parameters are sent through GET/POST, handling data such as function name sent though headers
 *
 * Caution: prior to PHP 5 the parameters fed to the PHP function are given IN ORDER, NOT BY NAME
 *
 * @param functionName string, the name of the function to call (ie. method "ajax"+functionName of the controller)
 * @param params array, data to be serialized and sent via POST
 * @param extParams array (optional), data to be serialized and sent via GET
 *
 * TODO: possibility of calling a method from another controller
 * TODO: handle errors gracefully
 *
 */
function PHPDS_remoteCall(functionName, params, extParams) {
    var url = document.URL;
    if (extParams) {
        url = URI(url).addQuery(extParams).href();
    }
    return $.when($.ajax({
        url:url,
        dataType:'json',
        data:params,
        type:'POST',
        headers:{'X-Requested-Type':'json', 'X-Remote-Call':functionName},
        beforeSend:function (xhr) {
            xhr.setRequestHeader('X-Requested-Type', 'json');
            xhr.setRequestHeader('X-Remote-Call', functionName);
        }
    })).done(function (data_received, status, deferred) {
            if (deferred.status !== 200) {
                /*deferred.reject();
                 alert('Error ' + deferred.status);*/
            }
        }).fail(function (deferred, status) {
            if (deferred.status !== 200) {
                //deferred.reject();
                alert('Error! ' + deferred.statusText);
            }
        }
    );
}

/**
 * Apply default formatting to the objects inside the given root element (root element is optional, defaults to BODY)
 * @param root DOM object to assign.
 */
function PHPDS_documentReady (root) {
    $(document).ready(function() {
        if (!root) root = $('BODY');
        $("#bg", root).fadeTo(0, 0.3).fadeTo('slow', 1);
        $("form.click-elegance").submit(function () {
            $("#bg").fadeTo('slow', 0.3, function () {
                $(".alert").slideUp('fast');
                $("#ajax-loader-art").fadeIn('fast');
            });
        });
        $("#nav a, a.click-elegance, button.click-elegance").not('#login-url,.grand-parent a').click(function () {
            $("#bg, .alert").fadeTo('slow', 0.3, function () {
                $(".alert").slideUp('fast');
                $("#ajax-loader-art").fadeIn('fast');
            });
        });
        $("#bg").load(function () {
            $("#ajax-loader-art").fadeOut();
        });
    });
}

(function ($) {
    $.fn.getAjaxDelete = function () {
        var bg = this;
        bg.on('click', ".first-click", function () {
            var first = this;
            $(first).removeClass("first-click").addClass("ajax-click btn-danger").parents("tr").addClass("error");
            $("i", first).removeClass("icon-remove").addClass("icon-trash icon-white");
            return false;
        });
        bg.on('click', ".ajax-click", function () {
            var item = this;
            var url = $(item).attr('href');
            $(item).addClass("disabled");
            $("i", item).removeClass("icon-trash").append('<img src="themes/default/images/loader.gif" width="15" height="15" />');
            $.get(url, function () {
                $(item).parents("tr").fadeOut('slow');
            });
            return false;
        });
    }
}(jQuery));

/**
 * Check multiple checkboxes at once.
 */
(function ($) {
    $.fn.checkAllCheckbox = function () {
        var checkall = this;
        return this.each(function () {
            checkall.click(function () {
                var checkedStatus = this.checked;
                checkall.parents("form").find(':checkbox').each(function() {
                    $(this).prop('checked', checkedStatus);
                });
            });
        });
    }
}(jQuery));

/**
 * Plugin to only allow buttons to be pressed when certain checkboxes are pressed.
 */
(function ($) {
    $.fn.enableButtonWhenChecked = function (buttonwrapper) {
        if( typeof(buttonwrapper) === "undefined" || buttonwrapper === null ) buttonwrapper = ".toggle-disabled-buttons";
        return this.each(function () {
            var checkboxes = $("input[type='checkbox']", this);
            var submitButt = $(buttonwrapper + " button[type='submit']");
            checkboxes.click(function() {
                submitButt.attr("disabled", !checkboxes.is(":checked"));
            });
        });
    }
}(jQuery));

/**
 * https://github.com/javierjulio/textarea-auto-expand
 */
(function ($) {
    $.fn.textareaAutoExpand = function () {
        return this.each(function () {
            var textarea = $(this);
            var height = textarea.height();
            var diff = parseInt(textarea.css('borderBottomWidth')) + parseInt(textarea.css('borderTopWidth')) +
                parseInt(textarea.css('paddingBottom')) + parseInt(textarea.css('paddingTop'));
            var hasInitialValue = (this.value.replace(/\s/g, '').length > 0);

            if (textarea.css('box-sizing') === 'border-box' ||
                textarea.css('-moz-box-sizing') === 'border-box' ||
                textarea.css('-webkit-box-sizing') === 'border-box') {
                height = textarea.outerHeight();

                if (this.scrollHeight + diff == height) // special case for Firefox where scrollHeight isn't full height on border-box
                    diff = 0;
            } else {
                diff = 0;
            }

            if (hasInitialValue) {
                textarea.height(this.scrollHeight);
            }

            textarea.on('scroll input keyup', function (event) { // keyup isn't necessary but when deleting text IE needs it to reset height properly
                if (event.keyCode == 13 && !event.shiftKey) {
                    // just allow default behavior to enter new line
                    if (this.value.replace(/\s/g, '').length == 0) {
                        event.stopImmediatePropagation();
                        event.stopPropagation();
                    }
                }

                textarea.height(0);
                //textarea.height(Math.max(height - diff, this.scrollHeight - diff));
                textarea.height(this.scrollHeight - diff);
            });
        });
    }
}(jQuery));

/**
 * Does simple name filtering for search fields that does not need filtering from database.
 */
(function ($) {
    $.fn.searchFilter = function () {

        return this.each(function () {
            var filterelement = $(this);

            //filter results based on query
            function filter(selector, query) {
                query = $.trim(query); //trim white space
                query = query.replace(/ /gi, '|'); //add OR for regex query

                $(selector).each(function() {
                    ($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('tr-visible') : $(this).show().addClass('tr-visible');
                });

                if (!$(".tr-visible")[0]) {
                    $("thead").hide();
                    $(".quickfilter-no-results").fadeIn("slow");
                } else {
                    $("thead").fadeIn("slow");
                    $(".quickfilter-no-results").fadeOut("slow");
                }
            }

            $('tbody tr').addClass('visible');

            $(filterelement).keyup(function(event) {
                //if esc is pressed or nothing is entered
                if (event.keyCode == 27 || $(this).val() == '') {
                    //if esc is pressed we want to clear the value of search box
                    $(this).val('');

                    //we want each row to be visible because if nothing
                    //is entered then all rows are matched.
                    $('tbody tr').removeClass('visible').show().addClass('visible');
                }
                //if there is text, lets filter
                else {
                    filter('tbody tr', $(this).val());
                }
            });
        });
    }
}(jQuery));
