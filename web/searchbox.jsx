var React = require('react');
var Autosuggest = require('react-autosuggest');

function getSuggestionValue(suggestion) { // when suggestion selected, this function tells
    return suggestion;                 // what should be the value of the input
}

function renderSuggestion(suggestion) {
    return (
        <span>{suggestion}</span>
    );
}

var SearchBox = React.createClass({

    render: function() {
        return (
            <Autosuggest
                suggestions={this.props.suggestions || []}
                onSuggestionsUpdateRequested={this.props.onSuggestionsUpdateRequested}
                getSuggestionValue={getSuggestionValue}
                renderSuggestion={renderSuggestion}
                inputProps={{
                        placeholder: this.props.placeholder || 'Search',
                        value: this.props.value,
                        onChange: this.props.onChange,
                        className: this.props.className
                }}
            />
        );
    }

});

module.exports= SearchBox;