/** 
 *  현재 읽고 있는 글 위치로 스크롤한다. 
 */
function saveCookie(id, sec) {
    const article = document.getElementById('article_' + id);
    if (article) {
        var rect = article.getBoundingClientRect();
        var date = new Date();
        var millisecond = 2000;
        if (sec) { millisecond = sec * 1000; }
        date.setTime(date.getTime() + millisecond);
        document.cookie = 'generalScrollTop=' + (rect.top + getScrollTop()) + ';expires=' + date.toUTCString() + ';secure';
    }
}

/** 
 *  글 위치로의 스크롤 기능을 해제한다.
 */
function removeCookie() {
    var date = new Date(0);
    document.cookie = 'generalScrollTop=;expires=' + date.toUTCString() + ';secure';
}


/**
 *  generalview 페이지에서 '목록'버튼을 눌렀을 때 이전 위치로 이동, 스크롤한다. 
 */
function saveCameFromCookie(id) {
    document.cookie = 'generalQueryString=' + location.search + ';secure'; 
    saveCookie(id, 86400);  // 1일
}
