// util.js 파일을 반드시 함께 include 해야 한다.

/**
 *  화면에 '시간표 추가'창을 추가한다. 
 */
function scheduleCreateStart() {
    var backHeight = window.innerHeight;    // 화면의 세로 길이
    var backWidth = window.innerWidth;      // 화면의 가로 길이

    // 뒷 배경을 감싸는 커버
    var backGroundCover = document.createElement('div');
    backGroundCover.setAttribute('id', 'invalidateBack');

    /**
     *  가운데 띄어 줄 폼
     *  [html 예시] ../../../private/templates/ssHome/schedule_create.html.bak
     */
    var myClassFormDiv = elt('div', { 
        id: 'my_class_formDiv'
    }, elt('form', {
        action: '',
        method: 'POST'
    }, 
            elt('label', { 'for': 'my_className' }, '과목명 (필수)'), 
            elt('input', {    
                type: 'text',                 
                id: 'my_className',           
                name: 'my_className',         
                'class': 'textInput',         
                maxlength: 30                 
            }),       
            elt('label', {}, '시간 (필수)'), 
            elt('div', {
                id: 'my_classTime'
            }, createClassTimeDiv(1)),
            elt('label', {
                'for': 'my_classInfo'
            }, '추가정보'),
            elt('input', {
                type: 'text',
                id: 'my_classInfo',
                name: 'my_classInfo',
                'class': 'textInput',
                maxlength: 30
            }),
            elt('div', {
                'class': 'submit_wrap'
            }, 
                elt('input', {
                    type: 'submit',
                    value: '취소',
                    onclick: 'scheduleCreateCancel(event)',
                    'class': 'submitInput',
                }), 
                elt('input', {
                    type: 'submit',
                    value: '저장',
                    onclick: 'scheduleCreateSave(event)',
                    'class': 'submitInput',
                }))));


    // body 요소에 '시간표 추가'창을 넣는다.
    const bodyElem = document.querySelector('body');
    bodyElem.appendChild(backGroundCover);
    backGroundCover.appendChild(myClassFormDiv);

    backGroundCover.style.top = getScrollTop() + 'px';
    backGroundCover.style.width = backWidth + 'px';
    backGroundCover.style.height = backHeight + 'px';

    /* 화면 스크롤에 맞춰서 뒷 배경을 감싸는 커버를 이동시킨다. */
    // 웹에서
    window.onscroll = function() { 
        var backGroundCover = document.getElementById('invalidateBack');
        backGroundCover.style.top = getScrollTop() + 'px';
    };
    // 모바일에서
    window.ontouchmove = function() {
        var backHeight = window.innerHeight;    // 화면의 세로 길이
        var backWidth = window.innerWidth;      // 화면의 가로 길이

        var backGroundCover = document.getElementById('invalidateBack');
        backGroundCover.style.top = getScrollTop() + 'px';
        
        // 뒷 배경의 길이도 재설정해야 한다.
        backGroundCover.style.width = backWidth + 'px';
        backGroundCover.style.height = backHeight + 'px';
    }
}

/**
 *  '요일/교시' 입력 항목을 생성한다.
 */
function createClassTimeDiv(number) {
    var classTimeDiv = elt('div', {
        'class': 'classTimeDiv' 
    }, elt('select', {
        id: 'classTime_day_' + number,
        name: 'classTime_day_' + number,
        'onchange': 'addClassTimeDiv(' + number + ')' 
    }, 
        elt('option', { value: 0 }, '[요일]'),
        elt('option', { value: 1 }, '월'),
        elt('option', { value: 2 }, '화'),
        elt('option', { value: 3 }, '수'),
        elt('option', { value: 4 }, '목'),
        elt('option', { value: 5 }, '금')
    ), '/', elt('select', {
        id: 'classTime_start_' + number,
        name: 'classTime_start_' + number,
        'onchange': 'addClassTimeDiv(' + number + ')' 
    }, 
        elt('option', { value: 0 }, '[선택]'),
        elt('option', { value: 1 }, '1'),
        elt('option', { value: 2 }, '2'),
        elt('option', { value: 3 }, '3'),
        elt('option', { value: 4 }, '4'),
        elt('option', { value: 5 }, '5'),
        elt('option', { value: 6 }, '6'),
        elt('option', { value: 7 }, '7'),
        elt('option', { value: 8 }, '8'),
        elt('option', { value: 9 }, '9'),
        elt('option', { value: 10 }, '10'),
        elt('option', { value: 11 }, '11'),
        elt('option', { value: 12 }, '12')
    ), '교시 -', elt('select', {
        id: 'classTime_end_' + number,
        name: 'classTime_end_' + number,
        'onchange': 'addClassTimeDiv(' + number + ')' 
    },
        elt('option', { value: 0 }, '[선택]'),
        elt('option', { value: 1 }, '1'),
        elt('option', { value: 2 }, '2'),
        elt('option', { value: 3 }, '3'),
        elt('option', { value: 4 }, '4'),
        elt('option', { value: 5 }, '5'),
        elt('option', { value: 6 }, '6'),
        elt('option', { value: 7 }, '7'),
        elt('option', { value: 8 }, '8'),
        elt('option', { value: 9 }, '9'),
        elt('option', { value: 10 }, '10'),
        elt('option', { value: 11 }, '11'),
        elt('option', { value: 12 }, '12')
    ), '교시');
   
    return classTimeDiv;
}   

/**
 *  '시간표 추가'창에 '요일/교시' 입력 항목을 추가한다.
 */
