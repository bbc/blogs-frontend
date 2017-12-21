// This file is generated.
// It is an intermediary file, we should update the Gulp file to use pipes correctly instead of using intermediary files
'use strict';
var BBCDatePicker = /** @class */ (function () {
    function BBCDatePicker() {
        this.monthBox = this.getCheckBox('bbc-datepicker__checkbox-month');
        this.yearBox = this.getCheckBox('bbc-datepicker__checkbox-year');
        var yearBoxNumbers = document.querySelectorAll('.bbc-datepicker__box-year-number a');
        for (var i = 0; i < yearBoxNumbers.length; i++) {
            yearBoxNumbers[i].onfocus = this.selectYears.bind(this);
        }
        var monthBoxNumbers = document.querySelectorAll('.bbc-datepicker__box-month-name a');
        for (var i = 0; i < monthBoxNumbers.length; i++) {
            monthBoxNumbers[i].onfocus = this.selectMonths.bind(this);
        }
    }
    BBCDatePicker.prototype.getCheckBox = function (className) {
        return document.getElementsByClassName(className)[0];
    };
    BBCDatePicker.prototype.selectMonths = function () {
        this.monthBox.checked = true;
    };
    BBCDatePicker.prototype.selectYears = function () {
        this.yearBox.checked = true;
    };
    return BBCDatePicker;
}());
new BBCDatePicker();
