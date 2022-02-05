// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {
    var addGcButtonA = document.getElementById('add_gc_button');
    var addGcCancelButtonA = document.getElementById('add_gc_cancel_button');
    var addGcCancelButtonWrapDiv = document.getElementById('add_gc_cancel_button_wrap');
    var addGcNewDiv = document.getElementById('add_gc_new');
    var addGcNewForm = document.getElementById('add_gc_new_form');

    var gcNameInput = document.getElementById('gc_name');
    var gcInfoInput = document.getElementById('gc_info');
    var gcHashtagInput = document.getElementById('gc_hashtag');

    var checkboxWrapDiv = document.querySelector('#add_gc_new_form > div.checkbox_wrap');
    var gcExpelInput = document.getElementById('gc_expel');

    var gcSearchForm = document.getElementById('gc_search_form');
    var gcSearchInput = document.getElementById('gc_search');

    
    /* display의 초기값을 설정한다. */
    addGcNewDiv.style.display = 'none';
    addGcCancelButtonWrapDiv.style.display = 'none';

    /* '새 게시판 만들기' 버튼 */
    addGcButtonA.onclick = function() {
        /* 폼을 출력한다 */
        if (addGcNewDiv.style.display == 'none') {
            if (document.getElementById('add_gc_errors')) { // DOM 요소가 존재하는 경우에만 실행한다.
                document.getElementById('add_gc_errors').style.display = 'none';
            }
            addGcNewDiv.style.display = 'block';
            addGcCancelButtonWrapDiv.style.display = 'block';
        }
        /* 폼을 제출한다. */
        else {
            // '#검색어#검색어#검색어...' 형식으로 입력하지 않은 경우
            if (/^(#[^\s#]{1,})*$/.test(gcHashtagInput.value) === false) {
                // 공백으로만 이루어진 경우 폼을 제출할 수 있다.
                if (gcHashtagInput.value.trim === '') {
                    addGcNewForm.submit();
                }
                alert("검색어 해시태그가 올바른 형식이 아닙니다. 띄어쓰기 없이 '#검색어#검색어#검색어...' 형식으로 입력해 주세요.\n\n"
                    + "ex) #개발연습#취업준비#오늘뭐먹지#띄어쓰기하지마세요");
            } else {
                addGcNewForm.submit();
            }
        }
    };

    /* '새 게시판 만들기' 취소 버튼 */
    addGcCancelButtonA.onclick = function() {
        gcNameInput.value = '';
        gcInfoInput.value = ''; 
        gcHashtagInput.value = ''; 

        addGcNewDiv.style.display = 'none';
        addGcCancelButtonWrapDiv.style.display = 'none';
    }

    
    /**
     * '검색어 해시태그' 형식으로 입력할 수 있도록 도와주는 기능 
     * (모바일과 IE에서는 제대로 동작하지 않는다.)
     */
    var isShiftRightPressed = false;
    gcHashtagInput.onkeydown = function(event) {
        // 처음 해시태그를 입력할 경우 앞에 '#'를 붙여준다.
        if (gcHashtagInput.value === '') {
            if (event.code === 'Space') { event.preventDefault(); }
            gcHashtagInput.value = '#';
        }

        // 사용자의 '#'키 입력을 제한한다.
        if (event.code === 'ShiftRight') {
            isShiftRightPressed = true;
        }
        if (isShiftRightPressed && event.code === 'Digit3') {
            event.preventDefault();
            isShiftRightPressed = false;
        }
    };

    gcHashtagInput.onkeypress = function(event) {
        // 마지막 글자가 '#'인 경우
        if (gcHashtagInput.value.charAt(gcHashtagInput.value.length - 1) === '#') {
            // Space키를 입력해도 처리하지 않는다.
            if (event.code === 'Space') { event.preventDefault(); }
        }
        // 마지막 글자가 '#'가 아닌 경우
        else {
            // Space키를 입력한 경우 새로운 해시태그를 생성한다.
            if (event.code === 'Space') {
                event.preventDefault();
                gcHashtagInput.value = gcHashtagInput.value + '#';
            }
        }
    };

    /* '방출 기능'에 대한 설명을 출력한다. */
    gcExpelInput.onchange = function() {
        var infoElem = document.getElementById('gc_expel_info');
        if (!infoElem) {
            infoElem = document.createElement('div');
            infoElem.setAttribute('id', 'gc_expel_info');
            checkboxWrapDiv.appendChild(infoElem);
        }

        if (gcExpelInput.checked === true) {
            infoElem.innerHTML = "'싫어요' 기능이 동작하며 과반수의 '싫어요'를 받은 글의 작성자는 게시판에서 방출됩니다.";
        } else {
            infoElem.innerHTML = "'싫어요' 기능이 동작하지 않습니다.";
        }
    };


    /* 검색어 입력 글자 수를 지정한다. */
    gcSearchForm.onsubmit = function(event) {
        if (gcSearchInput.value.length < 1) {
            event.preventDefault();
            alert('검색어는 한 글자 이상 입력해 주세요.');
        }
    };
    


    /* 뒤로가기를 눌렀을 때 '새 게시판 만들기' 폼이 재전송되는 것을 방지한다. */
    if (document.getElementById('add_gc_prevent_resubmit')) {
        history.pushState(null, null, location.href);
        window.onpopstate = function(event) {
            history.go(1);
        }
    }

    /* 게시판 검색 후 뒤로가기를 눌렀을 때 폼이 재전송되는 것을 방지한다. */
    if (document.getElementById('gc_search_prevent_resubmit')) {
        history.pushState(null, null, location.href);
        window.onpopstate = function(event) {
            history.go(1);
        }
    }

}
