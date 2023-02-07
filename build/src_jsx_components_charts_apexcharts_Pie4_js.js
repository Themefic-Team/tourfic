"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Pie4_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Pie4.js":
/*!******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Pie4.js ***!
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



class ApexPie4 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [42, 47, 52, 58],
      options: {
        chart: {
          // width: 300,
          type: "polarArea",
          sparkline: {
            enabled: true
          }
        },
        labels: ["VIP", "Reguler", "Exclusive", "Economic"],
        fill: {
          opacity: 1,
          colors: ["#709fba", "#ff5c00", "#5bcfc5", "#2258bf"]
        },
        stroke: {
          width: 0,
          colors: undefined
        },
        yaxis: {
          show: false
        },
        legend: {
          position: "bottom"
        },
        plotOptions: {
          polarArea: {
            rings: {
              strokeWidth: 0
            }
          }
        },
        theme: {
          monochrome: {
            enabled: true,
            shadeTo: "light",
            shadeIntensity: 0.6
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
      type: "polarArea",
      height: 251
      // width={300}
    }));
  }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexPie4);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Pie4_js.js.map