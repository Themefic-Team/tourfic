import { HashRouter,BrowserRouter } from "react-router-dom";
import { Provider } from "react-redux";
import { store } from "./store/store";
import reportWebVitals from "./reportWebVitals";
import SimpleReactLightbox from "simple-react-lightbox";
import ThemeContext from "./context/ThemeContext";
/**
 * attach react with any element using withReactMount
 * @param {host} string dom selector
 * @param {Component} ReactComponent
 */
export default function withReactMount(host, Component) {
  document.addEventListener("DOMContentLoaded", function () {
    const element = document.querySelector(host);

    if (typeof element !== "undefined" && element !== null) {
      ReactDOM.render(
        <Provider store={store}>
          <SimpleReactLightbox>
            <BrowserRouter>
              <ThemeContext>
                <Component />
              </ThemeContext>
            </BrowserRouter>
          </SimpleReactLightbox>
        </Provider>,
        element
      );
    }
  });
}
