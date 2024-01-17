var form_1 = document.querySelector(".form_1");
var form_2 = document.querySelector(".form_2");
var form_3 = document.querySelector(".form_3");


var form_1_btns = document.querySelector(".form_1_btns");
var form_2_btns = document.querySelector(".form_2_btns");
var form_3_btns = document.querySelector(".form_3_btns");


var form_1_next_btn = document.querySelector(".form_1_btns .btn_next");
var form_2_back_btn = document.querySelector(".form_2_btns .btn_back");
var form_2_next_btn = document.querySelector(".form_2_btns .btn_next");
var form_3_back_btn = document.querySelector(".form_3_btns .btn_back");

var form_2_progessbar = document.querySelector(".form_2_progessbar");
var form_3_progessbar = document.querySelector(".form_3_progessbar");

var btn_done = document.querySelector(".btn_done");
var modal_wrapper = document.querySelector(".modal_wrapper");
var shadow = document.querySelector(".shadow");

form_1_next_btn.addEventListener("click", function(){
	form_1.style.display = "none";
	form_2.style.display = "block";

	form_1_btns.style.display = "none";
	form_2_btns.style.display = "flex";

	form_2_progessbar.classList.add("active");
});

form_2_back_btn.addEventListener("click", function(){
	form_1.style.display = "block";
	form_2.style.display = "none";

	form_1_btns.style.display = "flex";
	form_2_btns.style.display = "none";

	form_2_progessbar.classList.remove("active");
});

form_2_next_btn.addEventListener("click", function(){
	form_2.style.display = "none";
	form_3.style.display = "block";

	form_3_btns.style.display = "flex";
	form_2_btns.style.display = "none";

	form_3_progessbar.classList.add("active");
});

form_3_back_btn.addEventListener("click", function(){
	form_2.style.display = "block";
	form_3.style.display = "none";

	form_3_btns.style.display = "none";
	form_2_btns.style.display = "flex";

	form_3_progessbar.classList.remove("active");
});

btn_done.addEventListener("click", function(){
	modal_wrapper.classList.add("active");
})

shadow.addEventListener("click", function(){
	modal_wrapper.classList.remove("active");
})
function uploadImage() {
    const uploadForm = document.getElementById("uploadForm");
    const imageInput = document.getElementById("imageInput");
    const uploadedImage = document.getElementById("uploadedImage");

    const formData = new FormData(uploadForm);

    fetch("upload.php", {
        method: "POST",
        body: formData,
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then((result) => {
        alert(result);
        uploadedImage.src = "uploads/" + imageInput.files[0].name;
    })
    .catch((error) => {
        console.error("Error:", error);
    });
}

  document.addEventListener('DOMContentLoaded', function () {
    var map;
  
    function initMap() {
      map = new ol.Map({
        target: 'map',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM()
          })
        ],
        view: new ol.View({
          center: ol.proj.fromLonLat([0, 0]),
          zoom: 2
        })
      });
  
      map.on('click', function (event) {
        getAddressFromLatLng(event.coordinate);
      });
    }
  
    function openMap(latitude, longitude) {
      var defaultLocation = ol.proj.fromLonLat([longitude || 0, latitude || 0]);
      map.getView().setCenter(defaultLocation);
      map.getView().setZoom(15);
    }
  
    function getAddressFromLatLng(coordinate) {
      var lonLat = ol.proj.toLonLat(coordinate);
      var apiUrl = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lonLat[1] + '&lon=' + lonLat[0];
  
      fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
          fillAddressFields(data.address);
        })
        .catch(error => {
          console.error('Error fetching address:', error);
        });
    }
  
    function fillAddressFields(address) {
      document.getElementById('addressLine1').value = address?.road || '';
      document.getElementById('addressLine2').value = address?.address29 || '';
      document.getElementById('city').value = address?.city || '';
      document.getElementById('state').value = address?.state || '';
      document.getElementById('postalCode').value = address?.postcode || '';
      document.getElementById('country').value = address?.country || '';
    }
  
    window.useCurrentLocation = function () {
      navigator.geolocation.getCurrentPosition(function (position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        openMap(latitude, longitude);
        getAddressFromLatLng([longitude, latitude]);
      }, function (error) {
        console.error('Error getting current location:', error.message);
      });
    };
  
    // Initialize the map once the document is fully loaded
    initMap();
  });
  function submitForm(event) {
    alert("Address Submitted Successfully")
    form_2.style.display = "none";
    form_3.style.display = "block";

    form_3_btns.style.display = "flex";
    form_2_btns.style.display = "none";

    form_3_progessbar.classList.add("active");
  }
  
    