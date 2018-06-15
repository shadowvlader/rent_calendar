function rentCalendar() {
  this.month = null;
  this.next = null;
  this.previous = null;
  this.label = null;
  this.activeDates = null;
  this.date = new Date();
  this.todaysDate = new Date();
  this.selectedDay = new Date();
  this._this = this;

  this.init = function (options) {
    this.month = document.getElementById(options.element).querySelectorAll('[data-calendar-area="month"]')[0]
    this.next = document.getElementById(options.element).querySelectorAll('[data-calendar-toggle="next"]')[0]
    this.previous = document.getElementById(options.element).querySelectorAll('[data-calendar-toggle="previous"]')[0]
    this.label = document.getElementById(options.element).querySelectorAll('[data-calendar-label="month"]')[0]
    this.options = options
    this.date.setDate(1)
    this.createMonth()
    this.createListeners()
  };

  this.createListeners = function () {
    var _this = this
    this.next.addEventListener('click', function () {
      _this.clearCalendar()
      var nextMonth = _this.date.getMonth() + 1
      _this.date.setMonth(nextMonth)
      _this.createMonth()
    })
    // Clears the calendar and shows the previous month
    this.previous.addEventListener('click', function () {
      _this.clearCalendar()
      var prevMonth = _this.date.getMonth() - 1
      _this.date.setMonth(prevMonth)
      _this.createMonth()
    })
  };

  this.createDay = function (num, day, year) {
    var newDay = document.createElement('div')
    var dateEl = document.createElement('span')
    dateEl.innerHTML = num
    newDay.className = 'vcal-date'
    newDay.setAttribute('data-calendar-date', this.date)

    // if it's the first day of the month
    if (num === 1) {
      if (day === 0) {
        newDay.style.marginLeft = (6 * 14.28) + '%'
      } else {
        newDay.style.marginLeft = ((day - 1) * 14.28) + '%'
      }
    }

    if (this.options.disablePastDays && this.date.getTime() <= this.todaysDate.getTime() - 1) {
      newDay.classList.add('vcal-date--disabled')
    } else {
      newDay.classList.add('vcal-date--active')
      newDay.setAttribute('data-calendar-status', 'active')
    }

    if (this.date.toString() === this.todaysDate.toString()) {
      newDay.classList.add('vcal-date--today')
    }

    newDay.appendChild(dateEl)
    this.month.appendChild(newDay)
  };

  this.dayClicked = function (event) {
    _this.removeActiveClass();
    this.classList.add('vcal-date--selected');
    _this.selectedDay = new Date(this.dataset.calendarDate);
    var picked = document.getElementById(_this.options.element).querySelectorAll(
      '[data-calendar-label="picked"]'
    )[0];
    picked.innerHTML = _this.getFormattedDate(_this.selectedDay);
    if (typeof _this.options.onDaySelect !== 'undefined') {
      _this.options.onDaySelect();
    }    
  }

  this.dateClicked = function () {
    this.activeDates = document.getElementById(this.options.element).querySelectorAll(
      '[data-calendar-status="active"]'
    )
    for (var i = 0; i < this.activeDates.length; i++) {
      this.activeDates[i].removeEventListener('click', this.dayClicked);
      this.activeDates[i].addEventListener('click', this.dayClicked);
    }
  };

  this.getFormattedDate = function (date) {
    var year = date.getFullYear();
  
    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;
  
    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    
    return year + '-' + month + '-' + day;
  };

  this.createMonth = function () {
    var currentMonth = this.date.getMonth()
    while (this.date.getMonth() === currentMonth) {
      this.createDay(
        this.date.getDate(),
        this.date.getDay(),
        this.date.getFullYear()
      )
      this.date.setDate(this.date.getDate() + 1)
    }
    // while loop trips over and day is at 30/31, bring it back
    this.date.setDate(1)
    this.date.setMonth(this.date.getMonth() - 1)

    this.label.innerHTML =
      this.monthsAsString(this.date.getMonth()) + ' ' + this.date.getFullYear()
    this.dateClicked()
  };

  this.monthsAsString = function (monthIndex) {
    return [
      'Sausis',
      'Vasaris',
      'Kovas',
      'Balandis',
      'Gegužė',
      'Birželis',
      'Liepa',
      'Rugpjūtis',
      'Rugsėjis',
      'Spalis',
      'Lapkritis',
      'Gruodis'
    ][monthIndex]
  };

  this.clearCalendar = function () {
    this.month.innerHTML = ''
  };

  this.removeActiveClass = function () {
    for (var i = 0; i < this.activeDates.length; i++) {
      this.activeDates[i].classList.remove('vcal-date--selected')
    }
  };
}