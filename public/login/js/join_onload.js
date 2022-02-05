window.onload = function () {

    // input 요소
    var inputId = document.getElementById('id');
    var inputPw = document.getElementById('pw');
    var inputPwConfirm = document.getElementById('pw_confirm');
    var inputName = document.getElementById('name');
    var inputBirthdateYear = document.getElementById('birthdate_year');
    var inputBirthdateDate = document.getElementById('birthdate_date');
    var inputNickname = document.getElementById('nickname');
    var inputStudentId = document.getElementById('student_id');
    var inputEmail = document.getElementById('email');

    // div 요소 (#wrap)
    var wrapId = document.getElementById('id_wrap');
    var wrapPw = document.getElementById('pw_wrap');
    var wrapPwConfirm = document.getElementById('pw_confirm_wrap');
    var wrapName = document.getElementById('name_wrap');
    var wrapBirthdate = document.getElementById('birthdate_wrap');
    var wrapNickname = document.getElementById('nickname_wrap');
    var wrapStudentId = document.getElementById('student_id_wrap');
    var wrapEmail = document.getElementById('email_wrap');


    /* Enter키를 입력해도 폼이 제출되지 않도록 설정한다. */
    document.addEventListener('keydown', function (e) {
            if (e.code === 'Enter') {
                e.preventDefault();
                return false;
            }
    });

    /* ============================================================================= 
            input 요소 검증용 이벤트
    ============================================================================= */

    // h3.msgElem 요소를 생성한다.
    function createMsgElem(msg) {
        var elem = document.createElement('h3');
        elem.setAttribute('class', 'msgElem');
        elem.innerHTML = msg;
        return elem;
    }
    
    // 아이디
    inputId.onkeyup = function() {
        // 해당 요소가 존재하지 않은 경우 null값이 저장된다.
        var msgElem = document.querySelector('#id_wrap > .msgElem');
        var phpMsgElem = document.querySelectorAll('#id_wrap > p');
        if (msgElem) {  // 이전에 생성된 메시지가 존재하는 경우 삭제한다.
            wrapId.removeChild(msgElem);
        }
        if (phpMsgElem) {   // 폼을 한 번 이미 제출한 경우,
                            // php에서 생성한 메시지가 존재하므로 삭제한다. 
            for (let i = 0; i < phpMsgElem.length; i++)
                phpMsgElem[i].innerHTML = "";
        }

        if (inputId.value === '') {
            wrapId.appendChild(createMsgElem('아이디를 입력해야 합니다.'));        
        } else if (/^[a-z0-9]{5,16}$/.test(inputId.value) === false) {
            wrapId.appendChild(createMsgElem('아이디는 5~16자의 영문 소문자, 숫자만 사용 가능합니다.'));        
        }
    };

    // 비밀번호
    inputPw.onkeyup = function() {
        var msgElem = document.querySelector('#pw_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#pw_wrap > p');
        if (msgElem) {
            wrapPw.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapPw.removeChild(phpMsgElem);
        }

        if (inputPw.value === '') {
            wrapPw.appendChild(createMsgElem('비밀번호를 입력해야 합니다.'));        
        } else if (/^[\w!@#$%^&*]{8,16}$/.test(inputPw.value) === false) {
            wrapPw.appendChild(createMsgElem('8~16자 영문 대 소문자, 숫자, 특수문자(!@#$%^&*)를 사용하세요.'));
        }
    };
    
    // 비밀번호 재확인
    inputPwConfirm.onkeyup = function() {
        var msgElem = document.querySelector('#pw_confirm_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#pw_confirm_wrap > p');
        if (msgElem) {
            wrapPwConfirm.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapPwConfirm.removeChild(phpMsgElem);
        }

        if (inputPwConfirm.value === '') {
            wrapPwConfirm.appendChild(createMsgElem('비밀번호를 재입력해야 합니다.'));        
        } else if (inputPwConfirm.value !== inputPw.value) {
            wrapPwConfirm.appendChild(createMsgElem('비밀번호가 일치하지 않습니다.'));        
        }
    };

    // 이름
    inputName.onkeyup = function() {
        var msgElem = document.querySelector('#name_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#name_wrap > p');
        if (msgElem) {
            wrapName.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapName.removeChild(phpMsgElem);
        }

        if (inputName.value === '') {
            wrapName.appendChild(createMsgElem('이름을 입력해야 합니다.'));        
        } else if (/^([ㄱ-ㅎ|ㅏ-ㅣ|가-힣]){2,6}$/.test(inputName.value)  === false) {
            wrapName.appendChild(createMsgElem('한글 이름(최대 6자)을 입력해 주세요.'));        
        }
    };

    // 생년월일
    inputBirthdateYear.onkeyup = function() {
        var msgElem = document.querySelector('#birthdate_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#birthdate_wrap > p');
        if (msgElem) {
            wrapBirthdate.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapBirthdate.removeChild(phpMsgElem);
        }

        if (inputBirthdateYear.value === '') {
            wrapBirthdate.appendChild(createMsgElem('YEAR 4자리를 입력해야 합니다.'));        
        } else if (/^[12][0-9]{3}$/.test(inputBirthdateYear.value)  === false) {
            wrapBirthdate.appendChild(createMsgElem('YEAR 올바르지 않은 형식입니다.'));        
        }
    };

    inputBirthdateDate.onkeyup = function() {
        var msgElem = document.querySelector('#birthdate_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#birthdate_wrap > p');
        if (msgElem) {
            wrapBirthdate.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapBirthdate.removeChild(phpMsgElem);
        }

        if (inputBirthdateDate.value === '') {
            wrapBirthdate.appendChild(createMsgElem('DATE를 입력해야 합니다.'));        
        } else if (/^([0][1-9]|[1-9]|[12][0-9]|3[01])$/.test(inputBirthdateDate.value)  === false) {
            wrapBirthdate.appendChild(createMsgElem('DATE 올바르지 않은 형식입니다.'));        
        }
    };

    // 닉네임
    inputNickname.onkeyup = function() {
        var msgElem = document.querySelector('#nickname_wrap > .msgElem');
        var phpMsgElem = document.querySelector('#nickname_wrap > p');
        if (msgElem) {
            wrapNickname.removeChild(msgElem);
        }
        if (phpMsgElem) {
            wrapNickname.removeChild(phpMsgElem);
        }

        if (inputNickname.value === '') {
            wrapNickname.appendChild(createMsgElem('닉네임을 입력해야 합니다.'));        
        }
    };

    // 학번
    inputStudentId.onkeyup = function() {
        var msgElem = document.querySelector('#student_id_wrap > .msgElem');
        var phpMsgElem = document.querySelectorAll('#student_id_wrap > p');
        if (msgElem) {
            wrapStudentId.removeChild(msgElem);
        }
        if (phpMsgElem) {
            for (let i = 0; i < phpMsgElem.length; i++)
                phpMsgElem[i].innerHTML = "";
        }

        if (inputStudentId.value === '') {
            wrapStudentId.appendChild(createMsgElem('학번을 입력해야 합니다.'));        
        } else if (/^[\d]{8}$/.test(inputStudentId.value) === false) {
            wrapStudentId.appendChild(createMsgElem('올바르지 않은 형식입니다.'));        
        }
    };

    // 이메일
    inputEmail.onkeyup = function() {
        var msgElem = document.querySelector('#email_wrap > .msgElem');
        var phpMsgElem = document.querySelectorAll('#email_wrap > p');
        if (msgElem) {
            wrapEmail.removeChild(msgElem);
        }
        if (phpMsgElem) {
            for (let i = 0; i < phpMsgElem.length; i++)
                phpMsgElem[i].innerHTML = "";
        }

        if (inputEmail.value === '') {
            wrapEmail.appendChild(createMsgElem('이메일을 입력해야 합니다.'));        
        }
    };


    /* 뒤로가기를 눌렀을 때 폼이 재전송되는 것을 방지한다. */
    if (document.querySelector('span.prevent_resubmit')) {
        history.pushState(null, null, location.href);
        window.onpopstate = function(event) {
            history.go(1);
        }
    }
};


/**
 *  AJAX을 사용한 중복 확인
 */
function validate(mode, value) {

    const serverAddress = 'ajax/validate.php';
    const showErrors = true;

    const settings = {
        url: serverAddress,
        type: "GET",
        async: true,
        complete: function(xhr, response, status) {
            const msgElem = document.getElementById(mode + 'ValidateP');
            if (response.result == false) {
                // '중복 알림 메시지'의 중복을 막는다.
                if (!document.getElementById(mode + '_duplicate_prevention') ||
                        !document.getElementById(mode + '_duplicate_prevention').innerHTML) { 
                    if (mode == 'id') {
                        if (response.code == 1) {
                            msgElem.innerHTML = "이미 사용 중인 아이디입니다.";   
                        } else if (response.code == 2) {
                            msgElem.innerHTML = "사용할 수 없는 아이디입니다.";   
                        }
                    } else if (mode == 'studentid' || mode == 'email') {
                        if (response.code == 1) {
                            if (mode == 'studentid')
                                msgElem.innerHTML = "이미 사용 중인 학번입니다. 하단에 기재된 이메일로 문의 바랍니다.";   
                            else if (mode == 'email')
                                msgElem.innerHTML = "이미 사용 중인 이메일입니다. 하단에 기재된 이메일로 문의 바랍니다."; 
                        } else if (response.code == 2) {
                            msgElem.innerHTML = '본인 인증 진행 중입니다. ( ' +
                                '<a href="verifyemail?email=' +
                                response.email +
                                '">Click here</a> )';
                        }
                    }
                }
            }
        },
        data: "mode=" + mode + "&value=" + value,
        showErrors: showErrors
    };

    var xmlHttp = new XmlHttp(settings);
}
