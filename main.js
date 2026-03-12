document.addEventListener('DOMContentLoaded', function() {

	var cal = document.querySelector('.cal');

	// localStorage helpers (replaces Depot)
	function getWatched()       { return JSON.parse(localStorage.getItem('watched') || '[]'); }
	function isWatched(day)     { return getWatched().includes(day); }
	function addWatched(day)    { var w = getWatched(); if (!w.includes(day)) { w.push(day); localStorage.setItem('watched', JSON.stringify(w)); } }
	function removeWatched(day) { localStorage.setItem('watched', JSON.stringify(getWatched().filter(function(d) { return d !== day; }))); }

	var first_year   = dayjs(date_start).year();
	var last_year    = dayjs(date_stop).year();
	var current_year = first_year;

	function renderYear(year) {
		cal.innerHTML = '';

		var prevDis = (year <= first_year) ? ' disabled' : '';
		var nextDis = (year >= last_year)  ? ' disabled' : '';
		cal.insertAdjacentHTML('beforeend',
			'<div class="year-nav">' +
				'<button class="year-prev"' + prevDis + '>&#9664;</button>' +
				'<h1>' + year + '</h1>' +
				'<button class="year-next"' + nextDis + '>&#9654;</button>' +
			'</div><hr>'
		);

		for (var m = 0; m < 12; m++) {
			cal.insertAdjacentHTML('beforeend',
				createMonth(dayjs(new Date(year, m, 1)))
			);
		}
	}

	renderYear(current_year);


	// Month
	function createMonth(d) {
		var start_date  = d;
		var start_fill  = start_date.day();
		var num_days    = start_date.daysInMonth();
		var post_fill   = 42 - (num_days + start_fill);
		var month_name  = start_date.format('MMM').toUpperCase();

		var r = '<div class="c_col">';
		r += '<h2>' + month_name + '</h2>';

		// First blanks
		for (var i = 0; i < start_fill; i++) {
			r += '<div class="lb">&nbsp;</div>';
		}
		// Days
		for (var i = 1; i <= num_days; i++) {
			var key = dayjs(new Date(start_date.year(), start_date.month(), i)).format('YYYYMMDD');
			r += dayData(i, key);
		}
		// Last blanks
		for (var i = 0; i < post_fill; i++) {
			r += '<div class="lb">&nbsp;</div>';
		}
		r += '</div>';
		return r;
	}

	function dayData(day, x) {
		var rin = '';
		var bk  = '';
		var ch  = '';
		if (jsonx[x]) {
			bk = ' style="background-image: url(https://i.ytimg.com/vi/' + jsonx[x][0].i + '/mqdefault.jpg); background-size: cover;"';
			ch = ' class="in"';
			for (var i = 0; i < jsonx[x].length; i++) {
				rin += '<p><a href="https://www.youtube.com/watch?v=' + jsonx[x][i].i + '">' + jsonx[x][i].t + '</a></p>';
			}
		}
		var isdone = isWatched(x) ? ' done' : '';
		var r = '<div class="lb' + isdone + '"' + bk + ' data-day="' + x + '">';
		r += '<h3' + ch + '>' + day + '</h3>';
		r += rin;
		r += '</div>';
		return r;
	}

	// Event delegation
	cal.addEventListener('click', function(e) {
		if (e.target.classList.contains('year-prev') && current_year > first_year) {
			current_year--;
			renderYear(current_year);
			return;
		}
		if (e.target.classList.contains('year-next') && current_year < last_year) {
			current_year++;
			renderYear(current_year);
			return;
		}

		var lb = e.target.closest('.lb');
		if (!lb) return;

		// If the click was on a link, let it open normally
		if (e.target.closest('a')) {
			e.stopPropagation();
			return;
		}

		var day = lb.dataset.day;
		if (day) {
			if (lb.classList.contains('done')) {
				lb.classList.remove('done');
				removeWatched(day);
			} else {
				lb.classList.add('done');
				addWatched(day);
			}
		}
		e.preventDefault();
	});

});
