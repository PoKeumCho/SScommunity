// stores the reference to the XMLHttpRequest object
var xmlHttp = createXmlHttpRequestObject();

// retrieves the XMLHttpRequest object
function createXmlHttpRequestObject() {
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
            } catch (e) {} // ignore potential
        }
    }

    // return the created object or display an error message
    if (!xmlHttp) {
        alert('Error creating the XMLHttpRequest object.');
    } else {
        return xmlHttp;
    }
}

// make asynchronous HTTP request using the XMLHttpRequest object
function process() {
    // proceed only if the xmlHttp object isn't busy
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        const selectReceiver = document.getElementById('selectReceiver');
        var data = 'mode=checkNewMessage';
        if (selectReceiver.value !== '0') {
            data += '&id=' + encodeURIComponent(selectReceiver.value);
        }

        // execute the ajax/chat.php page from the server
        xmlHttp.open('GET', 'ajax/chat.php?' + data, true);
        // define the method to handle server responses
        xmlHttp.onreadystatechange = handleServerResponse;
        // make the server request
        xmlHttp.send(null);
    }
    // if the connection is busy, try again after one second
    else {
        setTimeout('process()', 1000);
    }
}

// callback function executed when a message is received from the server
function handleServerResponse() {
    // move forward only if the transaction has completed
    if (xmlHttp.readyState == 4) {
        // status of 200 indicates the transaction completed successfully
        if (xmlHttp.status == 200) {
            // read the message from the server
            responseJSON = JSON.parse(xmlHttp.responseText);

            // ?????? ?????? ???????????? ??????
            var opponentOpts = document.querySelectorAll('#selectReceiver > option');
            var opponentList = [];
            for (let i = 1; i < opponentOpts.length; i++) {
                opponentList[i-1] = opponentOpts[i].id; 
            }

            const selectReceiver = document.getElementById('selectReceiver');

            for (let i = 0; i < responseJSON.senderList.length; i++) {
                // ?????? ?????? ???????????? ???????????? ?????? ??????
                if (opponentList.indexOf(responseJSON.senderList[i]) === -1) {
                    $('#selectReceiver')
                        .append(
                            $('<option id="' + responseJSON.senderList[i] 
                                + '" value="' + responseJSON.senderList[i]
                                + '" class="hasNewMessage"></option>')
                                .html(responseJSON.senderList[i] + ' &#128276;')
                        )
                } 
                // ?????? ?????? ???????????? ???????????? ??????
                else {
                    let $opponentOpt = $('#' + responseJSON.senderList[i]);
                    if (!$opponentOpt.hasClass('hasNewMessage') &&
                            (selectReceiver.value != responseJSON.senderList[i])) {
                        $opponentOpt
                            .addClass('hasNewMessage')
                            .html(responseJSON.senderList[i] + ' &#128276;');
                    }
                }
            }

            // ?????? ???????????? ????????????.
            if (responseJSON.senderList.length) { setNotification(); }


            /**
             * ==========================================================================
             *  ?????? ?????? ??????????????? ?????? ????????? ???????????? ????????????.
             * ==========================================================================
             */    

            /**
             *  ????????? ???????????? ?????? ????????? ???????????? ???????????? ?????? ?????? ???????????? ??????.
             *  ????????? ?????? ?????? ???????????? ????????? 2??? ???????????? ?????? ???????????? ?????????,
             *  ?????? ?????? ??? ??? ????????? ???????????? ???????????? no(?????? ???)??? ????????? ???????????? ????????? ????????????.
             */
            var chatDiv = document.querySelectorAll('#chat_wrap > div');
            if (chatDiv) {
                var chatNoList = [];
                for (let i = 0; i < chatDiv.length; i++) {
                    chatNoList[i] = chatDiv[i].dataset.chatNo; 
                }
            }

            if (responseJSON.messageList && responseJSON.messageList.length) {
                $('#chat_wrap > div.newChat').removeClass('newChat');
                for (let i = 0; i < responseJSON.messageList.length; i++) {
                    if (selectReceiver.value == responseJSON.messageList[i].senderid) {

                        // ?????? ????????? ????????????.
                        if (chatNoList.indexOf(responseJSON.messageList[i].no) !== -1) { break; }

                        if (responseJSON.messageList[i].contenttype == 'T') {
                            $('#chat_wrap')
                                .append(
                                    $('<div class="other ' + (i == 0 ? 'newChat' : '') + '"></div>')
                                        .append(
                                            $('<p class="text"></p>')
                                                .text(responseJSON.messageList[i].text)
                                        )
                                        .append(
                                            $('<p class="datetime"></p>')
                                                .html(datetimeFormatted(responseJSON.messageList[i].datetime))
                                        )
                                );
                        } else {
                            $('#chat_wrap')
                                .append(
                                    $('<div class="other ' + (i == 0 ? 'newChat' : '') + '"></div>')
                                        .append(
                                            $('<div class="file"></div>')
                                                .append('<img src="../../file/images/chat/' 
                                                    + responseJSON.messageList[i].path + '" />')
                                        )
                                        .append(
                                            $('<p class="datetime"></p>')
                                                .html(datetimeFormatted(responseJSON.messageList[i].datetime))
                                        )
                                )
                        } 
                    } 
                }
                var scrollTo = document.querySelector('#chat_wrap > div.newChat');
                if (scrollTo) { scrollTo.scrollIntoView(); }
            }

            // restart sequence
            setTimeout('process()', 1000);
        }
        // a HTTP status different than 200 signals an error
        else {
            alert(
                'There was a problem accessing the server: ' +
                    xmlHttp.statusText
            );
        }
    }
}
