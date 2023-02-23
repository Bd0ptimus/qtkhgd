/*=========================================================================================
    File Name: picker-date-time.js
    Description: Pick a date/time Picker, Date Range Picker JS
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function(window, document, $) {
    'use strict';

    /*******    Pick-a-date Picker  *****/
    // Basic date
    $('.pickadate').pickadate();

    // Format Date Picker
    $('.format-picker').pickadate({
        format: 'yyyy-mm-d'
    });

    // Format Date Picker
    $('.custom-format-picker').pickadate({
        format: 'dd/mm/yyyy'
    });

    // Date limits
    $('.pickadate-limits').pickadate({
        min: [2019,3,20],
        max: [2019,5,28]
    });
    
    // Disabled Dates & Weeks
    
    $('.pickadate-disable').pickadate({
        disable: [
            1,
            [2019,3,6],
            [2019,3,20]
        ]
    });

    // Picker Translations
    $( '.pickadate-translations' ).pickadate({
        formatSubmit: 'dd/mm/yyyy',
        monthsFull: [ 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ],
        monthsShort: [ 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec' ],
        weekdaysShort: [ 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ],
        today: 'aujourd\'hui',
        clear: 'clair',
        close: 'Fermer'
    });

    $( '.pickadate-vi' ).pickadate({
        format: 'dd/mm/yyyy',
        formatSubmit: 'yyyy-mm-dd',
        monthsFull: [ 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12' ],
        monthsShort: [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ],
        weekdaysShort: [ 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN' ],
        today: 'Hôm nay',
        clear: 'Xóa',
        close: 'Đóng',
        selectYears: true,
        selectMonths: true,
        min: [1920,1,1],
    });

    // Month Select Picker
    $('.pickadate-months').pickadate({
        selectYears: false,
        selectMonths: true
    });

    // Month and Year Select Picker
    $('.pickadate-months-year').pickadate({
        selectYears: true,
        selectMonths: true
    });

    // Short String Date Picker
    $('.pickadate-short-string').pickadate({
        weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
        showMonthsShort: true
    });

    // Change first weekday
    $('.pickadate-firstday').pickadate({
        firstDay: 1
    });

    

    /*******    Pick-a-time Picker  *****/
    // Basic time
    $('.pickatime').pickatime();

    // Format options
    $('.pickatime-format').pickatime({
        // Escape any “rule” characters with an exclamation mark (!).
        format: 'HH:i',
        formatLabel: 'HH:i',
        formatSubmit: 'HH:i',
        hiddenPrefix: 'prefix__',
        hiddenSuffix: '__suffix'
    });


    // Format options
    $('.pickatime-formatlabel').pickatime({
        formatLabel: function(time) {
            var hours = ( time.pick - this.get('now').pick ) / 60,
                label = hours < 0 ? ' !hours to now' : hours > 0 ? ' !hours from now' : 'now';
            return  'h:i a <sm!all>' + ( hours ? Math.abs(hours) : '' ) + label +'</sm!all>';
        }
    });

    // Min - Max Time to select
    $( '.pickatime-min-max').pickatime({

        // Using Javascript
        min: new Date(2015,3,20,7),
        max: new Date(2015,7,14,18,30)

        // Using Array
        // min: [7,30],
        // max: [14,0]
    });

    // Intervals
    $('.pickatime-intervals').pickatime({
        interval: 150
    });

    // Disable Time
    $('.pickatime-disable').pickatime({
        disable: [
        // Disable Using Integers
            3, 5, 7, 13, 17, 21

        /* Using Array */
            // [0,30],
            // [2,0],
            // [8,30],
            // [9,0]
        ]
    });

    
    // Close on a user action
    $('.pickatime-close-action').pickatime({
        closeOnSelect: false,
        closeOnClear: false
    });


})(window, document, jQuery);