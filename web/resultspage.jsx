var React = require('react');
var Ajax = require('simple-ajax');

var Resultspage = React.createClass({

    getInitialState: function() {
        return {
            results: null
        };
    },

    componentDidMount: function() {
        var that = this;

        if(!this.state.results) {
            this._getResults(this.state.postcode, this.state.search, function(response) {
                that.setState({
                    results: response
                });
            });
        }
    },

    render: function() {
            
        if(this.state.results) {
            return (
                <div className='resultspage'>
                    <h1>Search Results</h1>
                    <p>Postcode= {this.props.postcode}</p>
                    <p>Search term= {this.props.search}</p>
                    <ResultsList results={this.state.results}/>
                </div>
            );            
        } else {
            return (
                <div className='resultspage'>
                    <h1>Search Results</h1>
                    <p>Postcode= {this.props.postcode}</p>
                    <p>Search term= {this.props.search}</p>
                    loading results...
                </div>
            );
        }

    },

    _getResults: function(postcode, query, callback) {

        var ajax = new Ajax({
            url: '/org/test/FakeApiEndpoint.php?postcode=' + postcode + '&query=' + query,
            method: 'GET'
        });

        ajax.on('success', function(event) {
            try {
                var response = JSON.parse(event.target.response);
                callback(response);
            } catch(err) {
                callback(new Error(event.target.response));
            }

        });

        ajax.on('error', function(event) {
            callback(new Error('Bad response'));
        });

        ajax.send();
    },


});

var ResultsList = React.createClass({

    render: function() {
        var results = this.props.results.map(function(result) {
            return (
                <ResultsListItem result={result} />
            );
        })

        return (
            <table class="u-full-width">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>CategoryID</th>
                        <th>RestaurantID</th>
                        <th>Synonym</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Restaurant</th>
                    </tr>
                </thead>
                <tbody>
                    {results}
                </tbody>
            </table>
        );
    }

});

var ResultsListItem = React.createClass({

    render: function() {
        return (
            <tr>
                <td>{this.props.result.itemId}</td>
                <td>{this.props.result.itemName}</td>
                <td>{this.props.result.categoryId}</td>
                <td>{this.props.result.restaurantId}</td>
                <td>{this.props.result.itemSynonym}</td>
                <td>{this.props.result.itemDescription}</td>
                <td>{this.props.result.itemPrice}</td>
                <td>{this.props.result.categoryName}</td>
                <td>{this.props.result.restaurantName}</td>
            </tr>
        );
    }
});


module.exports = Resultspage;