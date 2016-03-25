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

    componentWillReceiveProps: function(newProps) {
        if(this.state.postcode != newProps.postcode || this.state.search != newProps.search) {
            this.setState({
                postcode: newProps.postcode,
                search: newProps.search
            });
            this._getResults();
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
                that.setState({
                    results: response
                });
            } catch(err) {
                console.log('bad json convert');
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