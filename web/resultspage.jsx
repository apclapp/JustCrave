var React = require('react');

var Resultspage = React.createClass({

    render: function() {
        return (
            <div>
                <h1>Search Results</h1>
                <p>Postcode= {this.props.postcode}</p>
                <p>Search term= {this.props.search}</p>
            </div>
        );
    }

});

module.exports = Resultspage;