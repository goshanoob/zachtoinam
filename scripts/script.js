document.addEventListener('DOMContentLoaded', setTimer);

function setTimer() {
    // Полное время таймера в секундах.
    let time = 900;
    const timer = setInterval(showTime, 2000);
    function showTime() {
        let minutes = Math.floor(time / 60).toFixed(),
            seconds = (time % 60).toFixed();
        if (time <= 0) clearInterval(timer);
        time -= 2;
        document.getElementById('timer').innerHTML = `ОСТАЛОСЬ <br> ${minutes} :  ${seconds.padStart(2, '0')}`;
    }
}