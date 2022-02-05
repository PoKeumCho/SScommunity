/**
 *  화면에 알림창을 추가한다.
 */
function alarmDisplay(message) {

    // 뒷 배경을 감싸는 커버
    var backGroundCover = document.createElement('div');
    backGroundCover.setAttribute('id', 'alarmBack');
    
    // 가운데 띄어 줄 메시지
    var alarmWrapDiv = document.createElement('div');
    var alarmP = document.createElement('p');
    
    // 직접 메시지를 정의한 경우
    if (message) {
        alarmP.innerText = message;
    } else {    // 기본 메시지
        alarmP.innerText = '새 메세지가 도착했습니다';
    }

    alarmP.setAttribute('id', 'alarm');
    alarmWrapDiv.appendChild(alarmP);

    // body 요소에 알림창을 넣는다.
    const bodyElem = document.querySelector('body');
    bodyElem.appendChild(backGroundCover);
    backGroundCover.appendChild(alarmWrapDiv);

    // 뒷 배경을 감싸는 커버의 CSS 속성
    backGroundCover.style.position = 'absolute';
    backGroundCover.style.top = '0px';
    backGroundCover.style.width = '100%';
    backGroundCover.style.height = '100%';
    backGroundCover.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    backGroundCover.style.zIndex = '100';

    // 가운데 띄어 줄 메시지의 CSS 속성
    alarmWrapDiv.style.margin = '0 auto';
    alarmWrapDiv.style.marginTop = '50px';
    alarmWrapDiv.style.width = '250px';
    alarmWrapDiv.style.height = '35px';
    alarmWrapDiv.style.borderRadius = "15px";
    alarmWrapDiv.style.backgroundColor = 'rgba(86, 86, 86, 0.9)';
    
    alarmP.style.textAlign = 'center';
    alarmP.style.lineHeight = '35px';
    alarmP.style.color = '#fff';
    alarmP.style.fontWeight = '550';

    // display를 none에서 block으로 변경한다.
    backGroundCover.style.display = 'block';
    alarmWrapDiv.style.display = 'block';

    // 클릭 시
    backGroundCover.onclick = function() {
        alarmRemove();  // 알림창을 제거한다.
        
        // 새 메세지의 도착을 알리는 경우에만 실행된다.
        if (!message) {

            // 채팅 상대방 아이디
            var opponentId = null;
            const qs = getQueryStringArgs();
            if (qs.hasOwnProperty('newReceiverId')) {
                opponentId = qs['newReceiverId'];
            } else {
                opponentId = qs['opponentId'];
            }

            // 새 메세지를 읽음 상태로 변경한다.
            var params = {
                mode: 'receivedNewMessage',
                id: opponentId,
            };
            $.ajax({
                url: 'ajax/chat.php',
                type: 'POST',
                data: $.param(params),
                dataType: 'json',
                error: function(xhr, textStatus, errorThrown) {
                    displayError(textStatus);
                }
            });
            
            // 드롭다운 창의 알림을 해제한다.
            $('#' + opponentId)
                .removeClass('hasNewMessage')
                .text(opponentId);
            setNotification();
        }
    }
}

/**
 *  화면에 추가한 알림창을 제거한다.
 */
function alarmRemove() {
    var backGroundCover = document.getElementById('alarmBack');
    if (backGroundCover) {
        backGroundCover.parentNode.removeChild(backGroundCover);
    }
}
