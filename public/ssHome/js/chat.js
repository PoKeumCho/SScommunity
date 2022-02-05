const url = "https://sscommu.com/public/chat/";

function openChat(userid, userpw, receiverId) {

    /**
     * window.open 변수넘길때, post방식으로 하기
     * [참고] http://www.hadooh.com/?p=313
     */

    // 채팅 상대 데이터베이스에 추가한다.
    if (receiverId) {
    }

    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", url);
    form.setAttribute("target", "chatWindow");
 
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = "id";
    input.value = userid;
    form.appendChild(input);
 
    input = document.createElement('input');
    input.type = 'hidden';
    input.name = "pw";
    input.value = userpw;
    form.appendChild(input);

    if (receiverId) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = "receiverId";
        input.value = receiverId;
        form.appendChild(input);
    }
 
    document.body.appendChild(form);

    const chatWin = window.open(url, "chatWindow", 
        "height=700,width=400,top=10,left=10,location=no,scrollbars=no"); 

    form.submit();

    document.body.removeChild(form);

    localStorage.setItem('chatWin', chatWin);
}

function logoutChat() {
    if (localStorage.getItem('chatWin')) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", url);
        form.setAttribute("target", "chatWindow");
     
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = "id";
        input.value = '';
        form.appendChild(input);
     
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = "pw";
        input.value = '';
        form.appendChild(input);

        document.body.appendChild(form);
        window.open(url, "chatWindow", "height=700,width=400,top=10,left=10,location=no,scrollbars=no"); 
        form.submit();
        document.body.removeChild(form);

        localStorage.removeItem('chatWin');
    }
}
