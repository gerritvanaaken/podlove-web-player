# BUILD PWP FILES

build:
	mkdir -p ./podlove-web-player/static
	cat ./podlove-web-player/libs/pwpfont/css/fontello.css ./podlove-web-player/libs/mediaelement/build/mediaelementplayer.css ./podlove-web-player/podlove-web-player.css > ./podlove-web-player/static/podlove-web-player.tmp.css
	cat ./podlove-web-player/libs/mediaelement/build/mediaelement-and-player.min.js ./podlove-web-player/podlove-web-player.js > ./podlove-web-player/static/podlove-web-player.tmp.js
	@echo "/**\n * ===========================================\n * Podlove Web Player v2.0.8\n * Licensed under The BSD 2-Clause License\n * http://opensource.org/licenses/BSD-2-Clause\n * ===========================================\n */\n" > ./podlove-web-player/static/copywrite.txt
	cat ./podlove-web-player/static/copywrite.txt ./podlove-web-player/static/podlove-web-player.tmp.js  > ./podlove-web-player/static/podlove-web-player.js
	cat ./podlove-web-player/static/copywrite.txt ./podlove-web-player/static/podlove-web-player.tmp.css > ./podlove-web-player/static/podlove-web-player.css
	cp ./podlove-web-player/libs/mediaelement/build/flashmediaelement.swf ./podlove-web-player/static/flashmediaelement.swf
	cp ./podlove-web-player/libs/mediaelement/build/controls.svg ./podlove-web-player/static/controls.svg
	cp ./podlove-web-player/libs/mediaelement/build/controls.png ./podlove-web-player/static/controls.png
	cp ./podlove-web-player/libs/jquery.ba-hashchange.min.js ./podlove-web-player/static/hashchange.min.js
	cp ./podlove-web-player/libs/mediaelement/build/bigplay.svg ./podlove-web-player/static/bigplay.svg
	cp ./podlove-web-player/libs/mediaelement/build/bigplay.png ./podlove-web-player/static/bigplay.png
	cp -R ./podlove-web-player/libs/pwpfont/font/ ./podlove-web-player/font/
	rm  ./podlove-web-player/static/podlove-web-player.tmp.css ./podlove-web-player/static/podlove-web-player.tmp.js ./podlove-web-player/static/copywrite.txt
