// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {
    const changeWrapDiv = document.querySelector('div.change_wrap');
    const changePwDiv = document.getElementById('change_pw');
    const changeNicknameDiv = document.getElementById('change_nickname');
    const changeAccountimgDiv = document.getElementById('change_accountimg');
    const withdrawalDiv = document.getElementById('withdrawal');

    const pwSubmitInput = document.getElementById('pw_submit');
    const curPwInput = document.getElementById('cur_pw');
    const newPwInput = document.getElementById('new_pw');
    const newPwConfirmInput = document.getElementById('new_pw_confirm');

    const accountimgSelect = document.getElementById('accountimg_select');
    const accountimgPreviewImg = document.getElementById('accountimg_preview');

    const withdrawalSubmitInput = document.getElementById('withdrawal_submit');
    const withdrawalCurPwInput = document.getElementById('withdrawal_cur_pw');

    const modeParam = getParam('mode'); 
    switch(modeParam) {
        case 'viewChangePw':
            changeWrapDiv.style.display = 'block';
            changePwDiv.style.display = 'block';
            break;
        case 'viewChangeNickname':
            changeWrapDiv.style.display = 'block';
            changeNicknameDiv.style.display = 'block';
            break;
        case 'viewChangeAccountimg':
            changeWrapDiv.style.display = 'block';
            changeAccountimgDiv.style.display = 'block';
            break;
        case 'viewWithdrawal':
            changeWrapDiv.style.display = 'block';
            withdrawalDiv.style.display = 'block';
            break;
    }


    // 비밀번호 변경
    pwSubmitInput.onclick = function(event) {
        if (curPwInput.value === '') {
            alert('현재 비밀번호를 입력하세요.');
            event.preventDefault(); 
            curPwInput.focus();
        } else if (newPwInput.value === '') {
            alert('새 비밀번호를 입력하세요.');
            event.preventDefault(); 
            newPwInput.focus();
        } else if (/^[\w!@#$%^&*]{8,16}$/.test(newPwInput.value) === false) {
            alert('비밀번호는 8~16자의 영문 대소문자와 숫자, 특수문자(!@#$%^&*)를 사용할 수 있습니다.')
            event.preventDefault(); 
            newPwInput.focus();
        } else if (newPwConfirmInput.value === '') {
            alert('새 비밀번호 확인을 입력하세요.');
            event.preventDefault(); 
            newPwConfirmInput.focus();
        } else if (newPwConfirmInput.value !== newPwInput.value) {
            alert('새 비밀번호와 비밀번호 확인이 일치하지 않습니다.');
            event.preventDefault(); 
            newPwConfirmInput.focus();
        }
    };


    // 프로필 이미지 변경
    /* select 박스의 값의 변화에 따라 해당 이미지를 보여준다. */
    accountimgSelect.onchange = function() {
        switch (accountimgSelect.value) {
            case '1':
                accountimgPreviewImg.src = './img/account_img/default.png';
                break;
            case '2':
                accountimgPreviewImg.src = './img/account_img/student.png';
                break;
            case '3':
                accountimgPreviewImg.src = './img/account_img/rest.png';
                break;
            case '4':
                accountimgPreviewImg.src = './img/account_img/graduate.png';
                break;
            case '5':
                accountimgPreviewImg.src = './img/account_img/worker.png';
                break;
        }
    };


    // 회원탈퇴 
    withdrawalSubmitInput.onclick = function(event) {
        if (withdrawalCurPwInput.value === '') {
            alert('현재 비밀번호를 입력하세요.');
            event.preventDefault(); 
            withdrawalCurPwInput.focus();
        }
    };
}

// URL에서 파라미터 추출
function getParam(name)
{
    var curr_url = location.search.substr(location.search.indexOf("?") + 1);
    var svalue = "";
    curr_url = curr_url.split("&");
    for (var i = 0; i < curr_url.length; i++)
    {
        temp = curr_url[i].split("=");
        if ([temp[0]] == name) { svalue = temp[1]; }
    }
    return svalue;
}
