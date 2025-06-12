<?php
/**
 * Template Name: World Markets Dashboard
 */

if (!defined('ABSPATH')) exit;

get_header(); 
?>

<div class="wrap">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <h1>Live World Markets</h1>
            <div id="world-markets-root">Loading markets...</div>
        </main>
    </div>
</div>

<style>
.market-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.market-grid {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

@media (min-width: 768px) {
    .market-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.market-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.market-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.market-name {
    font-weight: 500;
}

.price-up { color: #22c55e; }
.price-down { color: #ef4444; }

.loading {
    text-align: center;
    padding: 20px;
    color: #666;
}

.error {
    color: #ef4444;
    text-align: center;
    padding: 20px;
    background: #fee2e2;
    border-radius: 8px;
}

.last-update {
    text-align: center;
    color: #666;
    margin: 10px 0 20px;
    font-size: 0.9rem;
}
</style>

<?php
wp_enqueue_script('react', 'https://unpkg.com/react@18.2.0/umd/react.production.min.js', array(), null, true);
wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18.2.0/umd/react-dom.production.min.js', array('react'), null, true);

wp_localize_script('react', 'wpSettings', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('market_data_nonce')
));
?>

<script>
console.log('Script starting...');

const MarketDashboard = () => {
    // Initialize all state variables
    const [marketData, setMarketData] = React.useState(null);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [lastUpdate, setLastUpdate] = React.useState(null);

    const fetchData = async () => {
        try {
            console.log('Fetching data...');
            
            const formData = new FormData();
            formData.append('action', 'fetch_market_data');
            formData.append('nonce', wpSettings.nonce);

            const response = await fetch(wpSettings.ajaxurl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('Data received:', result);

            if (result.success && result.data?.quotes) {
                setMarketData(result.data.quotes);
                setLastUpdate(new Date());
                setError(null);
            } else {
                throw new Error('Invalid data received');
            }
        } catch (err) {
            console.error('Error:', err);
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    React.useEffect(() => {
        console.log('useEffect running...');
        fetchData();
        const interval = setInterval(fetchData, 30000);
        return () => clearInterval(interval);
    }, []);

    if (loading && !marketData) {
        return React.createElement('div', { className: 'loading' }, 'Loading market data...');
    }

    if (error) {
        return React.createElement('div', { className: 'error' }, 'Error: ' + error);
    }

    if (!marketData) {
        return React.createElement('div', { className: 'error' }, 'No data available');
    }

    return React.createElement('div', { className: 'market-container' },
        // Last update time
        React.createElement('div', { className: 'last-update' },
            'Last Updated: ', lastUpdate?.toLocaleTimeString()
        ),
        // Market grid
        React.createElement('div', { className: 'market-grid' },
            Object.entries(marketData).map(([region, markets]) =>
                React.createElement('div', { key: region, className: 'market-card' },
                    React.createElement('h2', null, 
                        region.charAt(0).toUpperCase() + region.slice(1)
                    ),
                    Array.isArray(markets) && markets.map(market =>
                        React.createElement('div', { 
                            key: market.symbol,
                            className: 'market-item'
                        },
                            React.createElement('div', { className: 'market-name' }, 
                                market.name
                            ),
                            React.createElement('div', null,
                                React.createElement('div', null, 
                                    market.price?.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })
                                ),
                                React.createElement('div', {
                                    className: (market.change >= 0) ? 'price-up' : 'price-down'
                                },
                                    (market.change >= 0 ? '+' : ''),
                                    market.change?.toFixed(2),
                                    '%'
                                )
                            )
                        )
                    )
                )
            )
        )
    );
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing app...');
    const container = document.getElementById('world-markets-root');
    if (container && React && ReactDOM) {
        const root = ReactDOM.createRoot(container);
        root.render(React.createElement(MarketDashboard));
        console.log('App initialized');
    } else {
        console.error('Missing required elements or libraries');
    }
});
</script>

<?php get_footer(); ?>