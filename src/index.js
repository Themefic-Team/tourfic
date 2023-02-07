import withReactMount from './utils/withReactMount';

/**
 * React dashboard initial component
 */
function Index() {
    return (
        <div className="hello">
            <h1>Hello hvjhgkgb World</h1>
        </div>
    )
}

// call some api
withReactMount('#tf-dashboard', Index);