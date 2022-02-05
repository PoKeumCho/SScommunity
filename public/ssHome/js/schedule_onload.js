// 웹 브라우저가 문서를 모두 읽어 들인 후 실행된다.
window.onload = function () {
    // 사용자의 저장된 시간표를 불러온다.
    loadClass();

    const PERIOD_COUNT = 12;
    const DAY_COUNT = 5;

    // schedule_box[n교시-1][m요일-1]
    var schedule_box = new Array(PERIOD_COUNT);
    for (let i = 0; i < PERIOD_COUNT; i++) {
        schedule_box[i] = new Array(DAY_COUNT);
    }

    for (var _i = 0; _i < PERIOD_COUNT; _i++) {
        for (var _j = 0; _j < DAY_COUNT; _j++) {
            // let문(블록유효범위)을 사용해서 해당 시점의 루프 카운터 값을 함수에 넘긴다.
            let i = _i;
            let j = _j;
            schedule_box[i][j] = document.getElementById('schedule_box_' + (i + 1) + '_' + (j + 1));

            //=============================== 이벤트 함수 등록 ===============================//
            /* 클릭 시, 시간표 제거 */
            schedule_box[i][j].onclick = function() {
                if (this.getAttribute('data-index')) {  // 클릭한 영역에 수업이 존재하는 경우
                    const click = JSON.parse(sessionStorage.getItem('schedule[' + Number(this.getAttribute('data-index')) + ']'));
                    
                    // 모바일에서는 화면을 스크롤하다가 실수로 클릭할 가능성이 높으므로 확인창을 띄운다.
                    let deleteOk = true;
                    if (isMobile()) {
                        const isYes = confirm(click.name + ' 수업을 시간표에서 삭제하시겠습니까?');
                        if (!isYes) { deleteOk = false; }
                    } else {
                        alert(click.name + ' 수업을 시간표에서 삭제합니다.');
                    }
                    if (deleteOk) { // 클릭한 수업을 삭제한다.
                        if (click.opt == 0) { removeClass(0, click.no); }
                        else if (click.opt == 1) { removeClass(1, click.time); }
                        removeSchedule(click.time);
                    }
                }
            };
            //================================================================================//
        }
    }


    // schedule_content[n교시-1][m요일-1]
    var schedule_content = new Array(PERIOD_COUNT);
    for (let i = 0; i < PERIOD_COUNT; i++) {
        schedule_content[i] = new Array(DAY_COUNT);
    }

    for (var _i = 0; _i < PERIOD_COUNT; _i++) {
        for (var _j = 0; _j < DAY_COUNT; _j++) {
            // let문(블록유효범위)을 사용해서 해당 시점의 루프 카운터 값을 함수에 넘긴다.
            let i = _i;
            let j = _j;
            schedule_content[i][j] = document.getElementById('schedule_content_' + (i + 1) + '_' + (j + 1));

            //==================== 이벤트 함수 등록 ====================//
            // schedule_content[i][j].onclick = function() {}
            //==========================================================//
        }
    }


    // input 요소 중 class 값을 false_submit로 갖는 요소는 폼 제출을 제한한다.
    const falseSubmitList = document.querySelectorAll('input.false_submit');
    if (falseSubmitList) {
        for (let i = 0; i < falseSubmitList.length ; i++) {
            falseSubmitList[i].onclick = function(event) {
                event.preventDefault();
            };
        }
    }
}

// 모바일 환경이면 true를 반환한다.
function isMobile() {
    return (location.href.includes('public/Mobile')); 
}
