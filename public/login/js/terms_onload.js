window.onload = function () {
    var toggleCb = document.getElementById('toggle');           // toggle 체크박스 
    var toggleImg = document.getElementById('toggle_img');
    var confirm = document.querySelector('.confirm');           // 확인 버튼

    // 체크박스 클릭 영역 설정
    var click = document.getElementsByClassName('click'); 
    var clickList = Array.prototype.slice.call(click);

    // 페이지를 새로 불러올 때 체크박스는 항상 꺼둔다.
    toggleCb.checked = false;

    // 체크박스 상태에 따라 toggle 이미지가 바뀐다.
    function changeToggleImg () { 
        const isChecked = toggleCb.checked;
        isChecked ? 
            toggleImg.src = './img/toggle/toggle_off.png' :
            toggleImg.src = './img/toggle/toggle_on.png';
    }

    toggleCb.onchange = function(event) {
        // 체크박스 상태 확인
        const isChecked = event.target.checked;
        // 체크박스가 체크된 상태에서 확인 버튼을 누른 경우에만 다음 페이지로 이동하도록 설정한다.
        isChecked ?
            confirm.setAttribute('href', 'join') :
            confirm.setAttribute('href', '#');
    };

    for (let i = 0; i < clickList.length; i++) {
        clickList[i].onclick = changeToggleImg;
    }
};
