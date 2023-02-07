"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Bar3_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Bar3.js":
/*!******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Bar3.js ***!
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



class ApexBar3 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [{
        name: "Income",
        data: [420, 550, 850, 220, 650]
      }, {
        name: "Expenses",
        data: [170, 850, 101, 90, 250]
      }],
      options: {
        chart: {
          type: "bar",
          height: 350,
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "55%",
            endingShape: "rounded"
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          show: true,
          fontSize: "12px",
          fontWeight: 300,
          labels: {
            colors: "black"
          },
          position: "bottom",
          horizontalAlign: "center",
          markers: {
            width: 19,
            height: 19,
            strokeWidth: 0,
            radius: 19,
            strokeColor: "#fff",
            fillColors: ["#fd683e", "#216fed"],
            offsetX: 0,
            offsetY: 0
          }
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
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"]
        },
        xaxis: {
          categories: ["06", "07", "08", "09", "10"]
        },
        fill: {
          colors: ["#fd683e", "#216fed"],
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return "$ " + val + " thousands";
            }
          }
        }
      }
    };
  }
  render() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      id: "chart",
      className: "bar-chart legent-text"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      options: this.state.options,
      series: this.state.series,
      type: "bar",
      height: 350
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexBar3);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Bar3_js.js.map