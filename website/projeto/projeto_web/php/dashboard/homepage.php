<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/homepage.css">
    <link rel="stylesheet" type="text/css" href="../../css/roundslider.css">
    <script defer src="../../js/drag.js"></script>
    <script type="text/javascript" defer src="../../js/roundslider.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/form.css">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
</head>

<body>
    <div class="container-homepage">
        <!--Homepage-->
        <main>
            <div class="top">
                <div class="text">
                    <h1 id="greeting">Good Morning,</h1>
                    <h2>
                        <?= ucfirst($_SESSION['name']) ?>
                    </h2>
                </div>

                <!--Home Form Section-->
                <div class="button" onclick="openForm()">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                        <g>
                            <path d="M34.283,384c17.646,30.626,56.779,41.148,87.405,23.502c0.021-0.012,0.041-0.024,0.062-0.036l9.493-5.483   c17.92,15.332,38.518,27.222,60.757,35.072V448c0,35.346,28.654,64,64,64s64-28.654,64-64v-10.944   c22.242-7.863,42.841-19.767,60.757-35.115l9.536,5.504c30.633,17.673,69.794,7.167,87.467-23.467   c17.673-30.633,7.167-69.794-23.467-87.467l0,0l-9.472-5.461c4.264-23.201,4.264-46.985,0-70.187l9.472-5.461   c30.633-17.673,41.14-56.833,23.467-87.467c-17.673-30.633-56.833-41.14-87.467-23.467l-9.493,5.483   C362.862,94.638,342.25,82.77,320,74.944V64c0-35.346-28.654-64-64-64s-64,28.654-64,64v10.944   c-22.242,7.863-42.841,19.767-60.757,35.115l-9.536-5.525C91.073,86.86,51.913,97.367,34.24,128s-7.167,69.794,23.467,87.467l0,0   l9.472,5.461c-4.264,23.201-4.264,46.985,0,70.187l-9.472,5.461C27.158,314.296,16.686,353.38,34.283,384z M256,170.667   c47.128,0,85.333,38.205,85.333,85.333S303.128,341.333,256,341.333S170.667,303.128,170.667,256S208.872,170.667,256,170.667z" />
                        </g>
                    </svg>
                    <p>Device/Room Settings</p>
                </div>

                <div class="form-popup" id="myForm">
                    <div class="form-container">
                        <div class="side-menu">
                            <label for="Device Options">Device Options</label>
                            <a id="toggle-add-device">Add Device</a>
                            <a id="toggle-edit-device">Edit Device</a>
                            <a id="toggle-move-device">Move Device</a>
                            <a id="toggle-remove-device">Remove Device</a>
                            <label for="Room Options">Room Options</label>
                            <a id="toggle-add-room">Add Room</a>
                            <a id="toggle-edit-room">Edit Room</a>
                            <a id="toggle-remove-room">Remove Room</a>
                        </div>
                        <div class="form-menu">

                            <!-- ADD DEVICE/ROOM FORM -->
                            <form action="../add.php" class="form" id="add" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Add Device</h1>

                                    <label for="room"><b>Choose a room (or create one)</b></label>
                                    <input list="rooms" name="room" id="room" placeholder="Enter room name" required>

                                    <datalist id="rooms">
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <option value="<?php echo $row['Name']; ?>">
                                            <?php }
                                            ?>
                                    </datalist>

                                    <label for="device"><b>Choose a device</b></label>
                                    <select name="device" id="device" class="select" required>
                                        <option disabled selected value>Select a device</option>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM device
                                        WHERE room_ID IS NULL; ";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <option value="<?php echo $row['ID']; ?>"><?php echo $row['IP']; ?></option>
                                        <?php }
                                        ?>
                                    </select>

                                    <label for="name"><b>New name for the device</b></label>
                                    <input type="text" id="text" placeholder="Enter device name" name="name" required>

                                    <label for="consumption"><b>Power consumption in watts (optional)</b></label>
                                    <input type="number" id="number" placeholder="Enter power consumption" name="consumption">

                                    <label for="icon"><b>Choose an icon for the device</b></label>
                                    <div class="container-device-icon">
                                        <div class="container-overflow">
                                            <label>
                                                <input type="radio" name="icon" id="icon_1" value="bulb.svg" required />
                                                <div class="border">
                                                    <svg alt="Option 1" id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                                                        <path d="m17.994 2.286a9 9 0 0 0 -14.919 5.536 8.938 8.938 0 0 0 2.793 7.761 6.263 6.263 0 0 1 2.132 4.566v.161a3.694 3.694 0 0 0 3.69 3.69h.62a3.694 3.694 0 0 0 3.69-3.69v-.549a5.323 5.323 0 0 1 1.932-4 8.994 8.994 0 0 0 .062-13.477zm-5.684 19.714h-.62a1.692 1.692 0 0 1 -1.69-1.69s-.007-.26-.008-.31h4.008v.31a1.692 1.692 0 0 1 -1.69 1.69zm4.3-7.741a7.667 7.667 0 0 0 -2.364 3.741h-1.246v-7.184a3 3 0 0 0 2-2.816 1 1 0 0 0 -2 0 1 1 0 0 1 -2 0 1 1 0 0 0 -2 0 3 3 0 0 0 2 2.816v7.184h-1.322a8.634 8.634 0 0 0 -2.448-3.881 7 7 0 0 1 3.951-12.073 7.452 7.452 0 0 1 .828-.046 6.921 6.921 0 0 1 4.652 1.778 6.993 6.993 0 0 1 -.048 10.481z" />
                                                    </svg>
                                                </div>
                                            </label>

                                            <label>
                                                <input type="radio" name="icon" id="icon_2" value="ac.svg" />
                                                <div class="border">
                                                    <svg alt="Option 2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 512 512">
                                                        <path d="M472.898,287.547l-52.236,12.448c-6.571,1.673-12.857,4.315-18.651,7.839l-42.485-26.086  c4.588-16.857,4.588-34.635,0-51.492l42.485-26.086c5.794,3.524,12.08,6.166,18.651,7.839c0,0,57.185,13.319,59.649,13.319  c17.597-0.168,31.726-14.57,31.558-32.168c-0.139-14.535-10.097-27.134-24.208-30.626l-52.321-12.512  c-1.188-0.289-2.204-1.059-2.804-2.124c-0.643-1.025-0.842-2.267-0.552-3.441l12.448-52.257c5.092-16.845-4.435-34.629-21.28-39.721  c-16.845-5.092-34.629,4.435-39.721,21.28c-0.378,1.249-0.678,2.519-0.9,3.805l-12.448,52.257c-1.46,6.549-1.94,13.278-1.423,19.968  l-42.485,26.192c-10.921-9.405-23.652-16.476-37.408-20.775v-50.027c6.029-3.055,11.558-7.009,16.399-11.726l38.003-37.982  c12.442-12.448,12.437-32.625-0.011-45.066c-12.448-12.442-32.625-12.437-45.066,0.011l0,0L260.11,48.418  c-1.758,1.76-4.609,1.762-6.369,0.004c-0.001-0.001-0.003-0.003-0.004-0.004l-38.024-38.003c-12.448-12.448-32.629-12.448-45.077,0  s-12.448,32.629,0,45.077l38.003,37.982c4.842,4.709,10.372,8.656,16.399,11.705v50.027c-13.726,4.338-26.42,11.437-37.302,20.86  l-42.592-26.277c0.532-6.739,0.052-13.52-1.423-20.117l-12.448-52.108c-3.806-17.181-20.82-28.024-38.001-24.218  c-17.181,3.806-28.024,20.82-24.218,38.001c0.073,0.328,0.151,0.655,0.234,0.98l12.448,52.257c0.29,1.175,0.091,2.417-0.552,3.441  c-0.6,1.066-1.616,1.835-2.804,2.124l-52.236,12.384C9.026,166.62-1.538,183.809,2.549,200.926  c3.42,14.327,16.215,24.443,30.944,24.465c2.464,0,59.649-13.319,59.649-13.319c6.571-1.673,12.857-4.315,18.651-7.839  l42.485,26.086c-4.588,16.857-4.588,34.635,0,51.492l-42.485,26.086c-5.794-3.524-12.08-6.166-18.651-7.839l-52.236-12.448  c-16.819-5.178-34.651,4.26-39.829,21.079c-5.178,16.819,4.26,34.651,21.079,39.829c1.307,0.402,2.639,0.72,3.986,0.951  l52.236,12.448c1.188,0.289,2.204,1.059,2.804,2.124c0.643,1.025,0.842,2.267,0.552,3.441L69.287,419.74  c-4.106,17.117,6.441,34.322,23.558,38.428c17.117,4.106,34.322-6.441,38.428-23.558l12.448-52.257  c1.477-6.604,1.957-13.392,1.423-20.138l42.592-26.192c10.882,9.423,23.576,16.522,37.302,20.86v49.942  c-6.025,3.071-11.553,7.031-16.399,11.747l-38.003,37.939c-12.448,12.448-12.448,32.629,0,45.077s32.629,12.448,45.077,0  l37.982-38.003c1.758-1.76,4.609-1.762,6.369-0.004c0.001,0.001,0.003,0.003,0.004,0.004l37.982,38.003  c12.448,12.448,32.629,12.448,45.077,0c12.448-12.448,12.448-32.629,0-45.077l-38.003-37.982  c-4.836-4.698-10.35-8.643-16.357-11.705v-50.027c13.726-4.338,26.42-11.437,37.302-20.86l42.485,26.192  c-0.532,6.739-0.052,13.52,1.423,20.117l12.448,52.257c4.106,17.117,21.311,27.664,38.428,23.558  c17.117-4.106,27.664-21.311,23.558-38.428l-12.448-52.257c-0.29-1.175-0.091-2.417,0.552-3.441c0.6-1.066,1.616-1.835,2.804-2.124  l52.236-12.448c17.117-4.106,27.664-21.311,23.558-38.428c-4.106-17.117-21.311-27.664-38.428-23.558l0,0L472.898,287.547z   M256.902,298.487c-23.464,0-42.485-19.021-42.485-42.485s19.021-42.485,42.485-42.485s42.485,19.021,42.485,42.485  S280.366,298.487,256.902,298.487z" />
                                                    </svg>
                                                </div>
                                            </label>

                                            <label>
                                                <input type="radio" name="icon" id="icon_3" value="thermostat.svg" />
                                                <div class="border">
                                                    <svg alt="Option 3" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                                        <path d="M12,14.184V5H10v9.184a3,3,0,1,0,2,0ZM11,18a1,1,0,1,1,1-1A1,1,0,0,1,11,18ZM16,5A5,5,0,0,0,6,5v7.111a7,7,0,1,0,10,0ZM11,22a4.994,4.994,0,0,1-3.332-8.719l.332-.3V5a3,3,0,0,1,6,0v7.983l.332.3A4.994,4.994,0,0,1,11,22ZM21,0a3,3,0,1,0,3,3A3,3,0,0,0,21,0Zm0,4a1,1,0,1,1,1-1A1,1,0,0,1,21,4Z" />
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="add" value="Create" class="btn">Create</button>
                            </form>

                            <!-- EDIT DEVICE FORM -->
                            <form action="../edit.php" class="form" id="edit" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Edit Device</h1>
                                    <label for="device"><b>Choose the device</b></label>
                                    <select name="device" id="device" class="select" required>
                                        <option disabled selected value>Select a device</option>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE ID IN
                                            (SELECT room_ID
                                            FROM device) AND user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <optgroup label="<?php echo $row['Name']; ?>">
                                                <?php
                                                $room_ID = $row['ID'];
                                                $sql2 = "SELECT *
                                                FROM device
                                                WHERE room_ID = :ROOM_ID;";
                                                $stmt2 = $pdo->prepare($sql2);
                                                $stmt2->bindValue(":ROOM_ID", $room_ID, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                while ($row2 = $stmt2->fetch()) {
                                                ?>
                                                    <option value="<?php echo $row2['ID']; ?>"><?php echo $row2['Name']; ?></option>
                                                <?php }
                                                ?>
                                            </optgroup>
                                        <?php }
                                        ?>
                                    </select>

                                    <label for="name"><b>New name for the device</b></label>
                                    <input type="text" id="text" placeholder="Enter new name" name="name" required>

                                    <label for="consumption"><b>Power consumption in watts (optional)</b></label>
                                    <input type="number" id="number" placeholder="Enter power consumption" name="consumption">

                                    <label for="icon"><b>Choose an icon for the device</b></label>
                                    <div class="container-device-icon">
                                        <div class="container-overflow">
                                            <label>
                                                <input type="radio" name="icon" id="icon_1" value="bulb.svg" required />
                                                <div class="border">
                                                    <svg alt="Option 1" id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                                                        <path d="m17.994 2.286a9 9 0 0 0 -14.919 5.536 8.938 8.938 0 0 0 2.793 7.761 6.263 6.263 0 0 1 2.132 4.566v.161a3.694 3.694 0 0 0 3.69 3.69h.62a3.694 3.694 0 0 0 3.69-3.69v-.549a5.323 5.323 0 0 1 1.932-4 8.994 8.994 0 0 0 .062-13.477zm-5.684 19.714h-.62a1.692 1.692 0 0 1 -1.69-1.69s-.007-.26-.008-.31h4.008v.31a1.692 1.692 0 0 1 -1.69 1.69zm4.3-7.741a7.667 7.667 0 0 0 -2.364 3.741h-1.246v-7.184a3 3 0 0 0 2-2.816 1 1 0 0 0 -2 0 1 1 0 0 1 -2 0 1 1 0 0 0 -2 0 3 3 0 0 0 2 2.816v7.184h-1.322a8.634 8.634 0 0 0 -2.448-3.881 7 7 0 0 1 3.951-12.073 7.452 7.452 0 0 1 .828-.046 6.921 6.921 0 0 1 4.652 1.778 6.993 6.993 0 0 1 -.048 10.481z" />
                                                    </svg>
                                                </div>
                                            </label>

                                            <label>
                                                <input type="radio" name="icon" id="icon_2" value="ac.svg" />
                                                <div class="border">
                                                    <svg alt="Option 2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 512 512">
                                                        <path d="M472.898,287.547l-52.236,12.448c-6.571,1.673-12.857,4.315-18.651,7.839l-42.485-26.086  c4.588-16.857,4.588-34.635,0-51.492l42.485-26.086c5.794,3.524,12.08,6.166,18.651,7.839c0,0,57.185,13.319,59.649,13.319  c17.597-0.168,31.726-14.57,31.558-32.168c-0.139-14.535-10.097-27.134-24.208-30.626l-52.321-12.512  c-1.188-0.289-2.204-1.059-2.804-2.124c-0.643-1.025-0.842-2.267-0.552-3.441l12.448-52.257c5.092-16.845-4.435-34.629-21.28-39.721  c-16.845-5.092-34.629,4.435-39.721,21.28c-0.378,1.249-0.678,2.519-0.9,3.805l-12.448,52.257c-1.46,6.549-1.94,13.278-1.423,19.968  l-42.485,26.192c-10.921-9.405-23.652-16.476-37.408-20.775v-50.027c6.029-3.055,11.558-7.009,16.399-11.726l38.003-37.982  c12.442-12.448,12.437-32.625-0.011-45.066c-12.448-12.442-32.625-12.437-45.066,0.011l0,0L260.11,48.418  c-1.758,1.76-4.609,1.762-6.369,0.004c-0.001-0.001-0.003-0.003-0.004-0.004l-38.024-38.003c-12.448-12.448-32.629-12.448-45.077,0  s-12.448,32.629,0,45.077l38.003,37.982c4.842,4.709,10.372,8.656,16.399,11.705v50.027c-13.726,4.338-26.42,11.437-37.302,20.86  l-42.592-26.277c0.532-6.739,0.052-13.52-1.423-20.117l-12.448-52.108c-3.806-17.181-20.82-28.024-38.001-24.218  c-17.181,3.806-28.024,20.82-24.218,38.001c0.073,0.328,0.151,0.655,0.234,0.98l12.448,52.257c0.29,1.175,0.091,2.417-0.552,3.441  c-0.6,1.066-1.616,1.835-2.804,2.124l-52.236,12.384C9.026,166.62-1.538,183.809,2.549,200.926  c3.42,14.327,16.215,24.443,30.944,24.465c2.464,0,59.649-13.319,59.649-13.319c6.571-1.673,12.857-4.315,18.651-7.839  l42.485,26.086c-4.588,16.857-4.588,34.635,0,51.492l-42.485,26.086c-5.794-3.524-12.08-6.166-18.651-7.839l-52.236-12.448  c-16.819-5.178-34.651,4.26-39.829,21.079c-5.178,16.819,4.26,34.651,21.079,39.829c1.307,0.402,2.639,0.72,3.986,0.951  l52.236,12.448c1.188,0.289,2.204,1.059,2.804,2.124c0.643,1.025,0.842,2.267,0.552,3.441L69.287,419.74  c-4.106,17.117,6.441,34.322,23.558,38.428c17.117,4.106,34.322-6.441,38.428-23.558l12.448-52.257  c1.477-6.604,1.957-13.392,1.423-20.138l42.592-26.192c10.882,9.423,23.576,16.522,37.302,20.86v49.942  c-6.025,3.071-11.553,7.031-16.399,11.747l-38.003,37.939c-12.448,12.448-12.448,32.629,0,45.077s32.629,12.448,45.077,0  l37.982-38.003c1.758-1.76,4.609-1.762,6.369-0.004c0.001,0.001,0.003,0.003,0.004,0.004l37.982,38.003  c12.448,12.448,32.629,12.448,45.077,0c12.448-12.448,12.448-32.629,0-45.077l-38.003-37.982  c-4.836-4.698-10.35-8.643-16.357-11.705v-50.027c13.726-4.338,26.42-11.437,37.302-20.86l42.485,26.192  c-0.532,6.739-0.052,13.52,1.423,20.117l12.448,52.257c4.106,17.117,21.311,27.664,38.428,23.558  c17.117-4.106,27.664-21.311,23.558-38.428l-12.448-52.257c-0.29-1.175-0.091-2.417,0.552-3.441c0.6-1.066,1.616-1.835,2.804-2.124  l52.236-12.448c17.117-4.106,27.664-21.311,23.558-38.428c-4.106-17.117-21.311-27.664-38.428-23.558l0,0L472.898,287.547z   M256.902,298.487c-23.464,0-42.485-19.021-42.485-42.485s19.021-42.485,42.485-42.485s42.485,19.021,42.485,42.485  S280.366,298.487,256.902,298.487z" />
                                                    </svg>
                                                </div>
                                            </label>

                                            <label>
                                                <input type="radio" name="icon" id="icon_3" value="thermostat.svg" />
                                                <div class="border">
                                                    <svg alt="Option 3" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                                        <path d="M12,14.184V5H10v9.184a3,3,0,1,0,2,0ZM11,18a1,1,0,1,1,1-1A1,1,0,0,1,11,18ZM16,5A5,5,0,0,0,6,5v7.111a7,7,0,1,0,10,0ZM11,22a4.994,4.994,0,0,1-3.332-8.719l.332-.3V5a3,3,0,0,1,6,0v7.983l.332.3A4.994,4.994,0,0,1,11,22ZM21,0a3,3,0,1,0,3,3A3,3,0,0,0,21,0Zm0,4a1,1,0,1,1,1-1A1,1,0,0,1,21,4Z" />
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="edit" value="Edit" class="btn">Save Changes</button>
                                </div>
                            </form>

                            <!-- MOVE DEVICE FORM -->
                            <form action="../edit.php" class="form" id="move" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Move Device</h1>
                                    <label for="device"><b>Choose the device(s) to move</b></label>
                                    <select name="device[]" id="multiple-device" class="select" multiple required>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE ID IN
                                            (SELECT room_ID
                                            FROM device) AND user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <optgroup label="<?php echo $row['Name']; ?>">
                                                <?php
                                                $room_ID = $row['ID'];
                                                $sql2 = "SELECT *
                                                FROM device
                                                WHERE room_ID = :ROOM_ID;";
                                                $stmt2 = $pdo->prepare($sql2);
                                                $stmt2->bindValue(":ROOM_ID", $room_ID, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                while ($row2 = $stmt2->fetch()) {
                                                ?>
                                                    <option value="<?php echo $row2['ID']; ?>"><?php echo $row2['Name']; ?></option>
                                                <?php }
                                                ?>
                                            </optgroup>
                                        <?php }
                                        ?>
                                    </select>

                                    <span class="tip">*Hold Ctrl(windows) or Command(Mac)<br>to select multiple options</span>

                                    <label for="room"><b>Choose a room to move the device(s) to</b></label>
                                    <input list="rooms" name="room" id="room" placeholder="Enter room name" required>

                                    <datalist id="rooms">
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <option value="<?php echo $row['Name']; ?>">
                                            <?php }
                                            ?>
                                    </datalist>

                                    <button type="submit" name="move" value="Move" class="btn">Save Changes</button>
                                </div>
                            </form>

                            <!-- DELETE DEVICE FORM -->
                            <form action="../delete.php" class="form" id="delete" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Remove Device</h1>
                                    <label for="device"><b>Choose the device</b></label>
                                    <select name="device" id="device" class="select" required>
                                        <option disabled selected value>Select a device</option>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE ID IN
                                            (SELECT room_ID
                                            FROM device) AND user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <optgroup label="<?php echo $row['Name']; ?>">
                                                <?php
                                                $room_ID = $row['ID'];
                                                $sql2 = "SELECT *
                                                FROM device
                                                WHERE room_ID = :ROOM_ID;";
                                                $stmt2 = $pdo->prepare($sql2);
                                                $stmt2->bindValue(":ROOM_ID", $room_ID, PDO::PARAM_INT);
                                                $stmt2->execute();
                                                while ($row2 = $stmt2->fetch()) {
                                                ?>
                                                    <option value="<?php echo $row2['ID']; ?>"><?php echo $row2['Name']; ?></option>
                                                <?php }
                                                ?>
                                            </optgroup>
                                        <?php }
                                        ?>
                                    </select>
                                    <span class="warning">Removing a device from a room will also remove <br> all of the device configurations!</span>
                                    <button type="submit" name="delete" value="Delete" class="btn">Save Changes</button>
                                </div>
                            </form>

                            <!-- ADD ROOM FORM -->
                            <form action="../add.php" class="form" id="add_room" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Add Room</h1>
                                    <label for="room"><b>Create a room</b></label>
                                    <input type="text" id="text" placeholder="Enter room name" name="name" required>
                                    <button type="submit" name="add_room" value="Add" class="btn">Create</button>
                                </div>
                            </form>

                            <!-- EDIT ROOM FORM -->
                            <form action="../edit.php" class="form" id="edit_room" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Edit Room</h1>
                                    <label for="room"><b>Choose the room</b></label>
                                    <select name="room" id="room" class="select" required>
                                        <option disabled selected value>Select a room</option>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <option value="<?php echo $row['ID']; ?>"><?php echo $row['Name']; ?></option>
                                        <?php }
                                        ?>
                                    </select>

                                    <label for="name"><b>New name for the room</b></label>
                                    <input type="text" id="text" placeholder="Enter new name" name="name" required>

                                    <button type="submit" name="edit_room" value="Edit" class="btn">Save Changes</button>
                                </div>
                            </form>

                            <!-- DELETE ROOM FORM -->
                            <form action="../delete.php" class="form" id="delete_room" method="POST">
                                <div class="cancel" onclick="closeForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.021 512.021" xml:space="preserve">
                                        <g>
                                            <path d="M301.258,256.01L502.645,54.645c12.501-12.501,12.501-32.769,0-45.269c-12.501-12.501-32.769-12.501-45.269,0l0,0   L256.01,210.762L54.645,9.376c-12.501-12.501-32.769-12.501-45.269,0s-12.501,32.769,0,45.269L210.762,256.01L9.376,457.376   c-12.501,12.501-12.501,32.769,0,45.269s32.769,12.501,45.269,0L256.01,301.258l201.365,201.387   c12.501,12.501,32.769,12.501,45.269,0c12.501-12.501,12.501-32.769,0-45.269L301.258,256.01z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="form-inputs">
                                    <h1>Remove Room</h1>
                                    <label for="room"><b>Choose the room</b></label>
                                    <select name="room" id="room" class="select" required>
                                        <option disabled selected value>Select a room</option>
                                        <?php
                                        $pdo = connectDB($db);
                                        $sql = "SELECT *
                                        FROM room
                                        WHERE user_ID = :REST_ID;";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch()) {
                                        ?>
                                            <option value="<?php echo $row['ID']; ?>"><?php echo $row['Name']; ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                    <span class="warning">Removing a room from the house will also remove <br> all of the devices assigned to it!</span>
                                    <button type="submit" name="delete_room" value="Delete" class="btn">Save Changes</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($_SESSION['selected_room'] == '') {
            ?>
                <div class="start-container">
                    <div class="start-div">
                        <span class="start-btn" onclick="openForm()"><img src="../../images/icons/dashboard/startadding.png" alt="">Start your smart home</span>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <!--Home Room Section-->
                <div id="room" class="room">
                    <?php
                    $pdo = connectDB($db);
                    $sql = "SELECT * FROM room WHERE user_ID = :REST_ID;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                    ?>
                        <input id="<?php echo $row['ID']; ?>" class="room_name<?php echo ($row['ID'] == $_SESSION['selected_room'] ? ' active' : ''); ?>" type="submit" onclick="selectRoom(this.id)" value="<?php echo $row['Name']; ?>" />
                    <?php }
                    ?>

                </div>

                <!--Home Device Details And Room Consumption Section-->
                <div class="detail">
                    <div class="control">
                        <?php include './detail.php'; ?>
                    </div>
                    <div class="consumption">
                        <?php include './consumption.php'; ?>
                    </div>
                </div>

                <!--Home Devices Section-->
                <div class="bottom">
                    <?php
                    if ($_SESSION['selected_device'] != '') {
                    ?>
                        <div class="text">Device</div>
                    <?php
                    }
                    ?>
                    <div id="devices" class="devices">
                        <?php include './device.php'; ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </main>

        <!--Home Weather Section-->
        <div class="weather">
            <div id="today-weather-icon" class="today-weather-icon"></div>
            <div class="info">
                <div class="date-time">
                    <div class="date" id="date">Mon, 05 Jan 1970</div>
                    <div class="time" id="time">00:00</div>
                </div>
                <div id="forecast" class="forecast"></div>
                <div class="inside-house">
                    <div class="title">Current Indoor</div>
                    <div class="values">

                        <?php
                        $pdo = connectDB($db);
                        $sql = "SELECT ROUND(actions.Temperature, 0) as Temperature, actions.Humidity
                                FROM actions
                                INNER JOIN device_has_actions ON device_has_actions.actions_ID = actions.ID
                                INNER JOIN device ON device.device_has_actions_ID = device_has_actions.ID
                                INNER JOIN room ON device.room_ID = room.ID
                                WHERE actions.Temperature IS NOT NULL
                                AND actions.Humidity IS NOT NULL
                                AND room.user_ID = :REST_ID LIMIT 1;";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch();
                        if ($stmt->rowCount() > 0) {
                        ?>
                            <div class="temperature">
                                <span class="text">Temperature</span>
                                <span class="value"><?= $row['Temperature'] ?>&#176;C</span>
                            </div>
                            <div class="humidity">
                                <span class="text">Humidity</span>
                                <span class="value"><?= $row['Humidity'] ?>%</span>
                            </div>
                        <?php
                        } else {
                        ?>
                            <span class="no-values">No thermostat detected</span>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../js/datetime.js"></script>

        <!--Form Script-->
        <script>
            var select = document.getElementById('multiple-device');
            select.size = select.length;

            function openForm() {
                document.getElementById("myForm").style.display = "block";
                setTimeout(function() {
                    document.getElementById("myForm").style.opacity = 1;
                }, 100);
                $("#toggle-add-device").addClass("active");
                $("#edit").hide().attr("formnovalidate");
                $("#move").hide().attr("formnovalidate");
                $("#delete").hide().attr("formnovalidate");
                $("#add_room").hide().attr("formnovalidate");
                $("#edit_room").hide().attr("formnovalidate");
                $("#delete_room").hide().attr("formnovalidate");
                $("#add").show();
            }

            function closeForm() {
                document.getElementById("myForm").style.opacity = 0;
                setTimeout(function() {
                    document.getElementById("myForm").style.display = "none";
                }, 900);
            }

            $("#toggle-add-device").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-add-device").addClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    
                    $("#add_room").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#add").fadeIn(300);
            });

            $("#toggle-edit-device").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-edit-device").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    
                    $("#add_room").hide().attr("formnovalidate");
                    $("#add").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#edit").fadeIn(300);
            });

            $("#toggle-move-device").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-move-device").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    
                    $("#add_room").hide().attr("formnovalidate");
                    $("#add").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#move").fadeIn(300);
            });

            $("#toggle-remove-device").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-remove-device").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    
                    $("#add_room").hide().attr("formnovalidate");
                    $("#add").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#delete").fadeIn(300);
            });

            $("#toggle-add-room").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-add-room").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");

                    $("#add").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#add_room").fadeIn(300);
            });

            $("#toggle-edit-room").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-edit-room").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    $("#toggle-remove-room").removeClass("active");

                    $("#add").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#add_room").hide().attr("formnovalidate");
                    $("#delete_room").hide().attr("formnovalidate");
                });
                $("#edit_room").fadeIn(300);
            });

            $("#toggle-remove-room").click(function() {
                $('.form').fadeOut(300, function() {
                    $("#toggle-remove-room").addClass("active");
                    $("#toggle-add-device").removeClass("active");
                    $("#toggle-edit-device").removeClass("active");
                    $("#toggle-move-device").removeClass("active");
                    $("#toggle-remove-device").removeClass("active");
                    $("#toggle-edit-room").removeClass("active");
                    $("#toggle-add-room").removeClass("active");
                    
                    $("#add_room").hide().attr("formnovalidate");
                    $("#add").hide().attr("formnovalidate");
                    $("#edit").hide().attr("formnovalidate");
                    $("#move").hide().attr("formnovalidate");
                    $("#delete").hide().attr("formnovalidate");
                    $("#edit_room").hide().attr("formnovalidate");
                });
                $("#delete_room").fadeIn(300);
            });
        </script>

        <script>
            function selectRoom(clicked_id) {

                const room_name = document.getElementById(clicked_id);

                if (!room_name.classList.contains('active')) {
                    const allRooms = document.getElementsByClassName("room_name");
                    for (var i = 0; i < allRooms.length; i++) {
                        allRooms[i].classList.remove('active');
                    }
                    room_name.classList.add('active');
                }
            }

            function selectDevice(clicked_id) {

                const device_name = document.getElementById(clicked_id);

                if (!device_name.classList.contains('active')) {
                    const allDevices = document.getElementsByClassName("item");
                    for (var i = 0; i < allDevices.length; i++) {
                        allDevices[i].classList.remove('active');
                    }
                    device_name.classList.add('active');
                }
            }
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.room_name').click(function() {
                    $.ajax({
                        url: '../update.php',
                        type: 'GET',
                        data: {
                            room_ID: this.id
                        },
                        success: function() {
                            $('#devices').fadeOut(300, function() {
                                $('#devices').load('device.php');
                                $("#devices").fadeIn(300);
                            });
                            $('.control').fadeOut(320, function() {
                                $('.control').load('detail.php');
                                $('.control').fadeIn(300);
                            });
                            $('.consumption').fadeOut(320, function() {
                                $('.consumption').load('consumption.php');
                                $('.consumption').fadeIn(300);
                            });
                        }
                    });
                });
            });
        </script>
</body>

</html>