import {Routes, Route, Link} from 'react-router-dom';
import withReactMount from './utils/withReactMount';

/**
 * React dashboard initial component
 */
function Dashboard() {
    return (
        <div className="hello">
            <h1>Hello World</h1>
        </div>
    )
}

// call some api
withReactMount('#tf-dashboard', Dashboard);