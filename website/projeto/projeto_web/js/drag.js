const room = document.querySelector(".room");
const devices = document.querySelector(".devices");
const legenda = document.querySelector(".legenda");
const icon = document.querySelector(".container-overflow");
const notification = document.querySelector(".notification-container");
const form = document.querySelector(".form-inputs");
const statistics = document.querySelector(".statistics-left");

let startY;
let startX;
let scrollLeft;
let scrollTop;
let isDown;

if (room != null) {
  /*** ROOM ***/
  room.addEventListener('mousedown', (e) => {
    isDown = true;
    room.classList.add('active');
    startX = e.pageX - room.offsetLeft;
    scrollLeft = room.scrollLeft;
  });
  room.addEventListener('mouseleave', () => {
    isDown = false;
    room.classList.remove('active');
  });
  room.addEventListener('mouseup', () => {
    isDown = false;
    room.classList.remove('active');
  });
  room.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move Horizontally
      const x = e.pageX - room.offsetLeft;
      const walkX = x - startX;
      room.scrollLeft = scrollLeft - walkX;
    }
  });

}

if (devices != null) {
  /*** DEVICES ***/
  devices.addEventListener('mousedown', (e) => {
    isDown = true;
    devices.classList.add('active');
    startX = e.pageX - devices.offsetLeft;
    scrollLeft = devices.scrollLeft;
  });
  devices.addEventListener('mouseleave', () => {
    isDown = false;
    devices.classList.remove('active');
  });
  devices.addEventListener('mouseup', () => {
    isDown = false;
    devices.classList.remove('active');
  });
  devices.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move Horizontally
      const x = e.pageX - devices.offsetLeft;
      const walkX = x - startX;
      devices.scrollLeft = scrollLeft - walkX;
    }
  });

}

if (legenda != null) {
  /*** LEGENDA ***/
  legenda.addEventListener('mousedown', (e) => {
    legenda.classList.add('active');
    isDown = true;
    startY = e.pageY - legenda.offsetTop;
    scrollTop = legenda.scrollTop;
  });
  legenda.addEventListener('mouseleave', () => {
    isDown = false;
    legenda.classList.remove('active');
  });
  legenda.addEventListener('mouseup', () => {
    isDown = false;
    legenda.classList.remove('active');
  });
  legenda.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - legenda.offsetTop;
      const walkY = y - startY;
      legenda.scrollTop = scrollTop - walkY;
    }
  });
}


if (icon != null) {
  /*** ICON ***/
  icon.addEventListener('mousedown', (e) => {
    icon.classList.add('active');
    isDown = true;
    startY = e.pageY - icon.offsetTop;
    scrollTop = icon.scrollTop;
  });
  icon.addEventListener('mouseleave', () => {
    isDown = false;
    icon.classList.remove('active');
  });
  icon.addEventListener('mouseup', () => {
    isDown = false;
    icon.classList.remove('active');
  });
  icon.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - icon.offsetTop;
      const walkY = y - startY;
      icon.scrollTop = scrollTop - walkY;
    }
  });
}

if (notification != null) {
  /*** NOTIFICATIONS ***/
  notification.addEventListener('mousedown', (e) => {
    notification.classList.add('active');
    isDown = true;
    startY = e.pageY - notification.offsetTop;
    scrollTop = notification.scrollTop;
  });
  notification.addEventListener('mouseleave', () => {
    isDown = false;
    notification.classList.remove('active');
  });
  notification.addEventListener('mouseup', () => {
    isDown = false;
    notification.classList.remove('active');
  });
  notification.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - notification.offsetTop;
      const walkY = y - startY;
      notification.scrollTop = scrollTop - walkY;
    }
  });
}

if (form != null) {
  /*** ICON ***/
  form.addEventListener('mousedown', (e) => {
    form.classList.add('active');
    isDown = true;
    startY = e.pageY - form.offsetTop;
    scrollTop = form.scrollTop;
  });
  form.addEventListener('mouseleave', () => {
    isDown = false;
    form.classList.remove('active');
  });
  form.addEventListener('mouseup', () => {
    isDown = false;
    form.classList.remove('active');
  });
  form.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - form.offsetTop;
      const walkY = y - startY;
      form.scrollTop = scrollTop - walkY;
    }
  });
}

if (statistics != null) {
  /*** STATISTICS ***/
  statistics.addEventListener('mousedown', (e) => {
    statistics.classList.add('active');
    isDown = true;
    startY = e.pageY - statistics.offsetTop;
    scrollTop = statistics.scrollTop;
  });
  statistics.addEventListener('mouseleave', () => {
    isDown = false;
    statistics.classList.remove('active');
  });
  statistics.addEventListener('mouseup', () => {
    isDown = false;
    statistics.classList.remove('active');
  });
  statistics.addEventListener('mousemove', (e) => {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - statistics.offsetTop;
      const walkY = y - startY;
      statistics.scrollTop = scrollTop - walkY;
    }
  });
}