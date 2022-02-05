# SScommunity   
https://www.sscommu.com/   

# Database   
https://github.com/PoKeumCho/SScommuityDatabase   

# Web Version   
## 1. Login process
* https://youtu.be/IBTvDiw8ttI
* https://youtu.be/DsPjx_wLnE4
## 2. Authorization(성신 인증)   
* ### **By Using Python CGI Applications And Python Web Crawling And Scraping**    
https://github.com/PoKeumCho/UsePythonToCheckAuthorization
## 3. General(게시판) process    
https://youtu.be/BLutZMtt8Uc
## 4. Schedule(시간표) process    
* ### Crawl schedule data from a website   
https://github.com/PoKeumCho/UsePythonToGetAndSaveSchedule   
* ### How it works   
![schedule_PC](https://user-images.githubusercontent.com/88548181/152640886-c14eece6-abe4-4f60-a962-85e4d8ea8ce4.gif)    
## 5. Trade(중고거래) process   
https://youtu.be/11pO_Oe5DaU   
## 6. MyInfo(내 정보) process  
https://youtu.be/bCxzPvLrHAs   
## 7. MyArticle(글 관리) process  
https://youtu.be/FqDA93cM458   
## 8. Chat process (실시간 채팅 구현)   
![chat](https://user-images.githubusercontent.com/88548181/152640919-ca1d346b-f8f2-4c3f-8fe7-c0817279e382.gif)   

- - -
## [ 참고 (1) ] 이미지 파일 구조 및 폴더 추가 자동화   
### **1. 이미지 파일 구조**
* file
    * images
        * chat
        * general
        * trade   
       
### **2. 폴더 추가 자동화**
**/etc/cron.daily/make-image-dir**   
   
![use_cron](https://user-images.githubusercontent.com/88548181/152640941-29d2c044-0faa-488d-8ae2-b9e9744dadaf.png)   
   
```bash
#!/bin/bash
# cron 프로그램을 통해 매일 다음 날짜에 해당하는 폴더를 미리 생성한다.

tomorrow=$(date '+%Y-%m-%d' -d '1 day')

mkdir /var/www/html/file/images/chat/$tomorrow
mkdir /var/www/html/file/images/general/$tomorrow
mkdir /var/www/html/file/images/trade/$tomorrow
```
## [ 참고 (2) ] 서비스 점검중 구현   
https://youtu.be/oXoKc7i_lIc   
   
```bash
<Directory /var/www/html/public>    
    Options FollowSymLinks MultiViews    
    AllowOverride All    
    Require all granted    
</Directory>    

##-- Maintenance Mode (Web) --#
#<DirectoryMatch /var/www/html/public/(login|ssHome|Mobile|chat)> 
##<DirectoryMatch /var/www/html/public/(?!Android|maintenance|favicon)(.+)> 
#    RewriteEngine on
#    RewriteRule "^(.*)$" "https://sscommu.com" [NC,L,QSA]
#</DirectoryMatch>

<Directory /var/www/html/private>    
    Options Indexes MultiViews FollowSymLinks    
    AllowOverride None    
    Order allow,deny       
    Allow from 1.234.63.146    
    Deny from all    
</Directory>
```
    
# Mobile Version
https://youtu.be/vbeDIeFBT1s   

# Android App Version   
https://github.com/PoKeumCho/SScommu.git   

- - -
**References (참고자료)**    
* 톰 버틀러, 케빈 양크 (김재영, 정병열) - PHP & MySQL 닌자 비법서 
* 윤인성 - 모던 웹을 위한 HTML5+CSS3 바이블 
* Bogdan Brinzarea, Cristian Darie, Audra Hendrix - AJAX and PHP: Building Modern Web Applications (2nd Edition)
* Jonathan Chaffer, Karl Swedberg - Learning jQuery (Fourth Edition)

- - -
# License
```
Copyright 2022 PoKeumCho

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
