var React = require('react');
var RouterMixin = require('react-mini-router').RouterMixin;
var navigate = require('react-mini-router').navigate;

var Homepage = require('./homepage.jsx');
var Resultspage = require('./resultspage.jsx');


var App = React.createClass({

    mixins: [RouterMixin],

    routes: {
        '/': 'home',
        '/q/:postcode/:search': 'results'
    },

    render: function() {
        return this.renderCurrentRoute();
    },

    home: function() {
        return <Homepage onSubmit={this._navigateToResults}/>;
    },

    results: function(postcode, search) {
        return <Resultspage postcode={postcode} search={search}/>;
    },

    notFound: function(route) {
        return (
            <div>
                {route} could not be found. Click <a href='/'>here</a> to go to the homepage.
            </div>
        );
    },

    _navigateToResults: function(postcode, search) {
        navigate('/q/' + postcode + '/' + search);
    }


});

module.exports = App;