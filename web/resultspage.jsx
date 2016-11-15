var React = require('react');
var api = require('./api.js');

var mode = {
    SUCCESS:1, FAIL:2, PENDING:3
}

var Resultspage = React.createClass({

    getInitialState: function() {
        return {
            mode: mode.PENDING,
            results: null,
            categories: null,
            synonyms: null,
            selectedCategories: [],
            selectedSynonyms: [],
            search: this.props.search,
            postcode: this.props.postcode,

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
                results: null,
                mode: mode.PENDING
            }, function() {
                that._getResults();
            });
        }
    },

    render: function() {
        var message;
        var resultsList;
        var filters;

        switch(this.state.mode) {
            
            case mode.PENDING:
                message = 'Loading results...';
                resultsList = null;
                filters = null;
                break;

            case mode.FAIL:
                message = ':( Sorry we couldn\'t load the results';
                resultsList = null;
                filters = null;
                break;

            case mode.SUCCESS:
                message = 'Found ' + this.state.results.length + ' results for "' + this.state.search + '" near ' + this.state.postcode + '.' ;
                resultsList = (
                    <ResultsList
                        results={this.state.results}
                        selectedCategories={this.state.selectedCategories}
                        selectedSynonyms={this.state.selectedSynonyms}
                        />
                );
                filters = (
                    <Filters
                        categories={this.state.categories}
                        synonyms={this.state.synonyms}
                        selectedCategories={this.state.selectedCategories}
                        selectedSynonyms={this.state.selectedSynonyms}
                        onSelectCategory={this._selectCategory}
                        onSelectSynonym={this._selectSynonym}
                        />
                );
                break
        }

        return (
            <div className='resultspage'>                
                <div className='container'>
                    {message}
                    {filters}
                    {resultsList}
                </div>
            </div>
        );
    },

    _getResults: function() {
        var that = this;
        api.search(this.state.postcode, this.state.search, function(err, response) {
            if(!err) {
                that.setState({
                    results: response.search_results,
                    categories: response.common_categories,
                    synonyms: response.common_synonyms,
                    selectedCategories: [],
                    selectedSynonyms: [],
                    mode: mode.SUCCESS
                });
            } else {
                that.setState({
                    mode: mode.FAIL
                })
            }
        });
    },   

    _search: function() {

    },

    _selectSynonym: function(id) {
        var that = this;
        
        this.setState(function(currentState) {
            var index = currentState.selectedSynonyms.indexOf(id);
            if(index > -1) return { selectedSynonyms: currentState.selectedSynonyms.slice(0, index).concat(currentState.selectedSynonyms.slice(index+1)) };
            else return { selectedSynonyms: currentState.selectedSynonyms.concat(id) };
        });
    },

    _selectCategory: function(id) {
        var that = this;
        
        this.setState(function(currentState) {
            var index = currentState.selectedCategories.indexOf(id);
            if(index > -1) return { selectedCategories: currentState.selectedCategories.slice(0, index).concat(currentState.selectedCategories.slice(index+1)) };
            else return { selectedCategories: currentState.selectedCategories.concat(id) };
        });
    }


});

var Filters = React.createClass({

    render: function() {

        var that = this;

        var categories = this.props.categories
        .sort(function(a, b) {
            return b.count - a.count;
        })
        .map(function(category) {
            
            var checked = that.props.selectedCategories.indexOf(category.id) != -1;

            return (
                <div className='filter' onClick={()=>that.props.onSelectCategory(category.id)}>
                    <input type='checkbox' checked={checked}/> {category.name} <small>({category.count})</small>
                </div>
            );

        });

        var synonyms = this.props.synonyms
        .sort(function(a, b) {
            return b.count - a.count;
        })
        .map(function(synonym) {
            
            var checked = that.props.selectedSynonyms.indexOf(synonym.id) != -1;

            return (
                <div className='filter' onClick={()=>that.props.onSelectSynonym(synonym.id)}>
                    <input type='checkbox' checked={checked}/> {synonym.name} <small>({synonym.count})</small>
                </div>
            );

        });

        return (
            <div className='filters'>
                <div>Categories</div>
                <div>{categories}</div>
                <br />
                <div>Filters</div>
                <div>{synonyms}</div>
            </div>
        );

    }

});

var ResultsList = React.createClass({

    render: function() {

        var that = this;
        var groupedResults = {};

        var filteredResults = this.props.results.filter(function(result) {
            console.log('test')
            if(that.props.selectedCategories.length) {
                if(that.props.selectedCategories.indexOf(result.friendlyCategoryId) == -1) return false;
            }
            if(that.props.selectedSynonyms.length) {
                if(that.props.selectedSynonyms.indexOf(result.friendlySynonymId) == -1) return false;
            }
            return true;
        });

        for(var key in filteredResults) {
            groupedResults[filteredResults[key].restaurantId] = groupedResults[filteredResults[key].restaurantId] || [];
            groupedResults[filteredResults[key].restaurantId].push(filteredResults[key]);
        }

        var restaurants = Object.keys(groupedResults)
            .map(function(key) {
            return (
                <ResultsListItem key={key} items={groupedResults[key]}/>
            );

        });

        return (
            <div className='results'>
                {restaurants}
            </div>
        );
    }

});

var ResultsListItem = React.createClass({

    render: function() {

        var poundSign = '\u00a3';

        var categoriesDictionary = this.props.items.reduce(function(categories, item) {
            categories[item.categoryId] = categories[item.categoryId] || { name: item.categoryName, items: [] };
            categories[item.categoryId].items.push(item);
            return categories;
        }, {});

        var categories = Object.keys(categoriesDictionary).map(function(key) {
            
            var groupsDictionary = categoriesDictionary[key].items.reduce(function(groups, item) {
                groups[item.itemName] = groups[item.itemName] || { name: item.itemName, items: [] };
                groups[item.itemName].items.push(item);
                return groups;
            }, {});

            var groups = Object.keys(groupsDictionary).map(function(key) {
                
                var items = groupsDictionary[key].items.map(function(item) {
                    return (
                        <div key={item.itemId} className='item'>
                            {item.itemSynonym}
                            <span className="price">{poundSign}{parseFloat(item.itemPrice).toFixed(2)}</span>
                        </div>
                    );
                });

                if(groupsDictionary[key].items.length == 1 && !groupsDictionary[key].items[0].itemSynonym) {
                    return (
                        <div className='group'>
                            <div className='group-name'>
                                {groupsDictionary[key].name}
                                <span className="price">{poundSign}{parseFloat(groupsDictionary[key].items[0].itemPrice).toFixed(2)}</span>                    
                            </div>
                        </div>
                    );
                }

                return (
                    <div className='group'>
                        <div className='group-name'>
                            {groupsDictionary[key].name}
                        </div>
                        <div className='group-items'>
                            {items}
                        </div>
                    </div>
                );
            })


            return (
                <div className='category'>
                    <div className='category-name'>
                        {categoriesDictionary[key].name}
                    </div>
                    <div className='category-items'>
                        {groups}
                    </div>
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
                    {categories}
                </div>

            </div>
        );
    }
});


module.exports = Resultspage;