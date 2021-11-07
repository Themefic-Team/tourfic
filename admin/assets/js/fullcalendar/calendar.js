jQuery('document').ready(function($){


//document.addEventListener('DOMContentLoaded', function() {
    
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      selectable: true,
      initialView: 'dayGridMonth',
      customButtons: {
        reloadButton: {
            text: 'Reload',
            click: function () {
                calendar.refetchEvents();
            }
        }
    },
      headerToolbar: {
        start: 'reloadButton',
        center: 'title',
      },
      dateClick: function(info) {
          console.log(info);
        //alert('clicked ' + info.dateStr);
      },
      select: function(info) {
          console.log(info);
        $('.check-in input').val(info.startStr);
        $('.check-out input').val(info.endStr);
      }
    });

    calendar.render();
    console.log(calendar);
  //});

});

  /**
   * document.addEventListener('DOMContentLoaded', function() {
    
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      selectable: true,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth'
      },
      dateClick: function(info) {
          console.log(info);
        //alert('clicked ' + info.dateStr);
      },
      select: function(info) {
        var sDate = info.startStr;
        var checkIn = document.getElementsByClassName('check-in');
        checkIn.querySelector('input').innerHTML = sDate;
       // alert('selected ' + info.startStr + ' to ' + info.endStr);
      }
    });

    calendar.render();
  });
   */