/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';

// start the Stimulus application
import './bootstrap';

console.log("Nico dans /assets/app.js");

var ctx = document.getElementById("cityList");
// const essai =JSON.parse( ctx.dataset.lab);
// const essai =JSON.parse(ctx.dataset.test);
// console.log(essai);

document.querySelector('#addCity').addEventListener('click', function () {

    document.querySelector('#cityList').innerHTML += `
    <div class="cityContainer">
    <p class="name">Ville</p>
    <p class="description">Descritpion</p>     
    <img class="weatherIcon" src="{{ asset('/images/'~city.weather.0.main~'.png') }}" alt="ACME logo">
    {# TEMPERATURES #}
    <div class="temperature">
                  <p class="tempMin">{{city.main.temp_min}} °C</p>
                  <span>-</span>
                  <p class="tempMax">{{city.main.temp_max}} °C</p>
            </div>  
    {# DELETE #}
    <button class="deleteCity">Delete</button>
</div>
					`
})

