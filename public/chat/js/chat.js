/**
 *  모바일에서 뒤로가기를 눌렀을 때 채팅 상대 선택 박스의 값을 올바르게 설정한다.
 */
window.onpageshow = function(event) {
	if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
        var selectReceiver = document.getElementById('selectReceiver');
        const qs = getQueryStringArgs();

        var option = null;
        if (qs.hasOwnProperty('newReceiverId')) {
            option = document.getElementById(qs['newReceiverId']);
            if (option) { 
                option.selected = 'selected';
            }
        } else if (qs.hasOwnProperty('receiverId')) {
            option = document.getElementById(qs['receiverId']);
            if (option) { 
                option.selected = 'selected';
            }
        }
        if (!option) {
            selectReceiver.getElementsByTagName('option')[0].selected = 'selected';
        }
	}
}

// URL에서 Query String 추출
function getQueryStringArgs() {
    var qs = (location.search.length > 0 ? location.search.substring(1) : '');
    var args = {};

    var qsSplit = qs.split('&');
    for (let i = 0; i < qsSplit.length; i++) {
        let item = qsSplit[i].split('=');
        let name = decodeURIComponent(item[0]);
        let value = decodeURIComponent(item[1]);
        if (name.length) {
            args[name] = value;
        }
    }

    return args;
}


window.onload = function() {

    /**
     *  새로 온 메세지가 있는지 확인한다.
     */
    process();

    // 새 메시지 또는 가장 최근의 메시지의 위치로 스크롤한다.
    var scrollTo = document.querySelector('#chat_wrap > div.newChat');
    if (scrollTo) { scrollTo.scrollIntoView(); }

    /**
     *  뒤로가기를 눌렀을 때 폼이 재전송되는 것을 방지한다.
     */
    history.pushState(null, null, location.href);
    window.onpopstate = function(event) {
        history.go(1);
    }

    /**
     *  채팅 창의 입력 칸(textarea)의 크기를 동적으로 결정한다.   
     */
    var taFrame = document.getElementById('ta-frame');
    var ta =  document.querySelector('#ta-frame textarea');

    const taDefaultHeight = ta.offsetHeight;
    ta.style.lineHeight = taDefaultHeight  + 'px';

    function autosize() {
      setTimeout(function() {
        ta.style.height = taDefaultHeight + 'px';
        var height = Math.min(Number(taDefaultHeight) * 6, ta.scrollHeight);
        taFrame.style.height = height + 'px';
        ta.style.height = height + 'px';
      }, 0);
    }

    ta.addEventListener('input', autosize);
    window.onresize = autosize;

    /**
     *  이미지 업로드 사용자 화면 처리 
     */     
    let filesList = document.getElementById('files-list');
    filesList.onchange = function(event) {
        let isValid = true;
        let files = event.target.files;

        // 5MB 이하의 이미지 파일 최대 3개까지만 업로드할 수 있도록 설정한다.
        if (files.length == 0) {
            isValid = false;    // 이미지 미리보기를 제공하지 않는다.
        } else if (files.length > 3) {
            isValid = false;
            filesList.value = '';
            alert('이미지 파일은 최대 3개 업로드 가능합니다.');
        } else {
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > 5242880) {
                    isValid = false;
                    filesList.value = '';
                    alert('업로드에 실패했습니다. 파일당 5MB까지 업로드할 수 있습니다.');
                    break;
                }
            }
        }

        // 이미지 미리보기 초기화
        image_preview_reset();

        // 이미지 미리보기 구현
        const imgWrapDiv = document.getElementById('img_wrap');
        const chatContentDiv = document.getElementById('chat_content');
        if (isValid) {  // 이미지 미리보기 존재
            imgWrapDiv.parentNode.setAttribute('class', 'bottom hasImg')
            const imgSizeWrapWidth = (imgWrapDiv.clientWidth - 12) * 0.33334;
            const imgSizeWrapHeight = (imgWrapDiv.clientHeight - 4);

            // 업로드한 이미지 미리보기 기능을 추가한다.
            for (let i = 0; i < files.length; i++) {    
                let imgSizeWrapDiv = document.createElement('div');
                imgSizeWrapDiv.setAttribute('class', 'img_size_wrap');
                imgSizeWrapDiv.style.width = imgSizeWrapWidth + 'px';
                imgSizeWrapDiv.style.height = imgSizeWrapHeight + 'px';

                let imgElem = document.createElement('img');    
                imgElem.setAttribute('src', URL.createObjectURL(files[i]));    
                imgElem.onload = function() {    
                    URL.revokeObjectURL(imgElem.src) // free memory    
                };    

                imgSizeWrapDiv.appendChild(imgElem);
                imgWrapDiv.appendChild(imgSizeWrapDiv);    

                chatContentDiv.style.height = 'calc(100% - 40px - 40px - 120px)';
            }   
        } else {    // 이미지 미리보기가 존재하지 않는 경우
            imgWrapDiv.parentNode.setAttribute('class', 'bottom hidden')
            chatContentDiv.style.height = 'calc(100% - 40px - 40px)';
        }
    }

    /**
     *  채팅 상대를 변경하면 폼을 자동으로 전송한다.
     */
    const selectReceiver = document.getElementById('selectReceiver');
    const selectReceiverForm = document.getElementById('selectReceiverForm');
    selectReceiver.onchange = function() {
        selectReceiverForm.submit();
    }

    /**
     *  채팅 차단 버튼을 클릭한 경우 동작한다. (폼 전송)
     */
    var blockClickImg = document.getElementById('block');
    blockClickImg.onclick = function() {
        const blockFrom = document.getElementById('blockFrom');
        // 채팅 상대가 정해진 경우에만 동작한다.
        if (selectReceiver.value !== '0') {
            const isYes = confirm(selectReceiver.value + '님을 차단하시겠습니까?');
            if (isYes) { blockFrom.submit(); }
        } 
    };

    /**
     *  새로운 메시지가 온 경우 알림창을 띄운다.
     */
    if (selectReceiver.value) {
        let curOption = document.getElementById(selectReceiver.value);
        if (curOption && curOption.getAttribute('class').search(/hasNewMessage/) != -1) {
            alarmDisplay();
        }
    }

};

