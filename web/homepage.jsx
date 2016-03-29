var React = require('react');
var api = require('./api.js');
var SearchBox = require('./searchbox.jsx');

var Homepage = React.createClass({

    getInitialState: function() {
        return {
            postcode: '',
            search: '',
            queryPredictions: []
        };
    },

    render: function() {
        return (
            <div className='homepage'>
                <h1>Homepage</h1>

                <form onSubmit={this._onSubmit}>
                    <div className="row">
                        <div className="six columns">
                            <label>Postcode</label>
                            <input
                                className="u-full-width"
                                type='text'
                                placeholder='postcode'
                                value={this.state.postcode}
                                onChange={this._onPostcodeChange}
                            />
                        </div>

                        <div className="six columns">
                            <label>Search</label>
                            <SearchBox
                                className="u-full-width"
                                type='text'
                                placeholder='search'
                                value={this.state.search}
                                onChange={this._onSearchChange}
                                suggestions={this.state.queryPredictions}
                                onSuggestionsUpdateRequested={this._onSuggestionsUpdateRequested}
                            />
                        </div>
                    </div>


                    <div className="row">
                        <button className="button-primary" type="submit">Search</button>
                    </div>

                </form>
            </div>
        );
    },

    _onPostcodeChange: function(event) {
        if(/^[a-z0-9 ]*$/i.test(event.target.value) && event.target.value.length < 8){
            this.setState({postcode: event.target.value.toUpperCase()}); 
        }
    },

    _onSearchChange: function(event, data) {
        console.log('onchange');
        this.setState({search: data.newValue});   
    },

    _onSuggestionsUpdateRequested: function(data) {
        console.log('suggestions update needed');

        this.setState({
            queryPredictions: []
        });

        var that = this;
        api.autofillquery(data.value, function(err, response) {
            console.log(response);

            that.setState({
                queryPredictions: response
            });

        });
    },

    _onSubmit: function(event) {
        var that = this;
        event.preventDefault();
        this.props.onSubmit(this.state.postcode, this.state.search);
    }

});

module.exports = Homepage;