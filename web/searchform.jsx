var React = require('react');
var api = require('./api.js');
var SearchBox = require('./searchbox.jsx');

var SearchForm = React.createClass({

    getInitialState: function() {
        return {
            postcode: this.props.postcode || '',
            query: this.props.query || '',
            queryPredictions: []
        };
    },

    render: function() {
        return (
            <form onSubmit={this._onSubmit} className='searchform'>
                <div className='postcode'>
                    <input
                        type='text'
                        placeholder='Postcode'
                        value={this.state.postcode}
                        onChange={this._onPostcodeChange}
                    />
                </div>

                <div className='query'>
                    <SearchBox
                        type='text'
                        placeholder='Search'
                        value={this.state.query}
                        onChange={this._onSearchChange}
                        suggestions={this.state.queryPredictions}
                        onSuggestionsUpdateRequested={this._onSuggestionsUpdateRequested}
                    />
                </div>
                <div className='submit-button'>
                    <button className="button" type="submit">Search</button>
                </div>
            </form>
        );
    },

    _onPostcodeChange: function(event) {
        if(/^[a-z0-9 ]*$/i.test(event.target.value) && event.target.value.length <= 8){
            this.setState({postcode: event.target.value.toUpperCase()}); 
        }
    },

    _onSearchChange: function(event, data) {
        this.setState({query: data.newValue});   
    },

    _onSuggestionsUpdateRequested: function(data) {
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
        this.props.onSubmit(this.state.postcode, this.state.query);
    }

});

module.exports = SearchForm;