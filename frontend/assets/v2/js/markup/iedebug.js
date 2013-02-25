// jQuery.XDomainRequest.js
// Author: Jason Moon - @JSONMOON
// IE8+
if (!jQuery.support.cors && window.XDomainRequest) {
  var httpRegEx = /^https?:\/\//i;
  var getOrPostRegEx = /^get|post$/i;
  var sameSchemeRegEx = new RegExp('^'+location.protocol, 'i');
  var jsonRegEx = /\/json/i;
  var xmlRegEx = /\/xml/i;
  
  // ajaxTransport exists in jQuery 1.5+
  jQuery.ajaxTransport('text html xml json', function(options, userOptions, jqXHR){
    // XDomainRequests must be: asynchronous, GET or POST methods, HTTP or HTTPS protocol, and same scheme as calling page
    //! FIXME reimplement for //blah.com url and upstrem id
    //    if (options.crossDomain && options.async && getOrPostRegEx.test(options.type) && httpRegEx.test(userOptions.url) && sameSchemeRegEx.test(userOptions.url)) {

    if (options.crossDomain && options.async && getOrPostRegEx.test(options.type)) {
      var xdr = null;
      var userType = (userOptions.dataType||'').toLowerCase();
      return {
        send: function(headers, complete){
          xdr = new XDomainRequest();
          if (/^\d+$/.test(userOptions.timeout)) {
            xdr.timeout = userOptions.timeout;
          }
          xdr.ontimeout = function(){
            complete(500, 'timeout');
          };
          xdr.onload = function(){
            var allResponseHeaders = 'Content-Length: ' + xdr.responseText.length + '\r\nContent-Type: ' + xdr.contentType;
            var status = {
              code: 200,
              message: 'success'
            };
            var responses = {
              text: xdr.responseText
            };
            /*
            if (userType === 'html') {
              responses.html = xdr.responseText;
            } else
            */
            try {
              if ((userType === 'json') || ((userType !== 'text') && jsonRegEx.test(xdr.contentType))) {
                try {
                  responses.json = $.parseJSON(xdr.responseText);
                } catch(e) {
                  status.code = 500;
                  status.message = 'parseerror';
                  //throw 'Invalid JSON: ' + xdr.responseText;
                }
              } else if ((userType === 'xml') || ((userType !== 'text') && xmlRegEx.test(xdr.contentType))) {
                var doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = false;
                try {
                  doc.loadXML(xdr.responseText);
                } catch(e) {
                  doc = undefined;
                }
                if (!doc || !doc.documentElement || doc.getElementsByTagName('parsererror').length) {
                  status.code = 500;
                  status.message = 'parseerror';
                  throw 'Invalid XML: ' + xdr.responseText;
                }
                responses.xml = doc;
              }
            } catch(parseMessage) {
              throw parseMessage;
            } finally {
              complete(status.code, status.message, responses, allResponseHeaders);
            }
          };
          //! Or it would randomly fail
          xdr.onprogress = function() {};
          xdr.onerror = function(){
            complete(500, 'error', {
              text: xdr.responseText
            });
          };
          var postData = (userOptions.data && $.param(userOptions.data)) || '';
          xdr.open(options.type, options.url);
          xdr.send(postData);
        },
        abort: function(){
          if (xdr) {
            xdr.abort();
          }
        }
      };
    }
  });
}

if ($.browser.msie) (function() {

    var __console, Console;

    Console = function() {
        var check = setInterval(function() {
            var f;
            if (window.console && console.log && !console.__buffer) {
                clearInterval(check);
                f = (Function.prototype.bind) ? Function.prototype.bind.call(console.log, console) : console.log;
                for (var i = 0; i < __console.__buffer.length; i++) f.apply(console, __console.__buffer[i]);
            }
        }, 1000);

        function log() {
            this.__buffer.push(arguments);
        }

        this.log = log;
        this.error = log;
        this.warn = log;
        this.info = log;
        this.time = log;
        this.timeEnd = log;
        this.__buffer = [];
    };

    __console = window.console = new Console();
})();