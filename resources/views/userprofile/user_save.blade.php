<x-app-layout>
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Form</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f8;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #34495e;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            margin-bottom: 30px;
        }

        h3 {
            font-size: 16px;
            color: #34495e;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3498db;
        }

        #map {
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Nearby Users Section */
        #users-list-section {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            display: none;
        }

        #users-list-container {
            list-style-type: none;
            padding: 0;
            font-size: 14px;
        }

        #users-list-container li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            color: #34495e;
        }

        #users-list-container li:last-child {
            border-bottom: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                padding: 15px;
                max-width: 100%;
            }

            button {
                font-size: 14px;
            }

            h3 {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    @if(session('success'))
    <p style="color: green; text-align: center;">{{ session('success') }}</p>
    @endif

    <h1>User Profile</h1>

    <!-- Nearby Users Section -->
    <div id="users-list-section">
        <h3>Nearby Users</h3>
        <ul id="users-list-container">
            <!-- Dynamically populated list of users -->
        </ul>
    </div>

    <form method="POST" action="{{ route('user.save.update') }}">
        @csrf

        <h3>Home Location</h3>
        <input type="text" name="home_location" id="home_location" placeholder="e.g., Kharadar, Karachi" value="{{ old('home_location', $user->home_location) }}">
        <input type="hidden" name="home_lat" id="home_lat" value="{{ old('home_lat', $user->home_lat) }}">
        <input type="hidden" name="home_lng" id="home_lng" value="{{ old('home_lng', $user->home_lng) }}">

        <h3>Office Location</h3>
        <input type="text" name="office_location" id="office_location" placeholder="e.g., Defence Phase 2, Karachi" value="{{ old('office_location', $user->office_location) }}">
        <input type="hidden" name="office_lat" id="office_lat" value="{{ old('office_lat', $user->office_lat) }}">
        <input type="hidden" name="office_lng" id="office_lng" value="{{ old('office_lng', $user->office_lng) }}">

        <h3>Office Timings</h3>
        <input type="text" name="office_timings" value="{{ old('office_timings', $user->office_timings) }}">

        <h3>Vehicle Info</h3>
        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $user->vehicle_type) }}">
        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $user->vehicle_number) }}">

        <button type="submit">Update</button>
    </form>

    <h3>Route Preview (Home → Office)</h3>
    <div id="map"></div>

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
        }

        async function handleLocationInput(inputId, markerType) {
            const query = document.getElementById(inputId).value;
            const coords = await getCoordinates(query);
            if (coords) {
                const latlng = [coords.lat, coords.lng];
                if (markerType === 'home') {
                    homeMarker.setLatLng(latlng);
                    document.getElementById('home_lat').value = coords.lat;
                    document.getElementById('home_lng').value = coords.lng;
                } else {
                    officeMarker.setLatLng(latlng);
                    document.getElementById('office_lat').value = coords.lat;
                    document.getElementById('office_lng').value = coords.lng;
                }
                map.setView(latlng, 13);
                updateRoute();
            }
        }

        window.onload = function () {
            let homeLat = parseFloat(document.getElementById('home_lat').value);
            let homeLng = parseFloat(document.getElementById('home_lng').value);
            let officeLat = parseFloat(document.getElementById('office_lat').value);
            let officeLng = parseFloat(document.getElementById('office_lng').value);

            const hasStoredLocation = !isNaN(homeLat) && !isNaN(homeLng) && !isNaN(officeLat) && !isNaN(officeLng);

            if (hasStoredLocation) {
                initMap(homeLat, homeLng, officeLat, officeLng);
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
                }, () => {
                    alert("Location not allowed. Default Karachi used.");
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