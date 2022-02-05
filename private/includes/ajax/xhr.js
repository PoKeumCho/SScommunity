// XmlHTTP constructor can receive request settings:
// url              : the server url
// contentType      : request content type (클라이언트가 서버로 전송할 데이터의 타입 지정)
// type             : request type (default is GET)
// data             : optional request parameters (형식: key1=value1&key2=value2&...)
// async            : whether the request is asynchronous (default is true)
// showErrors       : display errors
// complete         : the callback function to call when the request completes 
//                  : ex) complete: function(xhr, response, status) { /* body */ }
// beforeSend       : the callback function (로딩 창 구현을 위해 추가한 콜백함수)
function XmlHttp(settings) {
    // store the settings object
    this.settings = settings;

    /* override default settings with those received as parameter */
    // the default url points to the current page
    var url = location.href;
    if (settings.url) { url = settings.url; }

    // the default contentType is the content type for forms
    var contentType = "application/x-www-form-urlencoded";
    if (settings.contentType) { contentType = settings.contentType; }

    // by default the request is done through GET
    var type = "GET";
    if (settings.type) { type = settings.type; }

    // by default there are no parameters sent
    var data = null;
    if (settings.data) { 
        data = settings.data; 
        // if we go through GET we properly adjust the URL
        if (type == "GET") {
            url = url + "?" + data;
        }
    }

    // by default the postback is asynchronous
    var async = true;
    if (settings.async) { async = settings.async; }

    // by default we show all the infrastructure errors
    var showErrors = true;
    if (settings.showErrors) { showErrors = settings.showErrors; }

    // create the XMLHttpRequest object
    var xhr = XmlHttp.create();

    // set the postback properties
    // only continue if we have a valid xhr object
    if (xhr) {
        xhr.open(type, url, async);
        xhr.onreadystatechange = onreadystatechange;
        xhr.setRequestHeader = ("Content-Type", contentType);
        // call the callback function if any
        if (settings.beforeSend) {
            settings.beforeSend();
        }
        xhr.send(data);
    }

    /**
     *  the function that display errors
     */
    function displayError(message) {
        // ignore errors if showErrors is false
        if (showErrors) {
            // display error message
            alert("Error encountered:\n" + message);
        }
    }

    /**
     *  the function that reads the server response
     */
    function readResponse() {
        var response = null;
        try {
            // retrieve the response content type
            var contentType = xhr.getResponseHeader("Content-Type");
            // build the json object if the response has one
            if (contentType == "application/json") {
                response = JSON.parse(xhr.responseText);
            }
            // get the DOM element if the response is XML
            else if (contentType == "text/xml") {
                response = xhr.responseXML;
            }
            // by default get the response as text
            else {
                response = xhr.responseText;
            }
            // call the callback function if any
            if (settings.complete) {
                settings.complete(xhr, response, xhr.status);
            }
        } catch (e) {
            displayError(e.toString());
        }
    }

    /**
     *  called when the request state changes
     */
    function onreadystatechange() {
        // when readyState is 4, we read the server response
        if (xhr.readyState == 4) {
            // continue only if HTTP status is "OK"
            if (xhr.status == 200) {
                try {
                    // read the response from the server
                    readResponse();
                } catch (e) {
                    displayError(e.toString());
                }
            }
            // a HTTP status different than 200 signals an error
            else {
                displayError(xhr.statusText);
            }
        }
    }
}

// static method that returns a new XMLHttpRequest object
XmlHttp.create = function() {
    // will store the reference to the XMLHttpRequest object
    var xmlHttp;
    // create the XMLHttpRequest object
    try {
        // assume IE7 or newer or other modern browsers
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        // assume IE6 or older
        var XmlHttpVersion = new Array(
            'MSXML2.XMLHTTP.6.0',
            'MSXML2.XMLHTTP.5.0',
            'MSXML2.XMLHTTP.4.0',
            'MSXML2.XMLHTTP.3.0',
            'MSXML2.XMLHTTP',
            'Microsoft.XMLHTTP'                            
        );
        // try every prog id until one works
        for (var i = 0; i < XmlHttpVersion.length && !xmlHttp; i++) {
            try {
                // try to create XMLHttpRequest object
                xmlHttp = new ActiveXObject(XmlHttpVersion[i]);
            } catch (e) { } // ignore potential   
        }
    }

    // return the created object or display an error message
    if (!xmlHttp) {
        alert("Error creating the XMLHttpRequest object.");
    }
    else {
        return xmlHttp;
    }
}
