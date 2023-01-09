<?php
if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';



if ($_SESSION['selected_device'] == '') {
?>
    <div class="start-div">
        <span class="start-btn" onclick="openForm()"><img src="../../images/icons/dashboard/startadding.png" alt="">Add a device</span>
    </div>
<?php
} else {

    $pdo = connectDB($db);

    $sql = "SELECT device.ID, device.Name, device.State, device.Icon, actions.Output, actions.Input
    FROM ((device
    INNER JOIN device_has_actions ON device.device_has_actions_ID = device_has_actions.ID)
    INNER JOIN actions ON device_has_actions.actions_ID = actions.ID)
    WHERE device.ID = :DEVICE_ID";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":DEVICE_ID", $_SESSION['selected_device'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
?>
    <div class="top">
        <div class="name">
            <?php
            $svg_file = file_get_contents(ICON_PATH . $row['Icon']);
            echo $svg_file;
            ?>
            <p><?php echo $row['Name']; ?></p>
        </div>
        <label class="switch">
            <input type="checkbox" class="on_off_btn_detail" id="<?php echo $row['ID'] ?>" name="<?php echo $row['ID'] ?>" <?php echo ($row['State'] == 'on' ? 'checked' : ''); ?>>
            <span class="switch-slider round"></span>
        </label>
    </div>

    <div class="slider">
        <div class="minus">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <g>
                    <path d="M480,288H32c-17.673,0-32-14.327-32-32s14.327-32,32-32h448c17.673,0,32,14.327,32,32S497.673,288,480,288z" />
                </g>
            </svg>
        </div>

        <?php if ($row['Icon'] == 'bulb.svg') {
        ?>
            <div class="range" id="<?php echo $row['ID'] ?>">
                <input type="range" name="points" min="0" max="850" class="count">
                <div class="slice left">
                    <div class="blocker"></div>
                </div>
                <div class="slice right">
                    <div class="blocker"></div>
                </div>
                <span class="info">
                    <span class="info-inner">
                        <span class="count">0</span><span class="percent">%</span>
                        <span class="text">Brightness</span>
                    </span>
                </span>
                <div class="dial" tabindex="0"></div>
            </div>
        <?php
        } else {
        ?>
            <div class="range" id="<?php echo $row['ID'] ?>">
                <input type="range" name="points" min="100" max="264" class="count">
                <div class="slice left">
                    <div class="blocker"></div>
                </div>
                <div class="slice right">
                    <div class="blocker"></div>
                </div>
                <span class="info">
                    <span class="info-inner">
                        <span class="count">0</span><span class="percent">&#176;C</span>
                        <span class="text">Celsius</span>
                    </span>
                </span>
                <div class="dial" tabindex="0"></div>
            </div>
        <?php
        }
        ?>
        <div class="plus">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <g>
                    <path d="M480,224H288V32c0-17.673-14.327-32-32-32s-32,14.327-32,32v192H32c-17.673,0-32,14.327-32,32s14.327,32,32,32h192v192   c0,17.673,14.327,32,32,32s32-14.327,32-32V288h192c17.673,0,32-14.327,32-32S497.673,224,480,224z" />
                </g>
            </svg>
        </div>
    </div>
    <script type="text/javascript" defer src="../../js/roundslider.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.on_off_btn_detail').click(function() {
                var state = $(this).is(':checked') ? 'on' : 'off';
                $.ajax({
                    url: '../update.php',
                    type: 'GET',
                    data: {
                        state: state,
                        device_ID: this.id
                    },
                });
            });
        });

        $(".range").find(".count").text(<?php echo $row['Input'] ?>).val(<?php echo $row['Input'] ?>);
    </script>

<?php
}
?>