// util.js 파일을 반드시 함께 include 해야 한다.

// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {
    var generalNavForm = document.getElementById('general_nav_form');
    var generalOrderSelect = document.getElementById('general_order');
    var generalSearchInput = document.getElementById('general_search');

    var generalNewTextInput = document.getElementById('new_text');
    var generalAddFileInput = document.getElementById('add_file');
    var generalAddSubmitInput = document.getElementById('add_submit');

    var strangerFormList = document.querySelectorAll('form.stranger');

    // 새로고침 시 데이터 초기화
    generalAddFileInput.value = '';

    /* [전체 글, 오늘의 인기 글] 선택 시 해당 폼이 전송된다. */
    generalOrderSelect.onchange = function() {

        // '오늘의 인기 글' 선택 시 검색어를 삭제한다.
        if (generalOrderSelect.value == 1) {
            generalSearchInput.value = '';
        }

        generalNavForm.submit();
    };

    /** 
     *  검색한 단어가 존재하면 전체 글로 이동해서 검색 내용을 보여준다.
     *  공백만 입력한 경우에는 처리하지 않는다.
     */
    generalNavForm.onsubmit = function() {
        if (generalSearchInput.value.trim() !== '') {
            generalOrderSelect.value = 0;
        } else {
            generalSearchInput.value = '';
        }
    }


    /* 신규 글을 작성 시 '업로드한 이미지 미리보기' 기능을 구현한다. */
    generalAddFileInput.onchange = function(event) {

        // 이전의 '업로드한 이미지 미리보기'가 존재한다면 제거한다.
        if(document.querySelectorAll('#general_add_img_wrap > img')) {
            var imgElems = document.querySelectorAll('#general_add_img_wrap > img');
            for (let i = 0; i < imgElems.length; i++) {
                imgElems[i].parentNode.removeChild(imgElems[i]);
            }
        }

        // 이미지 파일의 업로드 개수를 제한한다.
        const target = event.target;
        const files = target.files;
        if (files.length > 5) {
            alert('이미지 파일은 최대 5개 업로드 가능합니다.');
            generalAddFileInput.value = '';
        }

        // 이미지 파일의 용량을 제한한다. (5M)
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5242880) {
                alert('업로드에 실패했습니다. 파일당 5MB까지 업로드할 수 있습니다.');
                generalAddFileInput.value = '';
            }
        }

        // 업로드한 이미지 미리보기 기능을 추가한다.
        var imgWrapDiv = document.getElementById('general_add_img_wrap');
        for (let i = 0; i < files.length; i++) {
            let imgElem = document.createElement('img');
            imgElem.setAttribute('src', URL.createObjectURL(files[i]));
            imgElem.setAttribute('width', 60);
            imgElem.onload = function() {
                URL.revokeObjectURL(imgElem.src) // free memory
            };
            imgWrapDiv.appendChild(imgElem);
        }
    };

    // 저장되는 동안 로딩 창을 띄워 오류를 방지한다.
    generalAddSubmitInput.onclick = function() {
        if (generalNewTextInput.value.trim() || generalAddFileInput.value) {
            loadingBarStart();
        }
    };

    // ssexpel(stranger) 폼 전송하기 전에 확인 메시지를 띄운다.
    for (let i = 0; i < strangerFormList.length; i++) {
        strangerFormList[i].onsubmit = function(event) {
            const isYes = confirm('해당 글 작성자가 재학/휴학/졸업생이 아니라고 생각하십니까?'); 
            if (isYes === false) {
                event.preventDefault();
            }
        };
    }


    /**
     *  likes, dislikes, stranger 버튼을 누른 후 폼 제출 후 다시 페이지 이동이 발생했을 때,
     *  해당 동작을 실행했던 위치로 스크롤하여 이동한다.
     */

    // 크롬을 비롯한 일부 브라우저에서는 스크롤 복원 기능을 꺼야한다.
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // 저장된 스크롤 위치로 이동한다.
    const cookies = convertCookieToObject(document.cookie);
    window.scrollTo(0, cookies['generalScrollTop']);
}
