jQuery('document').ready(function ($) {

  var calendarEl = document.getElementById('calendar');
  //var calendarEl = $('#calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    selectable: true,
    initialView: 'dayGridMonth',
    visibleRange: {
      start: '2020-03-22',
      end: '2020-03-25'
    },
    customButtons: {
      reloadButton: {
        text: 'Reload',
        click: function () {
          calendar.refetchEvents();
          calendar.render();
        }
      }
    },
    headerToolbar: {
      start: 'reloadButton',
      center: 'title',
    },
    dateClick: function (info) {
      console.log(info);
      //alert('clicked ' + info.dateStr);
    },
    select: function (info) {
      $('.fixed_availability .check-in input').val(info.startStr);
      $('.fixed_availability .check-out input').val(info.endStr);
    }
  });
  calendar.render();
  $('.tour-type select').on('change', function () {
    console.log('dd');
    calendar.render();
    $('#calendar').find('button').trigger('click');
  })
});
