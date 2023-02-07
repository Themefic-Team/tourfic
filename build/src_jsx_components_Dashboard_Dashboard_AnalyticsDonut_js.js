"use strict";
(self["webpackChunktourfic"] = self["webpackChunktourfic"] || []).push([["src_jsx_components_Dashboard_Dashboard_AnalyticsDonut_js"],{

/***/ "./src/jsx/components/Dashboard/Dashboard/AnalyticsDonut.js":
/*!******************************************************************!*\
  !*** ./src/jsx/components/Dashboard/Dashboard/AnalyticsDonut.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-chartjs-2'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());



class AnalyticsDonut extends react__WEBPACK_IMPORTED_MODULE_1__.Component {
  render() {
    const data = {
      weight: 0,
      defaultFontFamily: "Poppins",
      datasets: [{
        data: [this.props.value, 100 - this.props.value],
        borderWidth: 0,
        backgroundColor: [this.props.backgroundColor, this.props.backgroundColor2]
      }]
    };
    const options = {
      width: 110,
      cutoutPercentage: 65,
      responsive: false,
      maintainAspectRatio: true,
      tooltips: {
        enabled: false
      },
      hover: {
        mode: null
      }
    };
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "donut1",
      style: {
        marginTop: "-10px"
      }
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'react-chartjs-2'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), {
      data: data,
      options: options,
      height: 150,
      width: 150
    }));
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (AnalyticsDonut);

/***/ })

}]);
//# sourceMappingURL=src_jsx_components_Dashboard_Dashboard_AnalyticsDonut_js.js.map