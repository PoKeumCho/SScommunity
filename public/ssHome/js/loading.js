// util.js 파일을 반드시 함께 include 해야 한다.

/**
 *  화면에 로딩 바를 추가한다.
 */
function loadingBarStart() {
    var backHeight = window.innerHeight;    // 화면의 세로 길이
    var backWidth = window.innerWidth;      // 화면의 가로 길이

    // 뒷 배경을 감싸는 커버
    var backGroundCover = document.createElement('div');
    backGroundCover.setAttribute('id', 'loadingBack');
    
    // 가운데 띄어 줄 이미지
    var loadingBarImageDiv = document.createElement('div');
    var loadingBarImage = document.createElement('img');
    loadingBarImageDiv.setAttribute('id', 'loadingBar');
    loadingBarImage.setAttribute('src', './img/layout/loadingBar.gif');
    loadingBarImage.setAttribute('width', '64');
    loadingBarImageDiv.appendChild(loadingBarImage);

    // body 요소에 로딩 창을 넣는다.
    const bodyElem = document.querySelector('body');
    bodyElem.appendChild(backGroundCover);
    backGroundCover.appendChild(loadingBarImageDiv);


    backGroundCover.style.top = getScrollTop() + 'px';
    backGroundCover.style.width = backWidth + 'px';
    backGroundCover.style.height = backHeight + 'px';
    backGroundCover.style.opacity = '0.3';

    // display를 none에서 block으로 변경한다.
    backGroundCover.style.display = 'block';
    loadingBarImageDiv.style.display = 'block';

    /* 화면 스크롤에 맞춰서 뒷 배경을 감싸는 커버를 이동시킨다. */
    // 웹에서
    window.onscroll = function() { 
        var backGroundCover = document.getElementById('loadingBack');
        backGroundCover.style.top = getScrollTop() + 'px';
    };
    // 모바일에서
    window.ontouchmove = function() {
        var backHeight = window.innerHeight;    // 화면의 세로 길이
        var backWidth = window.innerWidth;      // 화면의 가로 길이

        var backGroundCover = document.getElementById('loadingBack');
        backGroundCover.style.top = getScrollTop() + 'px';
        
        // 뒷 배경의 길이도 재설정해야 한다.
        backGroundCover.style.width = backWidth + 'px';
        backGroundCover.style.height = backHeight + 'px';
    }
}


/**
 *  화면에 추가한 로딩 바를 제거한다.
 */
function loadingBarEnd() {
    var backGroundCover = document.getElementById('loadingBack');
    if (backGroundCover) {
        backGroundCover.parentNode.removeChild(backGroundCover);
    }

    // 화면 스크롤 이벤트를 제거한다.
    window.onscroll = null;
    window.ontouchmove = null;
}
