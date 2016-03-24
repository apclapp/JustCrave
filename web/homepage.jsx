var React = require('react');

var Homepage = React.createClass({

    getInitialState: function() {
        return {
            postcode: '',
            search: ''  
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
                            <input
                                className="u-full-width"
                                type='text'
                                placeholder='search'
                                value={this.state.search}
                                onChange={this._onSearchChange}
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

    _onPostcodeChange: function(event, value) {
        this.setState({postcode: event.target.value}); 
    },

    _onSearchChange: function(event, value) {
        this.setState({search: event.target.value});
    },

    _onSubmit: function(event) {
        var that = this;
        event.preventDefault();
        this.props.onSubmit(this.state.postcode, this.state.search);
    }

});

module.exports = Homepage;