const prayerName = document.querySelector('.prayer-name');
const islamicDate = document.querySelector('.islamic-date');
const christianDate = document.querySelector('.christian-date');
const nextPrayerName = document.querySelector('.next-prayer-name');
const nextPrayerTime = document.querySelector('.next-prayer-time');
const bgImageContainer = document.querySelector('.bg-image-container');
const videoContainer = document.querySelector('.video-container');
const prayerTime = document.querySelector('.prayer-time');


 
let nextPrayerIndex = 0; // Initialize the index of the next prayer to 0
let currentPrayerIndex = 0;
// Get user's location using Geolocation API
navigator.geolocation.getCurrentPosition(function(position) {
  const latitude = position.coords.latitude;
  const longitude = position.coords.longitude;

  // Call OpenCage Geocoding API to get city and country information
  const apiKey = 'c72f11306a23420b97a90e11a576da37';
  fetch(`https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`)
    .then(response => response.json())
    .then(data => {
      const results = data.results;
      if (results.length > 0) {
        const components = results[0].components;
        const city = components.city || components.town;
        const country = components.country;

        // Update the user's location in the DOM
        const userCityElement = document.querySelector('.user-city');
        const userCountryElement = document.querySelector('.user-country');
        userCityElement.textContent = city;
        userCountryElement.textContent = country;
      } else {
        console.error('Location data not found.');
      }
    })
    .catch(error => {
      console.error('Error fetching location:', error);
    });
});

function setBackground() {
  const now = new Date();
  const hour = now.getHours();
  let backgroundImage = 'url(img/isha.jpg)';
  let prayerIndex = 0;

 
  fetch('https://api.aladhan.com/v1/timingsByCity?city=Dakar&country=Senegal&method=2')
    .then(response => response.json())
    .then(data => {
      const prayerTimes = data.data.timings;

      if (hour >= 5 && hour < 7) {
        // Fajr
        backgroundImage = 'url(img/fadjr-1.jpg)';
        prayerIndex = 0;
      } else if (hour >= 7 && hour < 8) {
        // Sunrise to Dhuhr
        backgroundImage = 'url(img/sunrise.jpg)';
        prayerIndex = 1;
      } else if(hour >=8 && hour < 13) {
        //sunny but not part of the prayers
        backgroundImage = 'url(img/sunrise-2.jpg)';
        prayerIndex = 2;
      } else if (hour >= 13 && hour < 16) {
        // Dhuhr to Asr
        backgroundImage = 'url(img/sunrise-2.jpg)';
        prayerIndex = 2;
      } else if (hour >= 16 && hour < 19) {
        // Asr to Maghrib
        backgroundImage = 'url(img/asr.jpg)';
        prayerIndex = 3;
      } else if (hour >= 19 && hour < 20) {
        // Maghrib to Isha
        backgroundImage = 'url(img/maghrib.jpg)';
        prayerIndex = 4;
      } else {
        // Isha to Fajr
        backgroundImage = 'url(img/isha.jpg)';
        prayerIndex = 5;
      }

      // set the background image
      bgImageContainer.style.backgroundImage = backgroundImage;

      // update the prayer timing
      prayerName.textContent = Object.keys(prayerTimes)[prayerIndex];
      nextPrayerName.textContent = Object.keys(prayerTimes)[(prayerIndex + 1) % Object.keys(prayerTimes).length];
      nextPrayerTime.textContent = Object.values(prayerTimes)[(prayerIndex + 1) % Object.values(prayerTimes).length];
    })
    .catch(error => {
      console.error('Error fetching prayer times:', error);
    });
}

// update the prayer timing and background image every 1 minute
setInterval(setBackground, 60000);

// initialize the prayer timing and background image
setBackground();


// Get the video element
const video = document.querySelector('.video-container video');

// Add a scroll event listener to the window
window.addEventListener('scroll', () => {
  // Check if the video element is in the viewport
  const rect = video.getBoundingClientRect();
  const isInView = rect.top < window.innerHeight && rect.bottom >= 0;

  // If the video is in view and not already playing, start playing it
  if (isInView && video.paused) {
    video.play();
  }
});

const readMoreBtns = document.querySelectorAll('.read-more-btn');
const readMoreTexts = document.querySelectorAll('.read-more-text');

readMoreBtns.forEach(function(btn, index) {
btn.addEventListener('click', function() {
if (readMoreTexts[index].style.display === 'none') {
readMoreTexts[index].style.display = 'inline';
btn.innerHTML = 'Lire Moins...';
} else {
readMoreTexts[index].style.display = 'none';
btn.innerHTML = 'Lire la suite...';
}
});
});

$(window).on('scroll', function() {
  if ($(window).scrollTop() > 0) {
    $('header').addClass('sticky');
  } else {
    $('header').removeClass('sticky');
  }
});


function validateForm(event) {
  event.preventDefault(); // Prevent the default form submission

  // Validate form inputs
  let name = document.getElementById("name").value.trim();
  let email = document.getElementById("email_id").value.trim();
  let subject = document.getElementById("subject").value.trim();
  let message = document.getElementById("message").value.trim();

  if (name === "" || email === "" || subject === "" || message === "") {
    alert("Please fill in all fields");
    return false; // Stop form submission
  }

  let params = {
    from_name: name,
    email_id: email,
    subject: subject,
    message: message
  };
  
 
  
  emailjs.send("service_6vjjv6s", "template_0ix3ah7", params)
    .then(function (res) {
      // Utilisation de SweetAlert2 pour afficher une notification personnalisée de succès
      Swal.fire({
        icon: 'success',
        title: 'Email Envoyé',
        text: 'Votre message a été envoyé avec succès!',
      });
    })
    .catch(function (error) {
      // En cas d'erreur, vous pouvez également afficher une notification d'erreur
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Une erreur s\'est produite lors de l\'envoi de l\'email.',
      });
    })
    .finally(function () {
      // Cette partie sera exécutée quelle que soit la réussite ou l'échec de l'envoi
      
    });
  
  return false; // Prevent form submission (optional)
  }
  