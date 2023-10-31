<div class="tf-tour-wrapper" id="tf-tour-calendar">
    <h2 class="tf-section-title">Calendar & prices</h2>
    <div id="tf-tour3-caleandar"></div>
</div>

<script type="text/javascript" src="./assets/js/caleandar.min.js"></script>
<script>
    var events = [{
            'Date': new Date(2023, 9, 7),
            'Title': '$200',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 8),
            'Title': '$300',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 9),
            'Title': '$250',
            'Link': 'https://github.com/joynal05'
        }, {
            'Date': new Date(2023, 9, 10),
            'Title': '$200',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 11),
            'Title': '$300',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 12),
            'Title': '$250',
            'Link': 'https://github.com/joynal05'
        }, {
            'Date': new Date(2023, 9, 17),
            'Title': '$200',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 18),
            'Title': '$300',
            'Link': 'https://github.com/joynal05'
        },
        {
            'Date': new Date(2023, 9, 19),
            'Title': '$250',
            'Link': 'https://github.com/joynal05'
        },
    ];
    var settings = {};
    var element = document.getElementById('tf-tour3-caleandar');
    caleandar(element, events, settings);



    (function ($) {
        "use strict";
        $(document).ready(function () {
            $("#tf-tour3-caleandar li.cld-day.currMonth").click(function () {
                var link = $(this).find("a").attr("href");
                if (link) {
                    window.open(link, "_blank");
                }
            });
        });

    }(jQuery));
</script>