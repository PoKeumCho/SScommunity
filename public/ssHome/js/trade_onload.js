// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {

    var tradePage = document.getElementById('page');
    if (tradePage && tradePage.dataset.value != 0) {
        window.location.reload();   // 새로고침을 강제한다.
    }


    const campusNavSelect = document.getElementById('campus_nav');
    const categoryNavSelect = document.getElementById('category_nav');

    // 변경된 Navigation 부분의 폼의 값을 올바르게 설정한다.
    const qs = getQueryStringArgs();
    if (qs.hasOwnProperty('campus')) {
        campusNavSelect
            .getElementsByTagName('option')[qs['campus']]
            .selected = 'selected';
    }
    if (qs.hasOwnProperty('category')) {
        categoryNavSelect
            .getElementsByTagName('option')[qs['category']]
            .selected = 'selected';
    }

    // 선택 박스의 값이 변경되면 폼을 자동으로 전송한다.
    function submitParentForm() {
        this.parentNode.submit();
    }
    campusNavSelect.onchange = submitParentForm;
    categoryNavSelect.onchange = submitParentForm;

    /* Enter키를 입력해도 폼이 제출되지 않도록 설정한다. */    
    document.addEventListener('keydown', function (e) {    
        // textarea에서는 Enter키를 동작시킨다.
        const ta = document.getElementById('trade_info');
        if (e.target !== ta && e.code === 'Enter') {    
            e.preventDefault();    
            return false;    
        }    
    });

    /* '업로드한 이미지 미리보기' 기능을 구현한다. */
    const addFileInput = document.getElementById('add_file');
    addFileInput.onchange = function(event) {

        var addImgDivList = document.querySelectorAll('#trade_add_wrap > div.addImgWrap');
        var defaultImgList = document.querySelectorAll('#trade_add_wrap > div.addImgWrap > img.defaultImg');

        // 이전의 '업로드한 이미지 미리보기'가 존재한다면 제거한다.
        for (let i = 0; i < defaultImgList.length; i++) {
            if (defaultImgList[i].getAttribute('class') == 'defaultImg hidden') {
                let parentNode = defaultImgList[i].parentNode;
                parentNode.removeChild(parentNode.lastElementChild);
                defaultImgList[i].setAttribute('class', 'defaultImg');
            }  
        }

        // 이미지 파일의 업로드 개수를 제한한다.
        const target = event.target;
        const files = target.files;
        if (files.length > 3) {
            alert('이미지 파일은 최대 3개 업로드 가능합니다.');
            addFileInput.value = '';
            return false;
        }

        // 이미지 파일의 용량을 제한한다. (5M)
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5242880) {
                alert('업로드에 실패했습니다. 파일당 5MB까지 업로드할 수 있습니다.');
                addFileInput.value = '';
                return false;
            }
        }

        // 업로드한 이미지 미리보기 기능을 추가한다.
        for (let i = 0; i < files.length; i++) {
            
            // 기본 이미지는 화면에서 안 보이게 설정한다.
            defaultImgList[i].setAttribute('class', 'defaultImg hidden');

            // 업로드 이미지 요소를 생성한다. 
            let imgElem = document.createElement('img');
            imgElem.setAttribute('src', URL.createObjectURL(files[i]));
            imgElem.onload = function() {
                URL.revokeObjectURL(imgElem.src) // free memory
            };
            addImgDivList[i].appendChild(imgElem);
        }
    };


    // 새로운 글 등록 폼 검증
    const addSubmitInput = document.getElementById('add_submit');
    addSubmitInput.onclick = function(event) {
        const tradeTitleInput = document.getElementById('trade_title');             // 제목
        const tradeCategorySelect = document.getElementById('trade_category');      // 카테고리
        const tradePriceInput = document.getElementById('trade_price');             // 가격
        const tradeInfoTextarea = document.getElementById('trade_info');            // 설명
        const addFileInput = document.getElementById('add_file');                   // 이미지
        const campusCheckbox_1 = document.getElementById('campusCb_1');             // 수정 캠퍼스 체크박스
        const campusCheckbox_2 = document.getElementById('campusCb_2');             // 운정 캠퍼스 체크박스

        var valid = true;   // 유효성 검사

        var index = 0;
        var error = [];     // 오류 메시지

        if (tradeTitleInput.value.trim() == '') {
            valid = false;
            error[index++] = '제목을 입력해주세요.';
        } 

        if (tradeCategorySelect.value == 0) {
            valid = false;
            error[index++] = '카테고리를 선택해주세요.';
        }

        // 가격은 숫자만 입력할 수 있도록 제한한다.
        tradePriceInput.value = String(parseInt(tradePriceInput.value));
        if (!tradePriceInput.value) {
            valid = false;
            error[index++] = '가격을 입력해주세요.';
        } else {
            if (Number(tradePriceInput.value) > 1000000) {
                valid = false;
                error[index++] = '거래 금액을 100만원 이하로 설정해주세요.';
            } else if (Number(tradePriceInput.value) < 0) {
                valid = false;
                error[index++] = '거래 금액을 0원 이상으로 설정해주세요.';
            }
        }

        if (tradeInfoTextarea.value.trim() == '') {
            valid = false;
            error[index++] = '게시글 내용을 작성해주세요.';
        }

        if (addFileInput.value == '') {
            valid = false;
            error[index++] = '이미지를 첨부해주세요.';
        }

        if (!campusCheckbox_1.checked && !campusCheckbox_2.checked) {
            valid = false;
            error[index++] = '캠퍼스를 선택해주세요.';
        }

        if (!valid) {
            event.preventDefault();

            // 오류 메시지 출력
            var errorString = '';
            for (let i = 0; i < error.length; i++) {
                errorString += '-  ' + error[i] + '\n';
            }
            alert(errorString);
        } else { 
            loadingBarStart();  // 저장되는 동안 로딩 창을 띄워 오류를 방지한다.
        }
    };

}   // window.onload 함수 종료


