/**
 *  스크롤한 거리를 구한다. 크로스 브라우징 지원
 */
function getScrollTop() {
    if (window.pageYOffset !== undefined) {
        return window.pageYOffset;
    } else {
        return document.documentElement.scrollTop || document.body.scrollTop;
    }
}

/**
 *  HTML 요소를 동적으로 생성한다.
 *
 *      - name          : 요소 이름 
 *      - attributes    : 속성의 이름과 값을 프로퍼티로 가지는 객체
 *      - child         : 추가할 자식 요소 (string 타입을 전달하면 텍스트 노드로 바꿔준다.)
 */
function elt(name, attributes) {
    var node = document.createElement(name);
    if (attributes) {
        for (var attr in attributes) {
            if (attributes.hasOwnProperty(attr)) {
                node.setAttribute(attr, attributes[attr])
            }
        }
    }
    for (var i = 2; i < arguments.length; i++) {
        var child = arguments[i];
        if (typeof child == "string") {
            child = document.createTextNode(child);
        }
        node.appendChild(child);
    }
    return node;
}

/**
 *  쿠키를 연관배열 형태로 변환한다.
 */
function convertCookieToObject(cookies) {
    const cookieItems = cookies.split(';');

    const obj = {};
    
    for (let i = 0; i < cookieItems.length; i++) {
        const elem = cookieItems[i].split('=');         // '='로 분리
        const key = elem[0].trim();                     // 키 가져오기
        const val = decodeURIComponent(elem[1]);        // 값 가져오기
        obj[key] = val;                                 // 저장
    }

    return obj;
}

/**
 *  현재 접속한 기기가 모바일인지 확인한다.
 */
function isMobile(){
    var UserAgent = navigator.userAgent;
    var re = new RegExp(['iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|',
                            'Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson|',
                            'LG|SAMSUNG'].join(''), 'i');
    if (UserAgent.match(re) != null) { 
        return true; 
    } else {
        return false;
    }
}

/**
 *  Detect the version of a browser
 *  [참고] https://stackoverflow.com/questions/5916900/how-can-you-detect-the-version-of-a-browser
 */
function getBrowser() {
    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
    if(/trident/i.test(M[1])){
        tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
        return {name:'IE',version:(tem[1]||'')};
        }   
    if(M[1]==='Chrome'){
        tem=ua.match(/\bOPR|Edge\/(\d+)/)
        if(tem!=null)   {return {name:'Opera', version:tem[1]};}
        }   
    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
    return {
      name: M[0],
      version: M[1]
    };
 }

/**
 *  Get query string values in JavaScript
 *  [참고] https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
 *
 *      - url   : location.href
 */
function getParameterByName(name, url) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
