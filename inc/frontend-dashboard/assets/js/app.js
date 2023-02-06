!function (t) {
    "use strict";

    function s() {
        for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, n = e.length; t < n; t++) "nav-item dropdown active" === e[t].parentElement.getAttribute("class") && (e[t].parentElement.classList.remove("active"), e[t].nextElementSibling.classList.remove("show"))
    }

    var a;
    t("#side-menu").metisMenu(), t("#vertical-menu-btn").on("click", function (e) {
        e.preventDefault(), t("body").toggleClass("sidebar-enable"), 992 <= t(window).width() ? t("body").toggleClass("vertical-collpsed") : t("body").removeClass("vertical-collpsed")
    }), t("#sidebar-menu a").each(function () {
        var e = window.location.href.split(/[?#]/)[0];
        this.href == e && (t(this).addClass("active"), t(this).parent().addClass("mm-active"), t(this).parent().parent().addClass("mm-show"), t(this).parent().parent().prev().addClass("mm-active"), t(this).parent().parent().parent().addClass("mm-active"), t(this).parent().parent().parent().parent().addClass("mm-show"), t(this).parent().parent().parent().parent().parent().addClass("mm-active"))
    }), t(".navbar-nav a").each(function () {
        var e = window.location.href.split(/[?#]/)[0];
        this.href == e && (t(this).addClass("active"), t(this).parent().addClass("active"), t(this).parent().parent().addClass("active"), t(this).parent().parent().parent().addClass("active"), t(this).parent().parent().parent().parent().addClass("active"), t(this).parent().parent().parent().parent().parent().addClass("active"))
    }), t(document).ready(function () {
        var e;
        0 < t("#sidebar-menu").length && 0 < t("#sidebar-menu .mm-active .active").length && (300 < (e = t("#sidebar-menu .mm-active .active").offset().top) && (e -= 300, t(".vertical-menu .simplebar-content-wrapper").animate({scrollTop: e}, "slow")))
    }), t(document).on("click", "body", function (e) {
        0 < t(e.target).closest(".right-bar-toggle, .right-bar").length || t("body").removeClass("right-bar-enabled")
    }), function () {
        if (document.getElementById("topnav-menu-content")) {
            for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, n = e.length; t < n; t++) e[t].onclick = function (e) {
                "#" === e.target.getAttribute("href") && (e.target.parentElement.classList.toggle("active"), e.target.nextElementSibling.classList.toggle("show"))
            };
            window.addEventListener("resize", s)
        }
    }(), t(function () {
        t('[data-bs-toggle="tooltip"]').tooltip()
    }), t(function () {
        t('[data-bs-toggle="popover"]').popover()
    })
}(jQuery);

var options = {
    series: [
        {
            name: 'South',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        },
        {
            name: 'North',
            data: [20, 30, 20, 40, 35, 50, 60, 80, 100]
        },
        {
            name: 'Central',
            data: [10, 20, 5, 20, 15, 30, 40, 60, 80]
        }
    ],
    chart: {
        type: 'area',
        height: 350,
        stacked: true,
        events: {
            selection: function (chart, e) {
                console.log(new Date(e.xaxis.min))
            }
        },
    },
    colors: ['#008FFB', '#00E396', '#CED4DC'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth'
    },
    fill: {
        type: 'gradient',
        gradient: {
            opacityFrom: 0.6,
            opacityTo: 0.8,
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'left'
    },
    xaxis: {
        type: 'datetime'
    },
};

var chart = new ApexCharts(document.querySelector("#chart-with-area"), options);
chart.render();