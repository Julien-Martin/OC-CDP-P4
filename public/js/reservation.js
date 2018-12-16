var ticketControl = $('#ticketControl');
var container = $('div#tickets_visitors');
var index = container.find('.ticket_visitor').length;
var datepicker = $('#datetimepicker');
var outOfStockSelector = datepicker.data('date');
var outOfStockDates = [];
var infoTickets = $('#info-tickets');
var infoDate = $('#info-date');
var visitDate = $('#reservation_visitDate');

jQuery(function ($) {
    if (moment().hour() >= 14) {
        $('#reservation_halfDay').parent().remove();
    }

    outOfStockSelector.forEach(function (elem) {
        outOfStockDates.push(moment(elem.visitDate.date.toString()));
    });
    outOfStockDates.push(moment().month(11).date(25), moment().add(1, 'years').month(11).date(25), moment().month(10).date(1), moment().add(1, 'years').month(10).date(1), moment().month(5).date(1), moment().add(1, 'years').month(5).date(1));
    datepicker.datetimepicker({
        locale: moment.locale(),
        format: 'L',
        minDate: moment(),
        maxDate: moment().add(1, 'years'),
        dateFormat: "yy/mm/dd",
        daysOfWeekDisabled: [0, 2],
        disabledDates: outOfStockDates
    });
    infoDate.text(visitDate.val());
    datepicker.on('input', () => {
        infoDate.text(visitDate.val());
    });

    ticketControl.on('change', () => {
        var nbr = $('div.ticket_visitor').length;
        infoTickets.text(ticketControl.val());
        if (ticketControl.val() > nbr) {
            for (i = nbr + 1; i <= ticketControl.val(); i++) {
                addVisitor();
            }
        } else if (ticketControl.val() < nbr) {
            for (i = ticketControl.val(); i < nbr; i++) {
                deleteVisitor();
            }
        }
    });

    function addVisitor() {
        var template = container.attr('data-prototype')
            .replace(/__name__/g, index);
        var prototype = $(template);
        prototype.attr('id', 'tickets_visitor_' + index);
        container.append(prototype);
        index++;
    }

    function deleteVisitor() {
        $('div.ticket_visitor').last().remove();
        index--;
    }
});
