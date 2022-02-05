// 시간표 검색 옵션 (classOption) 변경 시 동작
function classOptionChange() {
    // 검색 창 내용을 초기화한다.
    const inputClass = document.getElementById('inputClass');
    inputClass.value = '';

    // 이전에 검색된 시간표(<option ... />)를 모두 제거한다.
    removeAllClassDataListOption();
}

/**
 *  getClass : AJAX를 사용한 시간표 검색 자동 완성 기능
 */
function getClassList(value) {
    const classOptionSelect = document.getElementById('classOption');
    const classOption = classOptionSelect.value;

    const serverAddress = 'ajax/schedule.php';
    const showErrors = true;
    const data = "mode=getClass&opt=" + classOption + "&value=" + encodeURIComponent(value);

    const settings = {
        url: serverAddress,
        type: "GET",
        async: true,
        complete: function(xhr, response, status) {
            const classDataList = document.getElementById('classList');
            if (response.schedule) {            // 검색된 시간표가 존재하는 경우
                removeAllClassDataListOption();
                for (let i = 0; i < response.schedule.length; i++) {
                    if (response.schedule[i].info) {  // 추가 정보가 있는 경우
                        classDataList.appendChild(
                            createClassDataListOption(
                                response.schedule[i].no, 
                                response.schedule[i].className,
                                response.schedule[i].classTime,
                                response.schedule[i].info
                            )
                        );
                    } else {
                        classDataList.appendChild(
                            createClassDataListOption(
                                response.schedule[i].no, 
                                response.schedule[i].className,
                                response.schedule[i].classTime
                            )
                        );
                    }
                }
            }
        },
        data: data,
        showErrors: showErrors
    };

    // 지연을 최소화하기 위해 일정 글자 수를 입력해야 검색을 시작한다.
    if (classOption == 0) { // '교과목명'으로 검색한 경우
        if (value.length > 1) {
            var xmlHttp = new XmlHttp(settings);
        }
    } else {    // '학수번호'로 검색한 경우
        if (value.length > 2) {
            var xmlHttp = new XmlHttp(settings);
        }
    }
}


/**
 *  <option value="className (bunban)" data-id="no" data-time="classTime" 
 *      (data-info="campus roomAndProf") /> 생성 / 반환
 */
function createClassDataListOption(no, className, classTime, info) {
    var optionElem = document.createElement('option');
    optionElem.setAttribute('value', className);
    optionElem.setAttribute('data-id', no);
    optionElem.setAttribute('data-time', classTime);
    if (info) {
        optionElem.setAttribute('data-info', info);
    }
    return optionElem;
}

/**
 *  이전에 검색된 시간표(<option ... />)를 모두 제거한다.
 */
function removeAllClassDataListOption() {
    const optionElemList = document.querySelectorAll('#classList > option');
    if (optionElemList) {
        for (let i = 0; i < optionElemList.length; i++) {
            optionElemList[i].parentNode.removeChild(optionElemList[i]);
        }
    }
}


/**
 *  loadClass : AJAX를 사용한 사용자의 저장된 시간표 불러오기
 */
function loadClass() {
    const serverAddress = 'ajax/schedule.php';
    const showErrors = true;
    const data = "mode=loadClass";

    const settings = {
        url: serverAddress,
        type: "GET",
        async: false,
        beforeSend: function() {
            //loadingBarStart();
        },
        complete: function(xhr, response, status) {
            for (let i = 0; i < response.schedule.length; i++) {
                addSchedule({
                    contentHTML: response.schedule[i].contentHTML,
                    classTime: response.schedule[i].classTime
                }, i);

                const scheduleData = {
                    name: contentHtmlToClassName(response.schedule[i].contentHTML),
                    time: response.schedule[i].classTime,
                    opt: response.schedule[i].opt
                };
                if (response.schedule[i].no) {
                    scheduleData.no = response.schedule[i].no;
                }
                // 사용자의 브라우저에 추가한 schedule 데이터를 따로 저장한다.
                sessionStorage.setItem('schedule[' + i + ']', JSON.stringify(scheduleData));
            }
            // 사용자의 브라우저에 schedule_index 데이터를 저장한다.
            sessionStorage.setItem('schedule_index', response.schedule.length);

            //loadingBarEnd();
        },
        data: data,
        showErrors: showErrors
    };

    var xmlHttp = new XmlHttp(settings);
}

