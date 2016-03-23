var React = require('react');
var RouterMixin = require('react-mini-router').RouterMixin;

var Homepage = require('./homepage.jsx');
var Resultspage = require('./resultspage.jsx');


var App = React.createClass({

    mixins: [RouterMixin],

    routes: {
        '/': 'home',
        '/results/:postcode/:search': 'results'
    },

    render: function() {
        return this.renderCurrentRoute();
    },

    home: function() {
        return <Homepage/>;
    },

    results: function(postcode, search) {
        return <Resultspage postcode={postcode} search={search}/>;
    }

});

module.exports = App;