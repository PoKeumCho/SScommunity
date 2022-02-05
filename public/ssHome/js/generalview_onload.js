// util.js 파일을 반드시 함께 include 해야 한다.

// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {
   
    /**
     * &j_(댓글id)=(대댓글 페이지를 나타내는 숫자 값) 형식이 URL에 들어가있으면,
     * 해당 댓글 모음의 display를 block으로 설정한다.
     */
    const j_page_regexp = /j_(\d)+/;
    const j_page_result = document.location.search.match(j_page_regexp);
    if (j_page_result) {
        var j_page = j_page_result[0].slice(2);
    }
    if (document.getElementById('add_child_comment_' + j_page)) {
        document.getElementById('add_child_comment_' + j_page).style.display = 'block';
        document.getElementById('child_comment_wrap_' + j_page).style.display = 'block'; 
        document.getElementById('child_comment_nav_' + j_page).style.display = 'block'; 
    } 

    // ssexpel(stranger) 폼 전송하기 전에 확인 메시지를 띄운다.
    var strangerFormList = document.querySelectorAll('form.stranger');
    for (let i = 0; i < strangerFormList.length; i++) {
        strangerFormList[i].onsubmit = function(event) {
            const isYes = confirm('해당 글 작성자가 재학/휴학/졸업생이 아니라고 생각하십니까?'); 
            if (isYes === false) {
                event.preventDefault();
            }
        };
    }

    // 크롬을 비롯한 일부 브라우저에서는 스크롤 복원 기능을 꺼야한다.
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // 스크롤할 위치로 이동한다.
    const cookies = convertCookieToObject(document.cookie);
    window.scrollTo(0, cookies['scrollTop']);
}
