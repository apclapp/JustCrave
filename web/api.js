var Ajax = require('simple-ajax');

autofillrequest = null;

module.exports = {

    search: function(postcode, query, callback) {
        var response;
        var ajax = new Ajax({
            url: '/org/test/JustCraveAPITest.php?postcode=' + encodeURIComponent(postcode) + '&query=' + encodeURIComponent(query),
            method: 'GET',
            timeout: 15 * 60 * 1000 // 15 minutes
        });

        ajax.on('success', function(event) {
            try {
                response = JSON.parse(event.target.response);
            } catch(err) {
                console.log('bad json convert');
                callback(err);
            } finally {
                callback(null, response);
            }

        });

        ajax.on('error', function(event) {
            console.log('bad ajax');
            callback(event);
        });

        ajax.send();
    },

    autofillquery: function(query, callback) {

        if(autofillrequest) autofillrequest.abort();

        var response;
        var ajax = new Ajax({
            url: '/org/test/JustCraveApiSuggestionTest.php?query=' + encodeURIComponent(query),
            method: 'GET'
        });

        ajax.on('success', function(event) {
            try {
                response = JSON.parse(event.target.response);
            } catch(err) {
                console.log('bad json convert');
                callback(err);
            } finally {
                callback(null, response);
            }

        });

        ajax.on('error', function(event) {
            console.log('bad ajax');
            callback(event);
        });

        ajax.send();
        autofillrequest = ajax.request;
    }

};