// 채팅 창을 열어놓은 상태에서 로그아웃 한 경우 처리용
window.onunload = function() {
    localStorage.removeItem('chatWin');
};


$(document).ready(function() {

    setNotification();  // 알림 이미지 설정하기

    // textarea 초기 height 속성 값
    var taDefaultHeight = $('textarea').css('height');

    /**
     *  채팅 창의 입력 칸(textarea)에 focus가 들어간 경우 이미지 미리보기를 hidden으로 설정한다.
     */
    $('textarea').focusin(function() {
        const $divBottom = $('#chat_sendMsg > div.bottom');
        if ($divBottom.hasClass('hasImg')) {
            $divBottom.addClass('hidden'); 
            $('#chat_content').css('height', 'calc(100% - 40px - 40px)');
        }
    }).focusout(function() {
        const $divBottom = $('#chat_sendMsg > div.bottom');
        if ($divBottom.hasClass('hasImg')) {
            $divBottom.removeClass('hidden'); 
            $('#chat_content').css('height', 'calc(100% - 40px - 40px - 120px)');
        }
    });


    /**
     *  채팅 전송 버튼을 클릭한 경우 동작한다.
     */
    $('#submit_chat').click(function() {
        // 채팅 상대가 정해진 경우에만 동작한다.
        if ($('#selectReceiver').val() != '0') {

            // 전송할 메시지(데이터)가 존재하는 경우에만 동작한다.
            if ($('#files-list').val() || $('textarea').val().trim()) {

                // 채팅 상대방 아이디
                var receiverId = null;
                const qs = getQueryStringArgs();
                if (qs.hasOwnProperty('newReceiverId')) {
                    receiverId = qs['newReceiverId'];
                } else {
                    receiverId = qs['receiverId'];
                }

                /**
                 *  Upload files asynchronously using jQuery
                 *  [참고] https://www.geeksforgeeks.org/how-to-upload-files-asynchronously-using-jquery/
                 */
                var fd = new FormData();
                var files = $('#files-list')[0].files;
                for (let i = 0; i < files.length; i++) {
                    fd.append('file_' + i, files[i]);
                }

                // 업로드한 이미지가 존재하는 경우 서버에 이미지를 저장한다.
                $.ajax({
                    url: 'ajax/upload.php',
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        loadingBarStart();
                    },
                    success: function(data, textStatus) {

                        // 서버에 전송할 데이터를 정의한다.
                        var params = {
                            mode: 'sendMessage',
                            id: receiverId,
                            message: $('textarea').val(),
                            imgCount: data.path.length
                        };
                        for (let i = 0; i < data.path.length; i++) {
                            params['img_path_' + i] = data.path[i];
                            params['img_width_' + i] = data.width[i];
                        }

                        $.ajax({
                            url: 'ajax/chat.php',
                            type: 'POST',
                            data: $.param(params),
                            dataType: 'json',
                            error: function(xhr, textStatus, errorThrown) {
                                displayError(textStatus);
                            },
                            success: function(data, textStatus) {
                                
                                /**
                                 *  전송된 채팅을 화면에 표시한다.
                                 */
                                $('#chat_wrap > div.newChat').removeClass('newChat');
                                for (let i = 0; i < data.length; i++) {
                                    if (data[i].type == 'T') {
                                        $('#chat_wrap')
                                            .append(
                                                $('<div class="me ' + (i == 0 ? 'newChat' : '') + '"></div>')
                                                    .append(
                                                        $('<p class="text"></p>')
                                                            .text(data[i].text)
                                                    )
                                                    .append(
                                                        $('<p class="datetime"></p>')
                                                            .html(datetimeFormatted(data[i].time.date))
                                                    )
                                            );
                                    } else {
                                        $('#chat_wrap')
                                            .append(
                                                $('<div class="me ' + (i == 0 ? 'newChat' : '') + '"></div>')
                                                    .append(
                                                        $('<div class="file"></div>')
                                                            .append('<img src="../../file/images/chat/' + data[i].path + '" />')
                                                    )
                                                    .append(
                                                        $('<p class="datetime"></p>')
                                                            .html(datetimeFormatted(data[i].time.date))
                                                    )
                                            )
                                    }
                                }
                                // 전송된 채팅 위치로 화면을 스크롤한다.
                                document.querySelector('#chat_wrap > div.newChat').scrollIntoView();

                                // 텍스트 입력 초기화
                                $('textarea')
                                    .val('')
                                    .css('height', taDefaultHeight);
                            }
                        });

                        // 이미지를 업로드한 경우
                        if (data.path.length > 0) {
                            image_form_preview_reset(); // 이미지 첨부 초기화

                        }
                    },
                    complete: function() {
                        loadingBarEnd();
                    }
                });
            }

        } else {
            // 입력된 값을 리셋한다.
            $('textarea')
                .val('')
                .css('height', taDefaultHeight);      
            image_form_preview_reset();

            alarmDisplay('채팅 상대를 지정해주세요.');
        }
    });


});

