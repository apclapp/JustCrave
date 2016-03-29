var React = require('react');
var RouterMixin = require('react-mini-router').RouterMixin;
var navigate = require('react-mini-router').navigate;

var Resultspage = require('./resultspage.jsx');
var SearchForm = require('./searchform.jsx');


var App = React.createClass({

    mixins: [RouterMixin],

    routes: {
        '/': 'home',
        '/q/:postcode/:query': 'results'
    },

    render: function() {
        return this.renderCurrentRoute();
    },

    home: function() {
        return this._renderPage('', '', null);
    },

    results: function(postcode, query) {
        return this._renderPage(postcode, query, (
            <Resultspage postcode={postcode} search={query}/>
        ));
    },

    notFound: function(route) {
        return (<p>"NOT FOUND"</p>);  
    },

    _renderPage: function(postcode, query, children) {

        return (
            <div className='page'>
                <div className='banner'>
                    <div className='container'>
                    <div className='title'>Just Crave</div>
                        <SearchForm onSubmit={this._navigateToResults} postcode={postcode} query={query} />
                    </div>
                </div>
                {children}
            </div>
        );

    },

    _navigateToResults: function(postcode, query) {
        navigate('/q/' + postcode + '/' + query);
    }


});

module.exports = App;