/**
 *  contentHTML에서 className(과목명)부분만 추출한다.
 */
function contentHtmlToClassName(contentHTML) {
    var className = null;
    className = contentHTML.split('</p>')[0];
    className = className.replace('<p class="className_bold">', '')
    return className;
}


/**
 *  saveClass : AJAX를 사용한 시간표 save(저장) & display
 *      
 *      - opt       : [0] 추가 / [1] 직접 추가
 *
 *      - settings  : [0]   {
 *                              schedule_no: ,
 *                              contentHTML: ,
 *                              classTime:
 *                          }
 *
 *                    [1]   {
 *                              className: ,
 *                              classTime: ,
 *                              classInfo:
 *                          }
 *
 */
function saveClass(opt, settings) {
    const serverAddress = 'ajax/schedule.php';
    const showErrors = true;

    var data = "mode=saveClass&opt=" + opt; 
    if (opt == 0) {     // 시간표를 '추가'하는 경우
        data += "&no=" + settings.schedule_no;
    } else if (opt == 1) {  // 시간표를 '직접 추가'하는 경우
        data += "&name=" + encodeURIComponent(settings.className) + "&time=" + settings.classTime;
        if (settings.classInfo) {
            data += "&info=" + encodeURIComponent(settings.classInfo);
        }
    }

    const addScheduleSettings = {
        contentHTML: null,
        classTime: settings.classTime
    }
    if (opt == 0) {         // 시간표를 '추가'하는 경우
        addScheduleSettings.contentHTML = settings.contentHTML;
    } else if (opt == 1) {  // 시간표를 '직접 추가'하는 경우
        addScheduleSettings.contentHTML = '<p class="className_bold">' + htmlEntities(settings.className) + '</p>';
        if (settings.classInfo) {
            addScheduleSettings.contentHTML += '<br/>' + htmlEntities(settings.classInfo);
        }
    }

    var ajaxSettings = {
        url: serverAddress,
        type: "GET",
        async: false,
        beforeSend: function() {
            loadingBarStart();
        },
        complete: complete,
        data: data,
        showErrors: showErrors
    };

    function complete(xhr, response, status) {
        if (response.result) {  // 성공적으로 데이터베이스에 저장(save)되면,
            const index = Number(sessionStorage.getItem('schedule_index'));
            addSchedule({       // display
                contentHTML: addScheduleSettings.contentHTML,
                classTime: addScheduleSettings.classTime
            }, index);

            const scheduleData = {
                name: contentHtmlToClassName(addScheduleSettings.contentHTML),
                time: addScheduleSettings.classTime,
                opt: opt
            };
            if (opt == 0) {
                scheduleData.no = settings.schedule_no;
            }
            sessionStorage.setItem('schedule[' + index + ']', JSON.stringify(scheduleData));
            sessionStorage.setItem('schedule_index', index + 1);
        } else {
            if (response.code == 1) {   // 해당 시간에 이미 다른 수업이 존재하는 경우
                if (opt == 0) {
                    alert('선택한 수업과 같은 시간에 이미 수업이 있습니다.');
                } else if (opt == 1) {
                    alert(settings.className + ' 수업과 같은 시간에 이미 수업이 있습니다.');
                }
            } else if (response.code == 2) {    // 해당 'no'를 기본 키로 갖는 데이터가 존재하지 않는 경우
                alert('오류가 발생했습니다. 나중에 다시 시도해 주세요.');
            }
        }
        loadingBarEnd();
    };

    var xmlHttp = new XmlHttp(ajaxSettings);
}

/**
 *  htmlEntities for JavaScript
 *  [출처] https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
 */
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/**
 *  removeClass : AJAX를 사용한 저장된 시간표 데이터베이스에서 제거
 *      
 *      - opt       : [0] 추가 / [1] 직접 추가
 *      - keyValue  : [0] no / [1] time
 *
 */
function removeClass(opt, keyValue) {
    const serverAddress = 'ajax/schedule.php';
    const showErrors = true;

    var data = "mode=removeClass&opt=" + opt + "&value=" + encodeURIComponent(keyValue); 

    const settings = {
        url: serverAddress,
        type: "GET",
        async: false,
        data: data,
        showErrors: showErrors
    };

    var xmlHttp = new XmlHttp(settings);
}