/**
 *  이미지 미리보기 초기화 
 */
function image_preview_reset() {
    const imgSizeWrapDivList = document.querySelectorAll('#img_wrap > div.img_size_wrap');
    if (imgSizeWrapDivList) {
        const parentNode = document.getElementById('img_wrap');
        for (let i = 0; i < imgSizeWrapDivList.length; i++) {
            parentNode.removeChild(imgSizeWrapDivList[i]);
        }
    }
}

/**
 *  이미지 첨부 폼과 이미지 미리보기를 모두 초기화
 */
function image_form_preview_reset() {
    $('#files-list').val('');
    image_preview_reset();
    $('#chat_sendMsg > div.bottom').removeClass('hasImg').addClass('hidden')
    $('#chat_content').css('height', 'calc(100% - 40px - 40px)');
}

var debugMode = true;
// function that displays an error message
function displayError(message) {
    // display error message, with more technical details if debugMode is true
    alert("Error accessing the server! " + (debugMode ? message : ""));
}

/** 
 *  날짜 형식 변환하기
 *  2021-11-25 23:08:39.179733 -> 21.11.25<br/>23:08
 */
function datetimeFormatted(dateString) {
    var date = dateString.split(' ')[0];   
    var time = dateString.split(' ')[1];

    date = date.replace(/-/g, '.');
    date = date.substring(2);

    time = time.substring(0, 5);

    return date + '<br/>' + time;
}

/**
 *  알림 이미지 설정하기
 */
function setNotification() {
    $('#notification').removeClass('hidden');
    if (!$('#selectReceiver > option.hasNewMessage').length) {
        $('#notification').addClass('hidden');
    }
}
