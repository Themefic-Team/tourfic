"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Line4_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Line4.js":
/*!*******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Line4.js ***!
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



class ApexLine4 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [{
        name: "Yoga",
        data: [65, 65, 65, 120, 120, 80, 120, 100, 100, 120, 120, 120]
      }, {
        name: "Cycling",
        data: [50, 100, 35, 35, 0, 0, 80, 20, 40, 40, 40, 40]
      }, {
        name: "Running",
        data: [20, 40, 20, 80, 40, 40, 20, 60, 60, 20, 110, 60]
      }],
      options: {
        chart: {
          height: 350,
          type: "line",
          toolbar: {
            show: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: [4, 4, 4],
          colors: ["#216fed", "#1EA7C5", "#FF9432"],
          curve: "straight"
        },
        legend: {
          show: false
        },
        xaxis: {
          type: "text",
          categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
        },
        colors: ["#216fed", "#1EA7C5", "#FF9432"],
        markers: {
          size: [8, 8, 6],
          strokeWidth: [0, 0, 4],
          strokeColors: ["#216fed", "#1EA7C5", "#FF9432"],
          border: 0,
          colors: ["#216fed", "#1EA7C5", "#fff"],
          hover: {
            size: 10
          }
        },
        yaxis: {
          title: {
            text: ""
          }
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
      type: "line",
      height: 350
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexLine4);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Line4_js.js.map