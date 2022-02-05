/** 
 *  현재 읽고 있는 (대)댓글로 스크롤한다. 
 *
 *  - type  : [0] 댓글 / [1] 대댓글
 */
function saveCommentCookie(type, id, sec) {
    var date = new Date();
    var millisecond = 2000;
    if (sec) { millisecond = sec * 1000; }
    date.setTime(date.getTime() + millisecond);
    
    var commentDiv = null;
    if (type == 0) {
        commentDiv = document.getElementById('comment_wrap_' + id); 
    } else if (type == 1) {
        commentDiv = document.getElementById('child_' + id); 
    }
    
    if (commentDiv) {
        var rect = commentDiv.getBoundingClientRect();
        document.cookie = 'scrollTop=' + (rect.top + getScrollTop()) + ';expires=' + date.toUTCString() + ';secure';
    }
}
