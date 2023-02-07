"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Bar2_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Bar2.js":
/*!******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Bar2.js ***!
  \******************************************************/
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



class ApexBar2 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [{
        name: "Cycling",
        data: [80, 40, 55, 20, 45, 30, 80, 90, 85, 90, 30, 85]
      }],
      options: {
        chart: {
          type: "bar",
          height: 230,
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            dataLabels: {
              position: "top"
            }
          }
        },
        colors: ["#216fed"],
        legend: {
          show: false,
          position: "top",
          horizontalAlign: "left"
        },
        dataLabels: {
          enabled: false,
          offsetX: -6,
          style: {
            fontSize: "12px"
            // colors: ["#fff"],
          }
        },

        stroke: {
          show: false
        },
        yaxis: {
          lines: {
            show: false
          }
        },
        xaxis: {
          show: false,
          categories: [2001, 2002, 2003, 2004, 2005, 2006, 2007]
        }
      }
    };
  }
  render() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      id: "chart",
      className: "bar-chart"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      options: this.state.options,
      series: this.state.series,
      type: "bar",
      height: 350
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexBar2);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Bar2_js.js.map