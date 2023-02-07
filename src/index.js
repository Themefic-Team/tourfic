import { lazy, Suspense, useEffect } from "react";

/// Components
import JsxIndex from "./jsx";
import { connect, useDispatch } from "react-redux";
import { Route, Switch, withRouter } from "react-router-dom";
// action
import { checkAutoLogin } from "./services/AuthService";
import { isAuthenticated } from "./store/selectors/AuthSelectors";
/// Style
import "./vendor/bootstrap-select/dist/css/bootstrap-select.min.css";
import "./vendor/datatables/css/dataTables.min.css";
import "./css/style.css";

/**
 * React dashboard initial component
 */
function Index() {
  return (
    <>
      <JsxIndex />
    </>
  );
}

// call some api
withReactMount("#tf-dashboard", Index);
