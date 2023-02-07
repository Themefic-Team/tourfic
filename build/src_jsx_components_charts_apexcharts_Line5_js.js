"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Line5_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Line5.js":
/*!*******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Line5.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());



class ApexLine5 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [{
        name: "Recovered Patient",
        data: [500, 230, 600, 360, 700, 890, 750, 420, 600, 300, 420, 220]
      }, {
        name: "New Patient",
        data: [250, 380, 200, 300, 200, 520, 380, 770, 250, 520, 300, 900]
      }],
      options: {
        chart: {
          height: 350,
          type: "area",
          group: "social",
          toolbar: {
            show: false
          },
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: [2, 2],
          colors: ["#216fed", "#709fba"],
          curve: "straight"
        },
        legend: {
          tooltipHoverFormatter: function (val, opts) {
            return val + " - " + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + "";
          },
          markers: {
            fillColors: ["#216fed", "#709fba"],
            width: 19,
            height: 19,
            strokeWidth: 0,
            radius: 19
          }
        },
        markers: {
          size: 6,
          border: 0,
          colors: ["#216fed", "#709fba"],
          hover: {
            size: 6
          }
        },
        xaxis: {
          categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "10 Jan", "11 Jan", "12 Jan"]
        },
        yaxis: {
          labels: {
            style: {
              colors: "#3e4954",
              fontSize: "14px",
              fontFamily: "Poppins",
              fontWeight: 100
            }
          }
        },
        fill: {
          colors: ["#2258bf", "#709fba"],
          type: "solid",
          opacity: 0.07
        },
        grid: {
          borderColor: "#f1f1f1"
        }
      }
    };
  }
  render() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      id: "chart",
      className: "line-chart-style bar-chart legent-text"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      options: this.state.options,
      series: this.state.series,
      type: "area",
      height: 350
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexLine5);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Line5_js.js.map