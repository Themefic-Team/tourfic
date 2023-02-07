"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_RadialBar_js"],{

/***/ "./src/jsx/components/charts/apexcharts/RadialBar.js":
/*!***********************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/RadialBar.js ***!
  \***********************************************************/
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



class ApexRedialBar extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [81],
      options: {
        chart: {
          height: 300,
          type: "radialBar",
          offsetY: -10
        },
        plotOptions: {
          radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
              name: {
                fontSize: "16px",
                color: undefined,
                offsetY: 120
              },
              value: {
                offsetY: 0,
                fontSize: "34px",
                color: "#216fed",
                formatter: function (val) {
                  return val + "%";
                }
              }
            }
          }
        },
        fill: {
          type: "gradient",
          colors: "#216fed",
          gradient: {
            shade: "dark",
            shadeIntensity: 0.15,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 65, 91]
          }
        },
        stroke: {
          dashArray: 4,
          colors: "#216fed"
        },
        labels: [""]
      }
    };
  }
  render() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      id: "chart"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      options: this.state.options,
      series: this.state.series,
      type: "radialBar",
      height: 350
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexRedialBar);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_RadialBar_js.js.map