<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/settings.css">
</head>

<body>
    <div class="container-settings">
        <!--Statistics-->
        <main>
            <div class="top">
                <div class="text">
                    <h1 id="greeting">Dashboard</h1>
                    <h2>Settings</h2>
                </div>
            </div>
            <div class="section">
                <div class="profile">
                    <span class="container-profile-name">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                            <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z" />
                            <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z" />
                        </svg>
                        Profile Settings</span>
                    <div class="container">
                        <div class="container-picture">
                            <h1>Picture</h1>
                            <div class="content-picture">
                                <img src="<?= AVATAR_WEB_PATH . ($_SESSION['avatar'] != null ? $_SESSION['avatar'] : AVATAR_DEFAULT) ?>">
                                <form method="POST" enctype="multipart/form-data" action="../upload.php">
                                    <div class="form-group">
                                        <label class="custom-file-upload">
                                            <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" />
                                            Change
                                        </label>
                                        <a href="../delete.php?delete=avatar">
                                            <label class="custom-file-upload">
                                                Remove
                                            </label>
                                        </a>
                                    </div>
                                    <input type="submit" class="btn btn-primary" name="submit" value="Upload" />
                                </form>
                            </div>
                        </div>

                        <div class="container-name">
                            <h1>Name</h1>
                            <div class="content-name">
                                <form method="POST" action="../edit.php">
                                    <input type="text" class="profile-name" id="text" value="<?= $_SESSION['name'] ?>" name="profile_name">
                                    <input type="submit" class="btn btn-primary" name="edit_name" value="Change Name" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="account">
                    <span class="container-account-name">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                            <path d="M19,0H5A5.006,5.006,0,0,0,0,5V19a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V5A5.006,5.006,0,0,0,19,0ZM7,22V21a5,5,0,0,1,10,0v1Zm15-3a3,3,0,0,1-3,3V21A7,7,0,0,0,5,21v1a3,3,0,0,1-3-3V5A3,3,0,0,1,5,2H19a3,3,0,0,1,3,3Z" />
                            <path d="M12,4a4,4,0,1,0,4,4A4,4,0,0,0,12,4Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,10Z" />
                        </svg>
                        Account Settings</span>
                    <div class="container">
                        <div class="container-username">
                            <h1>Username</h1>
                            <div class="content-username">
                                <form method="POST" action="../edit.php">
                                    <input type="text" class="account_username" id="text" value="<?= $_SESSION['username'] ?>" name="account_username">
                                    <input type="submit" class="btn btn-primary" name="edit_username" value="Change Username" />
                                </form>
                            </div>
                        </div>

                        <div class="container-email">
                            <h1>Email</h1>
                            <div class="content-email">
                                <form method="POST" action="../edit.php">
                                    <input type="email" class="account_email" id="text" value="<?= $_SESSION['email'] ?>" name="account_email">
                                    <input type="submit" class="btn btn-primary" name="edit_email" value="Change Email" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>