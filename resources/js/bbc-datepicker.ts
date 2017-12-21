'use strict';

class BBCDatePicker {
    monthBox: HTMLInputElement;
    yearBox: HTMLInputElement;

    constructor() {
        this.monthBox = this.getCheckBox('bbc-datepicker__checkbox-month');
        this.yearBox = this.getCheckBox('bbc-datepicker__checkbox-year');

        const yearBoxNumbers = <NodeListOf<HTMLElement>>document.querySelectorAll('.bbc-datepicker__box-year-number a');
        for (let i = 0; i < yearBoxNumbers.length; i++) {
            yearBoxNumbers[i].onfocus = this.selectYears.bind(this);
        }
        const monthBoxNumbers = <NodeListOf<HTMLElement>>document.querySelectorAll('.bbc-datepicker__box-month-name a');
        for (let i = 0; i < monthBoxNumbers.length; i++) {
            monthBoxNumbers[i].onfocus = this.selectMonths.bind(this);
        }
    }

    getCheckBox(className: string): HTMLInputElement {
        return <HTMLInputElement>document.getElementsByClassName(className)[0];
    }

    selectMonths() {
        this.monthBox.checked = true;
    }

    selectYears() {
        this.yearBox.checked = true;
    }
}

new BBCDatePicker();
