var ordinal = function(number) {
    var mod = number % 100;
    if (mod >= 11 && mod <= 13) {
        return number + 'th';
    } else if (mod % 10 == 1) {
        return number + 'st';
    } else if (mod % 10 == 2) {
        return number + 'nd';
    } else if (mod % 10 == 3) {
        return number + 'rd';
    } else {
        return number + 'th';
    }
};

// this needs to be global as it gets tapped by the page js
setRecurringOptions = function(start_elem, month_group_elem, rectypemonth) {
    // get startdate info
    var weekdays = [
        "Sunday", 
        "Monday", 
        "Tuesday",
        "Wednesday", 
        "Thursday", 
        "Friday", 
        "Saturday"
    ];

    var start_year = start_elem.val().substring(6, 10);
    var start_month = start_elem.val().substring(0, 2);
    var start_day = parseInt(start_elem.val().substring(3, 5));
    var start_date = new Date(start_year, start_month - 1, start_day);
    var start_weekday = weekdays[start_date.getDay()];

    // get week in month
    var nth = {
        "1": "First",
        "2": "Second",
        "3": "Third",
        "4": "Fourth",
        "5": "Last",
        1: "First",
        2: "Second",
        3: "Third",
        4: "Fourth",
        5: "Last"
    };

    // get number of days (28, 29, 30, 31) in month
    var days_in_month = 28;
    d = new Date(start_year, start_month - 1, 28);
    while (days_in_month == d.getDate()) {
        d = new Date(start_year, start_month - 1, ++days_in_month);
    }
    days_in_month--;

    var week = 0; // the week of the start day
    var total_weeks = 0; // total weeks in the month
    for (var i = 1; i <= days_in_month; i++) {
        var d = new Date(start_year, start_month - 1, i);
        if (weekdays[d.getDay()] == start_weekday && i <= start_day) {
            week++;
        }
        if (weekdays[d.getDay()] == start_weekday) {
            total_weeks++;
        }
    }

    // remove options, if any
    month_group_elem.children(".dynamicRecurring").remove();
    // populate rectypemonth with appropriate options

    var dynamicRecurringSelected = '';
    if (nth[week] != undefined) {
        dynamicRecurringSelected = '';
        if (rectypemonth == nth[week].toLowerCase()) { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='" + nth[week].toLowerCase() + "'>" + nth[week] + " " + start_weekday + " of every month</option>")
    }

    if (week == 4 && total_weeks == 4) {
        dynamicRecurringSelected = '';
        if (rectypemonth == 'last') { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='last'>" + "Last " + start_weekday + " of every month</option>")
    }

    if (days_in_month == start_day) {
      dynamicRecurringSelected = '';
      if (rectypemonth == 'lastday') { dynamicRecurringSelected = ' selected="selected" '}
      month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='lastday'>Last day of every month</option>");
    }

    var text = ordinal(start_day) + ' of every month';
    dynamicRecurringSelected = '';
    if (rectypemonth == 'date') { dynamicRecurringSelected = ' selected="selected" '}
    month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='date'>" + text + "</option>");
};

require(['jquery', 'wdn', frontend_url + 'templates/default/html/js/vendor/select2/js/select2.min.js'], function($, WDN) {
    $(document).ready(function() {
        $('form').on('change blur', 'input', function() {
            $(this).removeClass('validation-failed');
        });

        $(".use-select2").select2();

        $('#bulk-action').change(function () {
            var ids = [];

            // find ids of all events that are checked
            $('.select-event').each(function() {
                if ($(this).is(':checked')) {
                    ids.push(parseInt($(this).attr('data-id')));
                }
            });

            if (ids.length > 0) {
                $('#bulk-action-ids').val(ids.join(','));
                $('#bulk-action-action').val($(this).val());

                var confirm_string = null;
                if ($(this).val() == 'delete') {
                    confirm_string = 'Are you sure you want to delete these ' + ids.length.toString() + ' events?';
                } else if ($(this).val() == 'move-to-upcoming') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Upcoming"?';
                } else if ($(this).val() == 'move-to-pending') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Pending"?';
                } else {
                    // do nothing
                    return;
                }

                if (window.confirm(confirm_string)) {
                    $('#bulk-action-form').submit();
                }
            }
        });

        $('#checkbox-toggle').click(function (click) {
            console.log('click checkbox toggle', $(this).is(":checked"));
            $(".select-event").prop("checked", $(this).is(":checked"));
        });

        $('.pending-event-tools, .upcoming-event-tools, .past-event-tools, .searched-event-tools').change(function () {
            if ($(this).val() == 'recommend') {
                // redirect to recommend URL
                window.location = $(this).attr('data-recommend-url');
            } else if ($(this).val() == 'delete') {
                if (window.confirm('Are you sure you want to delete this event?')) {
                    $('#delete-' + $(this).attr('data-id')).submit();
                }
            } else if ($(this).val() == 'move-to-upcoming') {
                $('#move-target-' + $(this).attr('data-id')).val('upcoming');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'move-to-pending') {
                $('#move-target-' + $(this).attr('data-id')).val('pending');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'promote') {
                $('#promote-target-' + $(this).attr('data-id')).val('promote');
                $('#promote-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'hide-promo') {
                $('#promote-target-' + $(this).attr('data-id')).val('hide-promo');
                $('#promote-' + $(this).attr('data-id')).submit();
            }
        });

        $('#toggle-search').click(function (click) {
            click.preventDefault();
            $('#search-form').slideToggle(400, function() {
                if ($('#search-form').is(':visible')) {
                    $('#search-form input').focus();
                }
            });
        });

        notifier = {
            mark_input_invalid: function(input) {
                input.addClass('validation-failed');
            },
            failure: function(header, message) {
                $('#notice').removeClass('affirm').addClass('negate').removeClass('alert');
                $('#notice h4').text(header);
                $('#notice .message-content').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            info: function(header, message) {
                $('#notice').removeClass('affirm').removeClass('negate').removeClass('alert');
                $('#notice h4').text(header);
                $('#notice .message-content').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            success: function(header, message) {
                $('#notice').addClass('affirm').removeClass('negate').removeClass('alert');
                $('#notice h4').text(header);
                $('#notice .message-content').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            alert: function(header, message) {
                $('#notice').removeClass('affirm').removeClass('negate').addClass('alert');
                $('#notice h4').text(header);
                $('#notice .message-content').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            }
        };
    });
});
