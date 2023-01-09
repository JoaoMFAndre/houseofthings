function addZero(i) {
    if (i < 10) { i = "0" + i }
    return i;
}

const forecast = document.getElementById('forecast');
const todayWeatherIcon = document.getElementById('today-weather-icon');

const fullWeekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thrusday', 'Friday', 'Saturday'];
const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'];
const months = ['Jan', 'Fev', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const welcomeTypes = ["Good morning,", "Good afternoon,", "Good evening,"];

const API_KEY = '657d65a9aef368981096d98c5ce6c69d';

setInterval(() => {

    const d = new Date();

    let day = d.getDate();
    let weekday = d.getDay();
    let month = d.getMonth();
    let year = d.getFullYear();
    let hour = addZero(d.getHours());
    let minute = addZero(d.getMinutes());

    const dateText = weekdays[weekday] + ", " + day + " " + months[month] + " " + year;
    const timeText = hour + ":" + minute;

    var hrs = d.getHours();

    var greet;

    if (hrs >= 5 && hrs < 12)
        greet = 'Good Morning,';
    else if (hrs >= 12 && hrs <= 18)
        greet = 'Good Afternoon,';
    else
        greet = 'Good Evening,';

    document.getElementById('greeting').textContent = greet; 
    document.getElementById("date").textContent = dateText;
    document.getElementById("time").textContent = timeText;
}, 1000);

getWeatherData();
function getWeatherData() {
    fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=40.640507&lon=-8.653754&units=metric&cnt=29&appid=${API_KEY}`).then(res => res.json()).then(data => {
        showWeatherData(data);
    })
}

function showWeatherData(data) {
    let todayForecast = '';
    let tomorrowForecast = '';
    let dayAfterForecast = '';
    let dayAfterAfterForecast = '';

    let todayIcon = `<img src="http://openweathermap.org/img/wn/${data.list[4].weather[0].icon}@4x.png" alt="today weather icon" class="w-icon">`;

    const d1 = new Date(data.list[12].dt * 1000);
    const d2 = new Date(data.list[20].dt * 1000);

    let weekday1 = d1.getDay();
    let weekday2 = d2.getDay();

    const d1Text = fullWeekdays[weekday1];
    const d2Text = fullWeekdays[weekday2];

    data.list.forEach((day, idx) => {
        if (idx == 4) {
            todayForecast = `
                <div class="forecast-item">
                    <div class="icon">
                        <img src="http://openweathermap.org/img/wn/${day.weather[0].icon}@2x.png" alt="weather icon" class="w-icon">                        
                    </div>
                    <div class="text">
                    <div class="day">Today</div>
                    <div class="weather-description">${day.weather[0].main}</div>
                </div>
                <div class="temperature">
                    <div class="temp">${day.main.temp_max}&#176;/</div>
                    <div class="temp">${day.main.temp_min}&#176;</div>
                </div>
            `
        } else if (idx == 12) {
            tomorrowForecast = `
                <div class="forecast-item">
                    <div class="icon">
                        <img src="http://openweathermap.org/img/wn/${day.weather[0].icon}@2x.png" alt="weather icon" class="w-icon">                        
                    </div>
                    <div class="text">
                    <div class="day">Tomorrow</div>
                    <div class="weather-description">${day.weather[0].main}</div>
                </div>
                <div class="temperature">
                    <div class="temp">${day.main.temp_max}&#176;/</div>
                    <div class="temp">${day.main.temp_min}&#176;</div>
                </div>
            `
        } else if (idx == 20) {
            dayAfterForecast = `
                <div class="forecast-item">
                    <div class="icon">
                        <img src="http://openweathermap.org/img/wn/${day.weather[0].icon}@2x.png" alt="weather icon" class="w-icon">                        
                    </div>
                    <div class="text">
                    <div class="day">${d1Text}</div>
                    <div class="weather-description">${day.weather[0].main}</div>
                </div>
                <div class="temperature">
                    <div class="temp">${day.main.temp_max}&#176;/</div>
                    <div class="temp">${day.main.temp_min}&#176;</div>
                </div>
            `
        } else if (idx == 28) {
            dayAfterAfterForecast = `
                <div class="forecast-item">
                    <div class="icon">
                        <img src="http://openweathermap.org/img/wn/${day.weather[0].icon}@2x.png" alt="weather icon" class="w-icon">                        
                    </div>
                    <div class="text">
                    <div class="day">${d2Text}</div>
                    <div class="weather-description">${day.weather[0].main}</div>
                </div>
                <div class="temperature">
                    <div class="temp">${day.main.temp_max}&#176;/</div>
                    <div class="temp">${day.main.temp_min}&#176;</div>
                </div>
            `
        }
    })
    forecast.innerHTML += todayForecast;
    forecast.innerHTML += tomorrowForecast;
    forecast.innerHTML += dayAfterForecast;
    forecast.innerHTML += dayAfterAfterForecast;
    todayWeatherIcon.innerHTML = todayIcon;
}