/**
 *  화면에 로딩 창을 추가한다.
 */
function loadingBarStart() {

    // 뒷 배경을 감싸는 커버
    var backGroundCover = document.createElement('div');
    backGroundCover.setAttribute('id', 'loadingBack');
    
    // 가운데 띄어 줄 이미지
    var loadingBarImageDiv = document.createElement('div');
    var loadingBarImage = document.createElement('img');
    loadingBarImageDiv.setAttribute('id', 'loadingBar');
    loadingBarImage.setAttribute('src', './img/chat/loadingBar.gif');
    loadingBarImage.setAttribute('width', '64');
    loadingBarImageDiv.appendChild(loadingBarImage);

    // body 요소에 로딩 창을 넣는다.
    const bodyElem = document.querySelector('body');
    bodyElem.appendChild(backGroundCover);
    backGroundCover.appendChild(loadingBarImageDiv);

    // 뒷 배경을 감싸는 커버의 CSS 속성
    backGroundCover.style.position = 'absolute';
    backGroundCover.style.top = '0px';
    backGroundCover.style.width = '100%';
    backGroundCover.style.height = '100%';
    backGroundCover.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    backGroundCover.style.zIndex = '100';

    // 가운데 띄어 줄 이미지의 CSS 속성
    loadingBarImageDiv.style.position = 'absolute';
    loadingBarImageDiv.style.left = '42%';
    loadingBarImageDiv.style.top = '40%';
    loadingBarImageDiv.style.zIndex = '200';
}

/**
 *  화면에 추가한 로딩 창을 제거한다.
 */
function loadingBarEnd() {
    var backGroundCover = document.getElementById('loadingBack');
    if (backGroundCover) {
        backGroundCover.parentNode.removeChild(backGroundCover);
    }
}