/**
 *  '추가' 버튼을 누른 경우 동작 : 선택한 시간표를 표에 추가한다.
 */
function addClassToSchedule() { 
    const classOption = document.getElementById('classOption');
    const classInput = document.getElementById('inputClass');
    const classDataList = document.getElementById('classList');
    const classListOptions = classDataList.options;

    var scheduletbl_no = null;
    var scheduletbl_classTime = null;    
    var scheduletbl_info = null;

    for (i = 0; i < classListOptions.length; i++) {
        if (classListOptions[i].value === classInput.value) {
            scheduletbl_no = classListOptions[i].getAttribute('data-id');
            scheduletbl_classTime = classListOptions[i].getAttribute('data-time');
            scheduletbl_info = classListOptions[i].getAttribute('data-info');  
            break;
        }
    }

    if (scheduletbl_no && scheduletbl_classTime) {
        let contentHTML = null;
        if (classOption.value == 0) {   // '교과목명'으로 검색한 경우
            contentHTML = '<p class="className_bold">' + (classInput.value).split(' (분반')[0] + '</p>';
        } else {    // '학수번호'로 검색한 경우
            contentHTML = '<p class="className_bold">' + (classInput.value).split(' // ')[1] + '</p>';
        }

        if (scheduletbl_info)
            contentHTML += '<br/>' + scheduletbl_info;
       
        // 데이터 베이스에 시간표 저장, 화면에 시간표 display
        saveClass(0, { 
                schedule_no: scheduletbl_no,
                contentHTML: contentHTML,
                classTime: scheduletbl_classTime
        });
    } else {
        alert('드롭다운 메뉴에서 골라주세요.\n' + 
            '해당 ' + ((classOption.value == 0)? '교과목명이' : '학수번호가') + ' 존재하지 않으면 직접 추가해서 사용해 주세요.');
    }

    classInput.value = '';  // 검색 창 내용을 초기화한다.
}

/**
 *  '직접 추가' 버튼을 누른 경우 동작 : 시간표를 생성한다.
 */
function createSchedule() {
    scheduleCreateStart();  // 화면에 '시간표 추가'창을 추가한다.
}

/**
 *  시간표를 추가한다.
 *  - myClass { 
 *        contentHTML: (시간표에 들어갈 내용 html 형식으로 작성), 
 *        classTime:
 *    }
 *  - index : (시간표의 색깔을 구분하기 위한) 순서
 */
function addSchedule(myClass, index) {
    // '월/7-8,화/3' 형태로 구성된다.
    var classTime = myClass.classTime.split(',');
    if (classTime.length > 1) {     // 여러 요일에 수업이 있는 경우
        for (let i = 0; i < classTime.length; i++) {
            classTime[i] = classTime[i].split('/');
            addOneDaySchedule(classTime[i][0], classTime[i][1], myClass.contentHTML, index);
        }
    } else {    // 한 요일에만 수업이 있는 경우
        classTime = classTime[0].split('/');
        addOneDaySchedule(classTime[0], classTime[1], myClass.contentHTML, index);
    }
}

/**
 *  Add one day schedule (시간표를 추가한다.)
 *      - day           : '월', '화', '수', '목', '금'
 *      - period        : ex) 3, 7-9 ...
 *      - contentHTML   : 시간표에 들어갈 innerHTML 내용
 *      - index         : (시간표의 색깔을 구분하기 위한) 순서
 */
