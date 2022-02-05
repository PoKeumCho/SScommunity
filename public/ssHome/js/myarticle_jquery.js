$(document).ready(function() {

    /**
     *  글 종류 선택
     */
    $('#select_' + getParameterByName('select', location.href)).addClass('selected');


    /**
     *  폼 제출 후 다시 페이지 이동이 발생했을 때, 해당 동작을 실행했던 위치로 스크롤하여 이동한다.
     */
    if (getParameterByName('select', location.href) == 'trade') {
        
        // 크롬을 비롯한 일부 브라우저에서는 스크롤 복원 기능을 꺼야한다.
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // 저장된 스크롤 위치로 이동한다.
        const cookies = convertCookieToObject(document.cookie);
        window.scrollTo(0, cookies['myarticleScrollTop']);
    }


    /**
     *  a.submit 요소를 클릭 시 적당한 form 전송을 구현
     */
    $('#myarticle_content > article a.submit')
        .on('click', function(event) {
            event.preventDefault();

            // 선택한 글의 id 값
            var $thisArticle = $(this).parent().parent();
            if (this.dataset.id === 'trade_changePrice') {
                $thisArticle = $thisArticle.parent();
            }
            var tradeId = $thisArticle.attr('data-id');
            $('#tradeId').val(tradeId); 

            // 선택한 옵션
            /* 거래완료 (글 삭제) */
            if (this.dataset.id === 'trade_delete') { 
                $('#tradeOpt').val('delete'); 
            } 
            /* 끌어올리기 */
            else if (this.dataset.id === 'trade_update') {
                // 끌어올리기가 가능한지 확인한다.
                var date = $thisArticle.find('div.right > p.date').attr('data-date');
                if (!checkUpdateValid(date)) {
                    alert('끌어올리기는 일주일 간격으로 가능합니다.'); 
                    return false;
                }

                $('#tradeOpt').val('update'); 
            }
            /* 가격 변경 */
            else if (this.dataset.id === 'trade_changePrice') {
                var price = $(this).find('p').attr('data-price');
                var newPrice = prompt('판매가격 변경 (거래 금액을 100만원 이하로 설정해주세요.)', price);

                if (newPrice === null) { return false; }            // '취소'를 누른 경우
                if (!/^\d+$/.test(newPrice)) { return false; }      // 숫자 외의 문자가 존재하는 경우
                if (Number(newPrice) > 1000000) { return false; }   // 100만원을 초과하는 경우
                if (Number(newPrice) == price) { return false; }    // 기존 판매가와 동일한 경우

                $('#tradePrice').val(newPrice);
                $('#tradeOpt').val('changePrice');
            }

            // 현재 위치를 저장한다.
            saveCookie(tradeId);

            // form 전송
            $('#tradeSubmit').trigger('click');
        })

});


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
        document.cookie = 'myarticleScrollTop=' + (rect.top + getScrollTop()) + ';expires=' + date.toUTCString() + ';secure';
    }
}

/**
 *  끌어올리기를 할 수 있는지 확인한다. (일주일 기준)
 */
function checkUpdateValid(date) {
    // 모든 '-'를 '/'로 바꾼다.
    date = date.replace(/-/g, '/');

    var uploadDateMs = Date.parse(new Date(date));
    var nowMs = Date.parse(new Date());

    // 86,400,000 milliseconds in 1 day
    var diffMs = nowMs - uploadDateMs;
    var diffDay = (diffMs / 86400000).toFixed(0);   // 반올림

    if (diffDay < 7) {
        return false;
    } else {
        return true;
    }
}
