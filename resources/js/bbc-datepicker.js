define(['jquery-1.9'],function($){
    $(function() {

        /**
         * This is for accessibility so when the tab focuses the date
         * it should open that bit e.g year or month.
         */
        $('.bbc-datepicker__box-year-number a').on("focus", function() {
            $('.bbc-datepicker__checkbox-year').prop('checked', true);
        });

         $('.bbc-datepicker__box-month-name a').on("focus", function() {
             $('.bbc-datepicker__checkbox-month').prop('checked', true);
        });
    });
});
