"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_charts_apexcharts_Pie5_js"],{

/***/ "./src/jsx/components/charts/apexcharts/Pie5.js":
/*!******************************************************!*\
  !*** ./src/jsx/components/charts/apexcharts/Pie5.js ***!
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



class ApexRedialBar2 extends (react__WEBPACK_IMPORTED_MODULE_1___default().Component) {
  constructor(props) {
    super(props);
    this.state = {
      series: [71, 63, 90],
      options: {
        chart: {
          type: "radialBar",
          //width:320,
          // height: 370,
          offsetY: 0,
          offsetX: 0
        },
        plotOptions: {
          radialBar: {
            size: undefined,
            inverseOrder: false,
            hollow: {
              margin: 0,
              size: "30%",
              background: "transparent"
            },
            track: {
              show: true,
              background: "#216fed",
              strokeWidth: "10%",
              opacity: 1,
              margin: 18 // margin is in pixels
            }
          }
        },

        responsive: [{
          breakpoint: 830,
          options: {
            chart: {
              offsetY: 0,
              offsetX: 0
            },
            legend: {
              position: "bottom",
              offsetX: 0,
              offsetY: 0
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: "20%"
                }
              }
            }
          }
        }, {
          breakpoint: 800,
          options: {
            chart: {
              offsetY: 0,
              offsetX: 0
            },
            legend: {
              position: "bottom",
              offsetX: 0,
              offsetY: 0
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: "10%"
                }
              }
            }
          }
        }, {
          breakpoint: 768,
          options: {
            chart: {
              offsetY: 0,
              offsetX: 0
            },
            legend: {
              position: "bottom",
              offsetX: 0,
              offsetY: 0
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: "30%"
                }
              }
            }
          }
        }, {
          breakpoint: 330,
          options: {
            chart: {
              offsetY: 0,
              offsetX: 0
            },
            legend: {
              position: "bottom",
              offsetX: 0,
              offsetY: 0
            },
            plotOptions: {
              radialBar: {
                hollow: {
                  size: "20%"
                }
              }
            }
          }
        }],
        fill: {
          opacity: 1
        },
        colors: ["#216fed", "#216fed", "#216fed"],
        labels: ["Ticket A", "Ticket B", "Ticket C"],
        legend: {
          fontSize: "14px",
          show: true,
          position: "bottom"
        }
      }
    };
  }
  render() {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      id: "chart",
      className: "legent-text"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-apexcharts'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      options: this.state.options,
      series: this.state.series,
      type: "radialBar",
      height: this.props.height ? this.props.height : 370
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ApexRedialBar2);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_charts_apexcharts_Pie5_js.js.map