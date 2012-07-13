var PODLOVE = PODLOVE || {};

(function ($) {
	'use strict';

	var startAtTime = false,
		stopAtTime = false,
		// Keep all Players on site
		players = [],
		// Timecode as described in http://podlove.org/deep-link/
		// and http://www.w3.org/TR/media-frags/#fragment-dimensions
		timecodeRegExp = /(\d\d:)?(\d\d):(\d\d)(\.\d\d\d)?(,(\d\d:)?(\d\d):(\d\d)(\.\d\d\d)?)?/;

	/**
	 * return number as string lefthand filled with zeros
	 * @param number number
	 * @param width number
	 * @return string
	 **/
	function zeroFill(number, width) {
		width -= number.toString().length;
		return width > 0 ? new Array(width + 1).join('0') + number : number + '';
	}

	/**
	 * accepts array with start and end time in seconds
	 * returns timecode in deep-linking format
	 * @param times array
	 * @return string
	 **/
	function generateTimecode(times) {
		function generatePart(seconds) {
			var part, hours, milliseconds;
			// prevent negative values from player
			if (!seconds || seconds <= 0) {
				return '00:00';
			}

			// required (minutes : seconds)
			part = zeroFill(Math.floor(seconds / 60) % 60, 2) + ':' +
					zeroFill(Math.floor(seconds % 60) % 60, 2);

			hours = zeroFill(Math.floor(seconds / 60 / 60), 2);
			hours = hours === '00' ? '' : hours + ':';
			milliseconds = zeroFill(Math.floor(seconds % 1 * 1000), 3);
			milliseconds = milliseconds === '000' ? '' : '.' + milliseconds;

			return hours + part + milliseconds;
		}

		if (times[1] > 0 && times[1] < 9999999 && times[0] < times[1]) {
			return generatePart(times[0]) + ',' + generatePart(times[1]);
		}

		return generatePart(times[0]);
	}

	/**
	 * parses time code into seconds
	 * @param string timecode
	 * @return number
	 **/
	function parseTimecode(timecode) {
		var parts, startTime = 0, endTime = 0;

		if (timecode) {
			parts = timecode.match(timecodeRegExp);

			if (parts && parts.length === 10) {
				// hours
				startTime += parts[1] ? parseInt(parts[1], 10) * 60 * 60 : 0;
				// minutes
				startTime += parseInt(parts[2], 10) * 60;
				// seconds
				startTime += parseInt(parts[3], 10);
				// milliseconds
				startTime += parts[4] ? parseFloat(parts[4]) : 0;
				// no negative time
				startTime = Math.max(startTime, 0);

				// if there only a startTime but no endTime
				if (parts[5] === undefined) {
					return [startTime, false];
				}

				// hours
				endTime += parts[6] ? parseInt(parts[6], 10) * 60 * 60 : 0;
				// minutes
				endTime += parseInt(parts[7], 10) * 60;
				// seconds
				endTime += parseInt(parts[8], 10);
				// milliseconds
				endTime += parts[9] ? parseFloat(parts[9]) : 0;
				// no negative time
				endTime = Math.max(endTime, 0);

				return (endTime > startTime) ? [startTime, endTime] : [startTime, false];
			}
		}
		return false;
	}

	function checkCurrentURL(doStartPlayer) {
		var deepLink;

		// parse deeplink
		deepLink = parseTimecode(window.location.href);

		if (deepLink !== false) {
			startAtTime = deepLink[0];
			stopAtTime = deepLink[1];

			// get first player on page and start it
			if (doStartPlayer && players[0].play) {
				players[0].play();
			}
		}
	}

	function setFragmentURL(fragment) {
		window.location.hash = fragment;
	}

	// update the chapter list when the data is loaded
	function updateChapterMarks(player, marks) {
		marks.each(function () {
			var title, deepLink,
				mark       = $(this),
				startTime  = mark.data('start'),
				endTime    = mark.data('end'),
				isEnabled  = mark.data('enabled'),
				isBuffered = player.buffered.end(0) > startTime,
				isActive   = player.currentTime > startTime - 0.3 &&
						player.currentTime <= endTime;

			if (isActive) {
				mark
					.addClass('active')
					.siblings().removeClass('active');
			}
			if (!isEnabled && isBuffered) {
				deepLink = '#t=' + generateTimecode([startTime, endTime]);

				mark.data('enabled', true);

				title = mark.find('td.title');
				title.html('<a href="' + deepLink + '">' + title.html() + '</a>');
			}
		});
	}

	function checkTime(e) {
		if (players.length > 1) {
			return;
		}

		var player = e.data.player;

		if (startAtTime !== false) {
			player.setCurrentTime(startAtTime);
			startAtTime = false;
		}
		if (stopAtTime !== false && player.currentTime >= stopAtTime) {
			player.pause();
			stopAtTime = false;
		}
	}

	function addressCurrentTime(e) {
		var fragment;
		if (players.length === 1 &&
				stopAtTime === false &&
				startAtTime === false) {
			fragment = 't=' + generateTimecode([e.data.player.currentTime]);
			setFragmentURL(fragment);
		}
	}

	PODLOVE.web_player = function (playerId) {
		var deepLink,
			player = document.getElementById(playerId);

		players.push(player);

		// parse deeplink
		deepLink = parseTimecode(window.location.href);

		if (deepLink !== false && players.length === 1) {
			$(player)
				.attr({preload: 'auto', autoplay: 'autoplay'});

			startAtTime = deepLink[0];
			stopAtTime = deepLink[1];
		}

		window.MediaElementPlayer('#' + playerId, {
			success: function (player) {
				PODLOVE.web_player.addBehavior(player);
				if (deepLink !== false && players.length === 1) {
					$('html, body')
						.delay(150)
						.animate({
							scrollTop: $(players[0]).offset().top - 25
						});
				}
			}
		});
	};

	/**
	 * add chapter behavior and deeplinking: skip to referenced
	 * time position & write current time into address
	 * @param player object
	 */
	PODLOVE.web_player.addBehavior = function (player) {
		var jqPlayer = $(player),
			playerId = jqPlayer.attr('id'),
			list = $('table[rel=' + playerId + ']'),
			marks = list.find('tr');

		if (players.length === 1) {
			// check if deeplink is set
			checkCurrentURL('doStartPlayer');
		}

		// if needed load flattr js
		if (!window.FlattrLoader && $('.FlattrButton').length > 0) {
			$.getScript('https://api.flattr.com/js/0.6/load.js?mode=auto');
		}

		// chapters list
		list
			.show()
			.delegate('a', 'click', function (e) {
				e.preventDefault();

				var mark = $(this).closest('tr'),
					startTime = mark.data('start'),
					endTime = mark.data('end');

				// If there is only one player also set deepLink
				if (players.length === 1) {
					setFragmentURL('t=' + generateTimecode([startTime, endTime]));
				} else {
					// Basic Chapter Mark function (without deeplinking)
					player.setCurrentTime(startTime);
				}

				if (player.pluginType !== 'flash') {
					player.play();
				}
			});

		// wait for the player or you'll get DOM EXCEPTIONS
		jqPlayer.bind('canplay', function () {

			// add Deeplink Behavior if there is only one player on the site
			if (players.length === 1) {
				jqPlayer.bind({
					play: checkTime,
					timeupdate: checkTime,
					pause: addressCurrentTime
					// disabled 'cause it overrides chapter clicks
					//seeked: addressCurrentTime
				}, {player: player});

				// handle browser history navigation
				$(window).bind('hashchange onpopstate', function () {
					checkCurrentURL();
				});

				// handle links on the page
				// links added later are not handled!
				$('a').bind('click', function () {
					// if we stay on the page after clicking a link
					// check if theres a new deeplink
					window.setTimeout(function () {
						checkCurrentURL('doStartPlayer');
					}, 100);
				});
			}

			// always update Chaptermarks though
			jqPlayer.bind('timeupdate', function () {
				updateChapterMarks(player, marks);
			});

		});
	};
}(jQuery));