function addOneDaySchedule(day, period, contentHTML, index) {
    const DAY_TO_INDEX = { '월': 1, '화': 2, '수': 3, '목': 4, '금': 5 };
    const SCHEDULE_COLOR = [ 
        '#e8dff5', '#fce1e4', '#fcf4dd', '#ddedea', '#daeaf6',
        '#d4afb9', '#d1cfe2', '#9cadce', '#7ec4cf', '#52b2cf',
        '#ffe5ec', '#e69597', '#ceb5b7', '#b5d6d6', '#a7bed3',
        '#c6e2e9', '#f1ffc4', '#ffcaaf', '#dab894', '#edf2fb',
        '#d1d1d1', '#ffc09f', '#809bce', '#b8e0d2', '#eac4d5',
        '#e8d1c5', '#f0efeb', '#ffc2d1', '#e27396', '#ea9ab2',
        '#efcfe3', '#eaf2d7', '#b3dee2', '#d7e3fc', '#e1dbd6',
        '#ffee93', '#95b8d1', '#d6eadf', '#eddcd2', '#ccdbfd',
        '#e2e2e2', '#fcf5c7', '#fff1e6', '#c1d3fe', '#f9f6f2',
        '#a0ced9', '#eeddd3', '#abc4ff', '#adf7b6'
    ];
    index = index % SCHEDULE_COLOR.length;

    // 시간표 한 칸의 높이
    const contentHeightPx = ((location.href).search(/Mobile/) != -1) ? '70px' : '120px';  

    let periodSplit = period.split('-');

    if (periodSplit.length > 1) {  // 수업이 여러 교시에 걸쳐 있는 경우
        periodMin = Number(periodSplit[0]);
        periodMax = Number(periodSplit[1]);
    } else {    // 수업을 한 교시만 하는 경우
        periodMin = periodMax = Number(periodSplit[0]);
    }

    /* CSS 수정 */
    for (let i = periodMin; i <= periodMax; i++) {
        let contentElem = document.getElementById('schedule_content_' + i + '_' + DAY_TO_INDEX[day]);
        let boxElem = document.getElementById('schedule_box_' + i + '_' + DAY_TO_INDEX[day]);

        // data-index 속성에 index 값을 저장한다. (나중에 시간표 제거 기능을 위한 장치)
        boxElem.setAttribute('data-index', index);

        if (i == periodMin) {
            const contentPlusPx = (periodMin == periodMax)? '0px' : '3px';

            contentElem.innerHTML = contentHTML;
            contentElem.style.width = "100%";
            contentElem.style.height = "calc(" + contentHeightPx + " * " + (periodMax - periodMin + 1) 
                                        + " + " + contentPlusPx + ")"; 
            contentElem.style.backgroundColor = SCHEDULE_COLOR[index];

            boxElem.style.borderBottom = "none";
        } else if (i == periodMax) {
            boxElem.style.borderTop = "none";
        } else {
            boxElem.style.borderTop = "none";
            boxElem.style.borderBottom = "none";
        }
    }
}

/**
 *  시간표를 제거한다.
 */
function removeSchedule(classTimeStr) {
    // '월/7-8,화/3' 형태로 구성된다.
    var classTime = classTimeStr.split(',');
    if (classTime.length > 1) {     // 여러 요일에 수업이 있는 경우
        for (let i = 0; i < classTime.length; i++) {
            classTime[i] = classTime[i].split('/');
            removeOneDaySchedule(classTime[i][0], classTime[i][1]);
        }
    } else {    // 한 요일에만 수업이 있는 경우
        classTime = classTime[0].split('/');
        removeOneDaySchedule(classTime[0], classTime[1]);
    }
}

/**
 *  Remove one day schedule (시간표를 제거한다.)
 *      - day       : '월', '화', '수', '목', '금'
 *      - period    : ex) 3, 7-9 ...
 */
function removeOneDaySchedule(day, period) {
    const DAY_TO_INDEX = { '월': 1, '화': 2, '수': 3, '목': 4, '금': 5 };

    let periodSplit = period.split('-');

    if (periodSplit.length > 1) {  // 수업이 여러 교시에 걸쳐 있는 경우
        periodMin = Number(periodSplit[0]);
        periodMax = Number(periodSplit[1]);
    } else {    // 수업을 한 교시만 하는 경우
        periodMin = periodMax = Number(periodSplit[0]);
    }

    /* CSS 수정 */
    for (let i = periodMin; i <= periodMax; i++) {
        let contentElem = document.getElementById('schedule_content_' + i + '_' + DAY_TO_INDEX[day]);
        let boxElem = document.getElementById('schedule_box_' + i + '_' + DAY_TO_INDEX[day]);
        if (i == periodMin) {
            contentElem.innerHTML = "";
            contentElem.style.height = "0";
        }
        boxElem.style.borderTop = "1px solid #d6d6d6";
        boxElem.style.borderBottom = "1px solid #d6d6d6";
    }
}
