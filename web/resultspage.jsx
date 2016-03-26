var React = require('react');
var Ajax = require('simple-ajax');

var Resultspage = React.createClass({

    getInitialState: function() {
        return {
            results: null,
            search: this.props.search,
            postcode: this.props.postcode
        };
    },

    componentDidMount: function() {
        this._getResults();
    },

    componentDidUpdate: function() {
        if(this.state.postcode != this.props.postcode || this.state.search != this.props.search) {
            var that = this;
            this.setState({
                postcode: this.props.postcode,
                search: this.props.search,
                results: null
            }, function() {
                that._getResults();
            });
        }
    },

    render: function() {
        var resultsList = this.state.results ? (<ResultsList results={this.state.results}/>) : "loading results...";

        return (
            <div className='resultspage'>
                <h1>
                    Results for "{this.state.search}" near postcode {this.state.postcode}
                </h1>
                {resultsList}
            </div>
        );
    },

    _getResults: function() {

        var that = this;

        var ajax = new Ajax({
            url: '/org/test/JustCraveAPITest.php?postcode=' + this.state.postcode + '&query=' + this.state.search,
            // url: '/org/test/FakeApiEndpoint.php',
            method: 'GET'
        });

        ajax.on('success', function(event) {
            try {
                var response = JSON.parse(event.target.response);
            } catch(err) {
                console.log('bad json convert');
            } finally {
                that.setState({
                    results: response
                });
            }

        });

        ajax.on('error', function(event) {
            console.log('bad ajax');
        });

        ajax.send();
    },


});

var ResultsList = React.createClass({

    render: function() {

        var groupedResults = {};

        for(var key in this.props.results) {
            groupedResults[this.props.results[key].restaurantId] = groupedResults[this.props.results[key].restaurantId] || [];
            groupedResults[this.props.results[key].restaurantId].push(this.props.results[key]);
        }

        var restaurants = Object.keys(groupedResults).map(function(key) {
            return (
                <ResultsListItem items={groupedResults[key]}/>
            );

        });

        return (
            <div class='results'>
                {restaurants}
            </div>
        );
    }

});

var ResultsListItem = React.createClass({

    render: function() {

        var poundSign = '\u00a3';

        var items = this.props.items.map(function(item) {
            return (
                <div className='item'>
                    {item.categoryName} - {item.itemSynonym} {item.itemName}
                    <span className="price">{poundSign}{parseFloat(item.itemPrice).toFixed(2)}</span>
                </div>
            );
        });

        var logostyle = {
            backgroundImage: 'url(' + this.props.items[0].itemLogo + ')'
        };

        return (
            <div className='restaurant'>
                <div className="logo" style={logostyle}></div>
                <div className='info'>
                    <h4 className='restaurantName'>{this.props.items[0].restaurantName}</h4>
                    <small>OPEN NOW - DELIVERY & COLLECTION</small>
                </div>
                <div className='items'>
                    {items}
                </div>

            </div>
        );
    }
});


module.exports = Resultspage;