<div id="add_schedule_wrap">
    <form action="" method="POST">
        <select name="classOption" id="classOption"
            onchange="classOptionChange()" >
            <option value="0">교과목명</option>
            <option value="1">학수번호</option>
        </select>          

        <input list="classList" name="inputClass" id="inputClass" 
            <?php echo (strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 'onkeyup' : 'onkeypress'); ?>
            ="getClassList(this.value)" autocomplete="off" />
        <datalist id="classList">
            <!-- option data-id="no" value="교과목명(분반)" -->
        </datalist>

        <input class="false_submit" type="submit" value="추가"
            onmousedown="addClassToSchedule()" />

        <input class="false_submit" type="submit" value="직접 추가"
            onmousedown="createSchedule()" />
    </form>
</div>

<div id="schedule_wrap">

    <!-- 스케줄의 요일 부분 -->
    <div id="schedule_head">
        <div class="head-box"></div>
        <div class="head-box double-size">월</div>
        <div class="head-box double-size">화</div>
        <div class="head-box double-size">수</div>
        <div class="head-box double-size">목</div>
        <div class="head-box double-size">금</div>
        <div class="head-box"></div>
    </div>

    <!-- 스케줄의 시간표 부분 -->
    <div id="schedule_body">

    <!--
        n 교시 
        m [월(1), 화(2), 수(3), 목(4), 금(5)] 요일  
        
            클릭되는 영역의 id : schedule_box_n_m
            텍스트가 작성되는 영역의 id : schedule_content_n_m
    -->

        <?php for ($i = 1; $i < 4; $i++): ?>
        <div>
            <div class="body-box"><p><?=$i?></p><p>교시</p></div>
            <div id="schedule_box_<?=$i?>_1" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_1" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_2" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_2" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_3" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_3" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_4" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_4" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_5" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_5" class="schedule_content"></div>
            </div>
            <div class="body-box"><p>오전</p><p><?= $i + 8 ?>시</p></div>
        </div>
        <?php endfor; ?>

        <div>
            <div class="body-box"><p>4</p><p>교시</p></div>
            <div id="schedule_box_4_1" class="body-box double-size">
                <div id="schedule_content_4_1" class="schedule_content"></div>
            </div>
            <div id="schedule_box_4_2" class="body-box double-size">
                <div id="schedule_content_4_2" class="schedule_content"></div>
            </div>
            <div id="schedule_box_4_3" class="body-box double-size">
                <div id="schedule_content_4_3" class="schedule_content"></div>
            </div>
            <div id="schedule_box_4_4" class="body-box double-size">
                <div id="schedule_content_4_4" class="schedule_content"></div>
            </div>
            <div id="schedule_box_4_5" class="body-box double-size">
                <div id="schedule_content_4_5" class="schedule_content"></div>
            </div>
            <div class="body-box"><p>오후</p><p>12시</p></div>
        </div>

        <?php for($i = 5; $i <= 12; $i++) : ?>
        <div>
            <div class="body-box"><p><?=$i?></p><p>교시</p></div>
            <div id="schedule_box_<?=$i?>_1" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_1" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_2" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_2" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_3" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_3" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_4" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_4" class="schedule_content"></div>
            </div>
            <div id="schedule_box_<?=$i?>_5" class="body-box double-size">
                <div id="schedule_content_<?=$i?>_5" class="schedule_content"></div>
            </div>
            <div class="body-box"><p>오후</p><p><?= $i - 4 ?>시</p></div>
        </div>
        <?php endfor; ?>

    </div>

</div>
