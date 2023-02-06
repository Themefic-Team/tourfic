import { HashRouter} from "react-router-dom";

/**
 * attach react with any element using withReactMount
 * @param {host} string dom selector
 * @param {Component} ReactComponent
 */ 
export default function withReactMount(host, Component) {
    document.addEventListener('DOMContentLoaded', function() {
        
        const element = document.querySelector(host);

        if(typeof element !== 'undefined' && element !== null) {
            ReactDOM.render(
            <HashRouter>
                <Component/>
            </HashRouter>,
            element);
        }

    });   
}