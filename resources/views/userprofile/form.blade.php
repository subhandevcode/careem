<x-app-layout>
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Form</title>

    <!-- <script src="https://js.stripe.com/v3/"></script> -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="/assets/form.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />


</head>

<body>

    @if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h1>User Search</h1>
    
    <!-- Nearby Users Section -->
    <div id="users-list-section" style="display: none;">
        <h3>Nearby Users</h3>
        <ul id="users-list-container">
            <!-- Dynamically populated list of users -->
        </ul>
    </div>

    <form method="POST" action="{{ route('userprofile.update') }}">
        @csrf

        <h3>Home Location</h3>
        <input type="text" name="home_location" id="home_location" placeholder="e.g., Kharadar, Karachi"
            value="{{ old('home_location', $user->home_location) }}">
        <input type="hidden" name="home_lat" id="home_lat" value="{{ old('home_lat', $user->home_lat) }}">
        <input type="hidden" name="home_lng" id="home_lng" value="{{ old('home_lng', $user->home_lng) }}">

        <h3>Office Location</h3>
        <input type="text" name="office_location" id="office_location" placeholder="e.g., Defence Phase 2, Karachi"
            value="{{ old('office_location', $user->office_location) }}">
        <input type="hidden" name="office_lat" id="office_lat" value="{{ old('office_lat', $user->office_lat) }}">
        <input type="hidden" name="office_lng" id="office_lng" value="{{ old('office_lng', $user->office_lng) }}">

        <h3>Office Timings</h3>
        <input type="text" name="office_timings" value="{{ old('office_timings', $user->office_timings) }}">

        <h3>Vehicle Info</h3>
        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $user->vehicle_type) }}">
        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $user->vehicle_number) }}">

        <!-- <button type="submit">Update</button> -->
    </form>

    <h3>Route Preview (Home → Office)</h3>
    <div id="map"></div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>

    <script>
        let map, homeMarker, officeMarker, routingControl;

        async function getCoordinates(query) {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=pk&limit=1&q=${encodeURIComponent(query + ', Pakistan')}`);
            const data = await res.json();
            if (data.length > 0) {
                return {
                    lat: parseFloat(data[0].lat),
                    lng: parseFloat(data[0].lon)
                };
            }
            return null;
        }

        async function reverseGeocode(lat, lng) {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            return data.display_name || '';
        }

        // Handle Home Location Change
        document.getElementById('home_location').addEventListener('change', async function () {
            const coords = await getCoordinates(this.value);
            if (coords) {
                homeMarker.setLatLng([coords.lat, coords.lng]);
                document.getElementById('home_lat').value = coords.lat;
                document.getElementById('home_lng').value = coords.lng;
                map.setView([coords.lat, coords.lng], 13);
                updateRoute();
                getNearbySuggestions(coords.lat, coords.lng, 'home');
            }
        });

        // Handle Office Location Change
        document.getElementById('office_location').addEventListener('change', async function () {
            const coords = await getCoordinates(this.value);
            if (coords) {
                officeMarker.setLatLng([coords.lat, coords.lng]);
                document.getElementById('office_lat').value = coords.lat;
                document.getElementById('office_lng').value = coords.lng;
                map.setView([coords.lat, coords.lng], 13);
                updateRoute();
                getNearbySuggestions(coords.lat, coords.lng, 'office');
            }
        });

async function getNearbySuggestions(lat, lng, locationType) {
    try {
        const res = await fetch(`/api/nearby-users?lat=${lat}&lng=${lng}&locationType=${locationType}`);
        const data = await res.json();

        const usersListContainer = document.getElementById('users-list-container');
        usersListContainer.innerHTML = ''; // Clear previous content

        if (data.length > 0) {
            // Add each user to the list with a "Chat" button if they have an active subscription
            data.forEach(user => {
                const userItem = document.createElement('li');
                userItem.textContent = user.name; // User name

                // Check if the user has an active subscription
                if (user.has_active_subscription) {
                    // Create "Chat" button for each user
                    const chatButton = document.createElement('button');
                    chatButton.textContent = 'Chat';
                    chatButton.classList.add('chat-button');
                    chatButton.setAttribute('data-user-id', user.id);  // Store user ID in button attribute
                    chatButton.addEventListener('click', () => openChat(user.id));  // Event listener for opening chat
                    
                    userItem.appendChild(chatButton);
                } else {
                    // Show a "Subscribe" button if no active subscription
                    const subscribeButton = document.createElement('button');
                    subscribeButton.textContent = 'Subscribe';
                    subscribeButton.classList.add('subscribe-button');
                    subscribeButton.setAttribute('data-user-id', user.id);
                    subscribeButton.addEventListener('click', () => openSubscriptionPage(user.id));  // Redirect to subscription page
                    
                    userItem.appendChild(subscribeButton);
                }

                usersListContainer.appendChild(userItem);
            });

            // Show the users list section
            document.getElementById('users-list-section').style.display = 'block';
        } else {
            usersListContainer.innerHTML = '<li>No nearby users found within 1 km.</li>';
            document.getElementById('users-list-section').style.display = 'block';
        }
    } catch (error) {
        console.log("Nearby API error:", error);
    }
}
// Function to redirect user to the subscription page when they click "Subscribe"
function openSubscriptionPage(userId) {
    // Redirect to the subscription page, passing the user ID
    window.location.href = `/payment/${userId}`;
}

        function initMap(homeLat, homeLng, officeLat, officeLng) {
            map = L.map('map').setView([homeLat, homeLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            homeMarker = L.marker([homeLat, homeLng], { draggable: true }).addTo(map).bindPopup("Home").openPopup();
            officeMarker = L.marker([officeLat, officeLng], { draggable: true }).addTo(map).bindPopup("Office");

            routingControl = L.Routing.control({
                waypoints: [L.latLng(homeLat, homeLng), L.latLng(officeLat, officeLng)],
                routeWhileDragging: false,
                draggableWaypoints: false,
                addWaypoints: false
            }).addTo(map);

            homeMarker.on('dragend', updateRoute);
            officeMarker.on('dragend', updateRoute);
        }

        function updateRoute() {
            const newHome = homeMarker.getLatLng();
            const newOffice = officeMarker.getLatLng();

            document.getElementById('home_lat').value = newHome.lat;
            document.getElementById('home_lng').value = newHome.lng;
            document.getElementById('office_lat').value = newOffice.lat;
            document.getElementById('office_lng').value = newOffice.lng;

            routingControl.setWaypoints([newHome, newOffice]);
            getNearbySuggestions(newHome.lat, newHome.lng);
        }

        window.onload = function () {
            let homeLat = parseFloat(document.getElementById('home_lat').value);
            let homeLng = parseFloat(document.getElementById('home_lng').value);
            let officeLat = parseFloat(document.getElementById('office_lat').value);
            let officeLng = parseFloat(document.getElementById('office_lng').value);

            const hasStoredLocation = !isNaN(homeLat) && !isNaN(homeLng) && !isNaN(officeLat) && !isNaN(officeLng);

            if (hasStoredLocation) {
                initMap(homeLat, homeLng, officeLat, officeLng);
                getNearbySuggestions(homeLat, homeLng);
            } else if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async pos => {
                    const userLat = pos.coords.latitude;
                    const userLng = pos.coords.longitude;

                    const homeAddress = await reverseGeocode(userLat, userLng);
                    const officeAddress = await reverseGeocode(userLat, userLng);

                    document.getElementById('home_lat').value = userLat;
                    document.getElementById('home_lng').value = userLng;
                    document.getElementById('office_lat').value = userLat;
                    document.getElementById('office_lng').value = userLng;

                    document.getElementById('home_location').value = homeAddress;
                    document.getElementById('office_location').value = officeAddress;

                    initMap(userLat, userLng, userLat, userLng);
                    getNearbySuggestions(userLat, userLng);
                }, () => {
                    alert("Location not allowed. Using Karachi.");
                    initMap(24.8607, 67.0011, 24.8138, 67.0305);
                });
            } else {
                alert("Geolocation not supported.");
                initMap(24.8607, 67.0011, 24.8138, 67.0305);
            }

            document.getElementById('home_location').addEventListener('change', () => {
                handleLocationInput('home_location', 'home');
            });
            document.getElementById('office_location').addEventListener('change', () => {
                handleLocationInput('office_location', 'office');
            });
        };
    </script>
</body>

</html>
</x-app-layout>