$(window).scroll(function() {
    var scrollHeight = window.scrollY || $(window).scrollTop();

    // 테스트 용 코드
    //console.log(window.innerHeight + ' / ' + scrollHeight + ' / ' + document.body.offsetHeight);

    if ((window.innerHeight + scrollHeight) >= document.body.offsetHeight) {
        $('#more').trigger('click');    // Infinite scrolling
    }
});

$(document).ready(function() {

    const displayNum = 20;                                  // 한 번에 보여줄 글 개수
    const count = $('#trade_content').attr('data-count');   // 중고거래 글 개수

    const totalPage = parseInt(count / displayNum);
    var pageNum = 1;

    $('#more').click(function(event) {
        event.preventDefault();

        if (pageNum <= totalPage) {

            // 글 출력 위치 정보를 쿠키에 저장한다.
            const cookie = convertCookieToObject(document.cookie);
            cookie.trade_page = Number(cookie.trade_page) + 1;
            document.cookie = 'trade_page=' + cookie.trade_page + ';secure';

            // 이미지 탐색 시작 위치 정보를 쿠키에 저장한다.
            var $imgListPointer = $('#imgListPointer');
            document.cookie = 'trade_imgListPointer=' + $imgListPointer.attr('data-value') + ';secure';
            $imgListPointer.remove();

            /**
             *  사용자가 새로 고침 했을 때,
             *  ajax로 불러온 DOM Element가 없어질 때까지 자동 새로 고침을 구현하기 위한 장치
             */
            $('#page').remove();

            var $link = $(this);
            var url = $link.attr('href');
            if (url) {
                $.get(url, function(data) {
                    $('#trade_content').append(data);
                });
                pageNum++;
            }
        } else {
            $('#more').addClass('hidden');
        }
    }); // end-of-click_eventHandler
});


/**
 *  AJAX를 사용한 '부적절한 글 신고' 처리
 */
function contentExpelProcess(id) {
    const isYes = confirm('신고하시겠습니까?');
    if (isYes) {
        $.ajax({
            url: 'ajax/trade.php',
            type: 'GET',
            dataType: 'json',
            data: {
                mode: 'expelProcess',
                tradeId: id
            }
        });
    }
}
