<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title><?php echo $settings_r['site_title'] ?>-ROOMS</title>

</head>

<body class="bg-light">
    <?php require('inc/header.php');

    $checkin_default = "";
    $checkout_default = "";
    $adult_default = "";
    $children_default = "";
    if (isset($_GET['check_availability'])) {
        $frm_data = filteration($_GET);
        $checkin_default = $frm_data['checkin'];
        $checkout_default = $frm_data['checkout'];
        $adult_default = $frm_data['adult'];
        $children_default = $frm_data['children'];
    }
    ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
        <div class="h-line bg-dark"></div>

    </div>

    <div class="container-fluid">
        <div class="text-end mb-4">
            <select class="form-select shadow-none w-25 ms-auto" aria-label="Disabled select example" onchange="search_room(this.value)">
                <option selected disabled>--Choose Room Type--</option>
                <?php
                $res11 = select("SELECT * FROM `rooms` WHERE `removed`=?", [0], 'i');
                while ($arr = mysqli_fetch_assoc($res11)) {
                    ?>
                    
                    <option value="<?php echo $arr['name']; ?>"><?php echo $arr['name']; ?> </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-12 mb-4 mb-lg-0 ps-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-2">FILTERS</h4>
                        <button class="navbar-toggler shadow none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse flex-column mt-2 align-items-stretch" id="filterDropdown">
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                                    <span>CHECK AVAILABILITY</span>
                                    <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>
                                <label class="form-label">Check-in</label>
                                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>" id="checkin" onchange="chk_avail_filter()">
                                <label class="form-label">Check-out</label>
                                <input type="date" class="form-control shadow-none" value="<?php echo $checkout_default ?>" id="checkout" onchange="chk_avail_filter()">
                            </div>

                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                                    <span>FACILITIES</span>
                                    <button id="facilities_btn" onclick="facilities_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>
                                <?php
                                $facilities_q = selectAll('facilities');
                                while ($row = mysqli_fetch_assoc($facilities_q)) {
                                    echo <<<facilities
                                            <div class="mb-2">
                                                <input type="checkbox" onclick="fetch_rooms()" name="facilities" value="$row[id]" id="$row[id]" class="form-check-input shadow-none me-1">
                                                <label class="form-check-label" for="$row[id]">$row[name]</label>
                                            </div>
                                        facilities;
                                }
                                ?>
                            </div>

                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size:18px;">
                                    <span>GUESTS</span>
                                    <button id="guests_btn" onclick="guests_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <label class="form-label">Adults</label>
                                        <input type="number" min="1" id="adults" value="<?php echo $adult_default ?>" class="form-control shadow-none" oninput="guests_filter()">
                                    </div>
                                    <div>
                                        <label class="form-label">Children</label>
                                        <input type="number" min="1" id="children" value="<?php echo $children_default ?>" class="form-control shadow-none" oninput="guests_filter()">
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                </nav>
            </div>


            <div class="col-lg-9 col-md-12 px-4" id="rooms-data">

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        let rooms_data = document.getElementById('rooms-data');
        let checkin = document.getElementById('checkin');
        let checkout = document.getElementById('checkout');
        let chk_avail_btn = document.getElementById('chk_avail_btn');

        let adults = document.getElementById('adults');
        let children = document.getElementById('children');
        let guests_btn = document.getElementById('guests_btn')

        let facilities_btn = document.getElementById('facilities_btn')

        function fetch_rooms() {

            let chk_avail = JSON.stringify({
                checkin: checkin.value,
                checkout: checkout.value
            });

            let guests = JSON.stringify({
                adults: adults.value,
                children: children.value
            })

            let facility_list = {
                "facilities": []
            };
            let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
            if (get_facilities.length > 0) {
                get_facilities.forEach((facility) => {
                    facility_list.facilities.push(facility.value);
                });
                facilities_btn.classList.remove('d-none');
            } else {
                facilities_btn.classList.add('d-none');
            }
            facility_list = JSON.stringify(facility_list);

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "ajax/rooms_crud.php?fetch_rooms&chk_avail=" + chk_avail + "&guests=" + guests + "&facility_list=" + facility_list, true);

            xhr.onprogress = function() {
                rooms_data.innerHTML = `<div class="spinner-border text-info mb-3 mx-auto d-block" id="loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`;
            }

            xhr.onload = function() {
                rooms_data.innerHTML = this.responseText;
            }
            xhr.send();
        }

        function chk_avail_filter() {
            if (checkin.value != '' && checkout.value != '') {
                fetch_rooms();
                chk_avail_btn.classList.remove('d-none');
            }
        }

        function chk_avail_clear() {
            checkin.value = '';
            checkout.value = '';
            chk_avail_btn.classList.add('d-none');
            fetch_rooms();
        }

        function guests_filter() {
            if (adults.value > 0 || children.value > 0) {
                fetch_rooms();
                guests_btn.classList.remove('d-none');
            }
        }

        function guests_clear() {
            adults.value = '';
            children.value = '';
            guests_btn.classList.remove('d-none');
            fetch_rooms();
        }

        function facilities_clear() {
            let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
            get_facilities.forEach((facility) => {
                facility.checked = false;
            });
            facilities_btn.classList.add('d-none');
            fetch_rooms();
        }

        function search_room(roomname) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                document.getElementById('rooms-data').innerHTML = this.responseText;
            }

            xhr.send('search_room&name=' + roomname);
        }

        window.onload = function() {
            fetch_rooms();
        }
    </script>

    <?php require('inc/footer.php'); ?>

</body>

</html>