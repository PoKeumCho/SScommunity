$(document).ready(function() {

    const $selectEmail = $('#selectEmail');
    const $selectStudentid = $('#selectStudentid');

    const $email = $('#email');
    const $studentid = $('#studentid');

    $selectEmail.on('click', function(event) {
        event.preventDefault();
        if (!$selectEmail.hasClass('selected')) {
            $selectStudentid.removeClass('selected');
            $selectEmail.addClass('selected');

            $studentid.val('');
            $studentid.addClass('hidden');
            $email.removeClass('hidden');
        } 
    });
    $selectStudentid.on('click', function(event) {
        event.preventDefault();
        if (!$selectStudentid.hasClass('selected')) {
            $selectEmail.removeClass('selected');
            $selectStudentid.addClass('selected');

            $email.val('');
            $email.addClass('hidden');
            $studentid.removeClass('hidden');
        } 
    });


    /* 폼 검증 */
    $('#submit').on('click', function(event) {
        var valid = true;
        var errMsg = [];
        var errMsgIndex = 0;

        // 비밀번호 찾기에만 존재하므로
        if ($('#id').length) {
            const idValue = String($('#id').val()).trim();
            $('#id').val(idValue);

            // 아이디
            if (!idValue) {
                valid = false;
                errMsg[errMsgIndex++] = '아이디를 입력해주세요.'
            } else {
                if ((/^[a-z0-9]{5,16}$/.test(idValue) === false)) {
                    valid = false;
                    errMsg[errMsgIndex++] = '올바르지 않은 형식의 아이디입니다.'
                }
            }
        }
        
        const nameValue = String($('#name').val()).trim();
        $('#name').val(nameValue);

        const emailValue = String($email.val()).trim();
        $email.val(emailValue);

        const studentidValue = $studentid.val();
        
        // 이름
        if (!nameValue) {
            valid = false;
            errMsg[errMsgIndex++] = '이름을 입력해주세요.'
        } else {
            if (/^([ㄱ-ㅎ|ㅏ-ㅣ|가-힣]){2,6}$/.test(nameValue) === false) {
                valid = false;
                errMsg[errMsgIndex++] = '한글 이름(최대 6자)을 입력해주세요.'
            }
        }

        // 이메일
        if ($selectEmail.hasClass('selected')) {
            if (!emailValue) {
                valid = false;
                errMsg[errMsgIndex++] = '이메일을 입력해주세요.'
            }
        }

        // 학번
        if ($selectStudentid.hasClass('selected')) {
            if (!studentidValue) {
                valid = false;
                errMsg[errMsgIndex++] = '학번을 입력해주세요.'
            } else {
                if (/^[\d]{8}$/.test(studentidValue) === false) {
                    valid = false;
                    errMsg[errMsgIndex++] = '올바르지 않은 형식의 학번입니다.'
                }
            }
        }

        if (!valid) {
            event.preventDefault()

            // 오류 메시지 출력
            var errorString = '';
            for (let i = 0; i < errMsg.length; i++) {
                errorString += '-  ' + errMsg[i] + '\n';
            }
            alert(errorString);
        }
    });

});