function addClassTimeDiv(number) {
    const classTimeDiv = document.getElementById('my_classTime');

    const classTimeDay = document.getElementById('classTime_day_' + number);
    const classTimeStart = document.getElementById('classTime_start_' + number);
    const classTimeEnd = document.getElementById('classTime_end_' + number);

    // 모든 필드를 작성한 경우
    if (classTimeDay.value != 0 && classTimeStart.value != 0 && classTimeEnd.value != 0) {
        // 시작 교시가 마지막 교시보다 큰 경우를 방지한다. 
        if (Number(classTimeStart.value) > Number(classTimeEnd.value)) {
            classTimeEnd.value = classTimeStart.value;
        }

        // 다음 입력 칸을 생성하지 않은 경우, 생성한다. (입력 칸은 최대 3개) 
        if (!document.getElementById('classTime_day_' + (number + 1)) && (number < 3)) {
            let elem = createClassTimeDiv(number + 1);
            classTimeDiv.appendChild(elem);
        }
    }
}

/**
 *  화면에서 '시간표 추가'창을 제거한다.
 */
function scheduleCreateEnd() {
    var backGroundCover = document.getElementById('invalidateBack');
    if (backGroundCover) {
        backGroundCover.parentNode.removeChild(backGroundCover);
    }

    // 화면 스크롤 이벤트를 제거한다.
    window.onscroll = null;
    window.ontouchmove = null;
}


/* '취소' 클릭 시 */
function scheduleCreateCancel(event) {
    event.preventDefault();
    scheduleCreateEnd();
    return;
}

/* '저장' 클릭 시 */
function scheduleCreateSave(event) {
    // 폼 제출을 제한한다.
    event.preventDefault();

    // 데이터는 처음부터 유효하다고 가정
    var valid = true;

    /* 과목명 */
    const classNameInput = document.getElementById('my_className');
    if (!classNameInput.value) {
        valid = false;
        alert('과목명을 입력해 주세요.');
    } else {
        if (classNameInput.value.length > 30) {
            valid = false;
            alert('과목명은 30글자를 초과할 수 없습니다.');
        }
    }

    /* 시간 */
    var classTimeDayArray = [];
    var classTimeStartArray = [];
    var classTimeEndArray = [];
    for (let i = 0; i < 3; i++) {
        if (document.getElementById('classTime_day_' + (i+1))) { 
            classTimeDayArray[i] = document.getElementById('classTime_day_' + (i+1));
            classTimeStartArray[i] = document.getElementById('classTime_start_' + (i+1));
            classTimeEndArray[i] = document.getElementById('classTime_end_' + (i+1));
        } else { break; }
    }
    // 유효한 시간을 배열에 담는다.
    var classTimeArray = [];
    for (let i = 0; i < classTimeDayArray.length; i++) {
        if (classTimeDayArray[i].value != 0 && classTimeStartArray[i].value != 0 && classTimeEndArray[i].value != 0) {
            classTimeArray[i] = {
                day: classTimeDayArray[i].value,
                start: Number(classTimeStartArray[i].value),
                end: Number(classTimeEndArray[i].value)
            };
        }
    }
    var isDuplicated = false;   // 중복 확인
    if (classTimeArray.length == 0) {
        valid = false;
        alert('시간을 입력해 주세요.');
    } else if (classTimeArray.length > 1) {
        for (let i = 0; i < classTimeArray.length; i++) {
            for (let j = i+1; j < classTimeArray.length; j++) {
                if(classTimeArray[i].day == classTimeArray[j].day &&
                Math.max(classTimeArray[i].start, classTimeArray[j].start) <= Math.min(classTimeArray[i].end, classTimeArray[j].end)) {
                    valid = false;
                    isDuplicated = true;

                    // 중복 값을 하나로 표시한다.
                    classTimeStartArray[i].value = Math.min(classTimeArray[i].start, classTimeArray[j].start); 
                    classTimeEndArray[i].value = Math.max(classTimeArray[i].end, classTimeArray[j].end);
                    classTimeDayArray[j].value = 0; classTimeStartArray[j].value = 0; classTimeEndArray[j].value = 0;
                }
            }
        }
    } 
    if (isDuplicated) {
        alert('중복되는 시간을 하나로 합쳐주세요.');
    }

    /* 추가정보 */
    const classInfoInput = document.getElementById('my_classInfo');
    if (classInfoInput.value) {
        if (classInfoInput.value.length > 30) {
            valid = false;
            alert('추가정보는 30글자를 초과할 수 없습니다.');
        }
    }

    /* 입력된 값을 저장한다. */
    if (valid) {
        const classTime = classTimeArrayToString(classTimeArray);
        const isSave = confirm(classNameInput.value + ' (' + classTime + ') 저장하시겠습니까?');
        
        const settings = {
            className: classNameInput.value,
            classTime: classTime
        }
        if (classInfoInput.value) {
            settings.classInfo = classInfoInput.value;
        }

        if (isSave) {
            saveClass(1, settings);
            scheduleCreateEnd();
        }
    }

    return;
}

/**
 *  classTimeArray 배열을 시간 문자열로 변경한다. ([day/start-end, ...]형식)
 *  classTimeArray = [ { day: , start: , end: }, ... ]
 */
function classTimeArrayToString(classTimeArray) {
    var classTimeString = '';
    const INDEX_TO_DAY = { 1: '월', 2: '화', 3: '수', 4: '목', 5: '금' };

    for (let i = 0; i < classTimeArray.length; i++) {
        classTimeString += INDEX_TO_DAY[classTimeArray[i].day] + '/' + classTimeArray[i].start;  
        if (classTimeArray[i].start != classTimeArray[i].end) {
            classTimeString += '-' + classTimeArray[i].end;
        }
        classTimeString += ','
    }

    classTimeString = classTimeString.slice(0, -1);   // 마지막 ',' 제거
    return classTimeString;
}

