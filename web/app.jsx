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
        return <Resultspage postcode={postcode} search={search || ''}/>;
    },

    notFound: function(route) {
        return (<p>"NOT FOUND"</p>);  
    },

    _navigateToResults: function(postcode, search) {
        navigate('/q/' + postcode + '/' + search);
    }


});

module.exports = App;