import { render } from '@wordpress/element';
import { BrowserRouter } from 'react-router-dom';
import SmartSaleRanker from "./admin/smartsaleranker";

/**
 * Import the stylesheet for the plugin.
 */
import './style/main.scss';

const App = () => (
    <div>
        <BrowserRouter>
        <SmartSaleRanker />
        </BrowserRouter>
    </div>
);

const element = document.getElementById('smart-sale-ranker-admin');
if (element) {
    render(<App />, element);
}