/**
 *  Scroll Back To Top Button
 *  [참고] https://www.w3schools.com/howto/howto_js_scroll_to_top.asp
 */
window.onscroll = function() {
    gotoTopButton = document.getElementById("gotoTopBtn");

    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        gotoTopButton.style.display = "block";
    } else {
        gotoTopButton.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;            // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
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

/**
 *  이미지 영역을 클릭하면 다른 이미지를 보여준다.
 */
function showSlides(elem) {
    var childElemList = elem.childNodes;
    var dotchildElemList = elem.nextSibling.nextSibling.childNodes;
    
    // 해당 div 요소의 자식 img 요소를 추출한다.
    var imgElemList = [];
    var imgElemLength = 0;
    for (let i = 0; i < childElemList.length; i++) {
        if (childElemList[i].nodeType === 1 && childElemList[i].nodeName === 'IMG') {
            imgElemList[imgElemLength++] = childElemList[i];
        }
    }

    // 형제 요소의 자식 span 요소를 추출한다.
    var dotElemList = [];
    var dotElemLength = 0;
    for (let i = 0; i < dotchildElemList.length; i++) {
        if (dotchildElemList[i].nodeType === 1 && dotchildElemList[i].nodeName === 'SPAN') {
            dotElemList[dotElemLength++] = dotchildElemList[i];
        }
    }

    // 다른 이미지를 보여준다.
    if (imgElemLength > 1) {
        for (let i = 0; i < imgElemLength; i++) {
            if (imgElemList[i].getAttribute('class') == '') {
                imgElemList[i].setAttribute('class', 'hidden');

                if (i == (imgElemLength - 1)) {
                    imgElemList[0].setAttribute('class', '');
                } else {
                    imgElemList[i+1].setAttribute('class', '');
                }

                break;
            }
        }
    }

    // 이미지가 변경되는 순서를 보여준다.
    if (dotElemLength > 1) {
        for (let i = 0; i < dotElemLength; i++) {
            if (dotElemList[i].getAttribute('class') == 'dot select') {
                dotElemList[i].setAttribute('class', 'dot');

                if (i == (dotElemLength - 1)) {
                    dotElemList[0].setAttribute('class', 'dot select');
                } else {
                    dotElemList[i+1].setAttribute('class', 'dot select');
                }

                break;
            }
        }
    }
}
