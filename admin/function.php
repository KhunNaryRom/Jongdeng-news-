<!-- @import jquery & sweet alert  -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
    session_start();

    //Connection to DB
    function connectionDB() {
        $con = new mysqli('','root','','jongdeng_news');
        return $con;
    }

    // Move file upload
    function uploadFile($file) {
        $fileName   = rand(1, 999).'-'.$file['name'];
        $sourceFile = $file['tmp_name'];
        $path       = 'assets/image/'. $fileName;
        move_uploaded_file($sourceFile, $path);
        return $fileName;
    }

    // get current  date 
    function currentDate() {
        $date = date('Y-m-d');
        return $date;
    }

    // execute query
    function exeQuery($sqlStr) {
        $result = connectionDB()->query($sqlStr);
        if($result) {
            return true;
        }
        else {
            return false;
        }
    }

    // Register Account
    function userRegister() {
        if(isset($_POST['btn-register'])) {
            //validation field data
            if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_FILES['profile'])) {
               $name     = $_POST['name'];
               $email    = $_POST['email'];
               $password = md5($_POST['password']);

               $file     = $_FILES['profile'];
               $fileName = uploadFile($file); 

               // insert to table user
               $sqlStr = "
                INSERT INTO `user`(`name`, `email`, `password`, `profile`, `is_deleted`, `created_at`, `updated_at`) 
                VALUES ('".$name."', '".$email."', '".$password."', '".$fileName."', 0, '".currentDate()."', '".currentDate()."')
               ";
               $result = exeQuery($sqlStr);
               if($result) {
                  header('Location: login.php');
               }
            }
            else {
                return 'Please input all information!';
            }
        }
    }

    // user login account
    function userLogin() {
        if(isset($_POST['btn-login'])) {
            //validation field data
            if(!empty($_POST['name_email']) && !empty($_POST['password'])) {
                $nameEmail = $_POST['name_email'];
                $password  = md5($_POST['password']);
                $sqlStr = "
                    SELECT * FROM `user` WHERE (name = '".$nameEmail."' OR email = '".$nameEmail."') 
                    AND password = '".$password."'
                ";
                $result = connectionDB()->query($sqlStr);
                if($result->num_rows > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $userId = $row['id'];
                        $_SESSION['uid'] = $userId;
                        header('Location: index.php');
                    }
                }
                else {
                    return 'Invalid Account!';
                }
            }
            else {
                return 'Please input all information!';
            }
        }
    }

    // user logout
    function userlogout() {
        if(isset($_POST['btn-logout'])) {
            unset($_SESSION['uid']);
            header('Location: login.php');
        }
    }
    userlogout();

    // get current profile
    function getCurrentProfile() {
        $uID = $_SESSION['uid'];
        $sqlStr = "SELECT * FROM `user` WHERE id = $uID";
        $result = connectionDB()->query($sqlStr);
        $row    = mysqli_fetch_assoc($result);
        return $row;
    }

    // Activity log
    function activityLog($postType, $action) {
        $uID = $_SESSION['uid'];

        if($postType == 'logo') {
            $tableName = 'website_logo';
        }
        else if($postType == 'category') {
            $tableName = 'category';
        }
        else if($postType == 'news') {
            $tableName = 'news';
        }

        $sqlSelect = "SELECT id FROM ".$tableName." ORDER BY id DESC LIMIT 1";
        $rs        = connectionDB()->query($sqlSelect);
        $row       = mysqli_fetch_assoc($rs);
        $postID    = $row['id']; 

        $sqlStr = "
            INSERT INTO `activity_log`(`post_type`, `post_id`, `author_id`, `action`, `created_at`) 
            VALUES ('". $postType ."', $postID, $uID, '". $action ."', '". currentDate() ."');
        ";
        exeQuery($sqlStr);
    }
    // Get current post
    function getCurrentPost($postID, $tableName) {
        $sqlStr = "SELECT * FROM ". $tableName ." WHERE id = $postID";
        $rs     = connectionDB()->query($sqlStr);
        $row    = mysqli_fetch_assoc($rs);
        return $row;
    }

    // Insert logo
    function addlogo() {    
        if(isset($_POST['btn-insert-logo'])) {
            if(!empty($_FILES['thumbnail']['name'])) {
                if(!empty($_POST['pined'])) {
                    $pined = 1;
                    // update pined to DB
                    $sqlPined = "UPDATE `website_logo` SET `pined`= 0 WHERE pined = 1";
                    exeQuery($sqlPined);
                }
                else {
                    $pined = 0;
                }
                $file     = $_FILES['thumbnail'];
                $fileName = uploadFile($file);
    
                $uID = $_SESSION['uid'];
    
                $sqlStr = "
                    INSERT INTO `website_logo`(`thumbnail`, `pined`, `author_id`, `is_deleted`, `created_at`, `updated_at`) 
                    VALUES ('".$fileName."', '".$pined."', ".$uID.", 1, '".currentDate()."', '".currentDate()."')
                ";
                $result = exeQuery($sqlStr);
                if($result) {
                    activityLog('logo', 'insert');
                    return 'Post insert successfully';
                }
                else {
                    return 'Internal server error!';
                }
                
            }
            else {
                return 'Please input information!';
            }
        }
    }

    // Get list logo
    function listLogo() {
        $sqlStr = "
            SELECT website_logo.*, user.name AS u_name 
            FROM website_logo INNER JOIN user ON website_logo.author_id =  user.id 
            WHERE website_logo.is_deleted = 1 ORDER BY website_logo.id DESC 
        ";
        $result = connectionDB()->query($sqlStr);
        while($row = mysqli_fetch_assoc($result)) {
            $pined = $row['pined'];
            if($pined == 1) {
                $strPined = 'Pined';
            }
            else {
                $strPined = 'Unpined';
            }
            echo '
                <tr>
                    <td>'. $row['id'] .'</td>
                    <td><img src="assets/image/'. $row['thumbnail'] .'"></td>
                    <td>'. $strPined .'</td>
                    <td>'. $row['u_name'] .'</td>
                    <td>'. $row['created_at'] .'</td>
                    <td width="150px">
                        <a href="update-logo.php?id='.$row['id'].'" class="btn btn-primary">Update</a>
                        <button type="button" remove-id="'. $row['id'] .'" class="btn btn-danger btn-remove" data-bs-toggle="modal" data-bs-target="#removeLogo">
                            Remove
                        </button>
                    </td>
                </tr>
            ';
        }
    }

    // update logo 
    function updateLogo() {
        if(isset($_POST['btn-update-logo'])) {
            $postID = $_POST['postID'];

            if(!empty($_POST['pined'])) {
                $pined = 1;
                // update pined to DB
                $sqlPined = "UPDATE `website_logo` SET `pined`= 0 WHERE pined = 1";
                exeQuery($sqlPined);
            }
            else {
                $pined = 0;
            }

            if($_FILES['thumbnail']['name'] != '') {
                $file     = $_FILES['thumbnail'];
                $fileName = uploadFile($file);
            }
            else {
                $fileName = $_POST['oldThumbnail'];
            }

            $sqlStr = "
                UPDATE `website_logo` SET `thumbnail`='". $fileName ."',`pined`= $pined, `updated_at`='". currentDate() ."'
                WHERE id = $postID
            ";
            $rs = exeQuery($sqlStr);
            if($rs) {
                header('Location: list-logo.php');
            }
        }
    }
    updateLogo();

    // remove logo
    function removeLogo() {
        if(isset($_POST['btn-remove-logo'])) {
            $postID = $_POST['postID'];
            $sqlStr = "UPDATE `website_logo` SET `is_deleted`= 0 WHERE id = $postID";
            $rs     = exeQuery($sqlStr);
            if($rs) {
                header('Location: list-logo.php');
            }
        }
    }
    removeLogo();

    // Get list logs
    function listLogs() {
        $sqlStr = "
            SELECT activity_log.*, user.name 
            FROM activity_log INNER JOIN user 
            ON activity_log.author_id = user.id
            ORDER BY activity_log.id DESC
        ";
        $result = connectionDB()->query($sqlStr);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
                <tr>
                    <td>'. $row['id'] .'</td>
                    <td>'. $row['post_type'] .'</td>
                    <td>'. $row['post_id'] .'</td>
                    <td>'. $row['name'] .'</td>
                    <td>'. $row['action'] .'</td>
                    <td>'. $row['created_at'] .'</td>
                </tr>
            ';
        }
        
    }

    // Add Category
    function addCategory() {
        if(isset($_POST['btn-add-category'])) {
            if($_POST['title'] != '') {

                $name     = $_POST['title'];
                $authorID = $_SESSION['uid'];

                $sqlStr = "
                    INSERT INTO `category`(`name`, `author_id`, `is_deleted`, `created_at`, `updated_at`) 
                    VALUES ('". $name ."', ". $authorID .", 1, '". currentDate() ."', '". currentDate() ."' )
                ";
                $result = exeQuery($sqlStr);
                if($result) {
                    activityLog('category', 'insert');
                    return 'Post insert successfully';
                }
            }
            else {
                return 'Please input information!';
            }
        }
    }

    // List Category
    function listCategory() {
        $sqlStr = "
            SELECT category.*, user.name AS author_name  
            FROM category INNER JOIN user ON category.author_id = user.id 
            WHERE category.is_deleted = 1
            ORDER BY category.id DESC 
        ";
        $result = connectionDB()->query($sqlStr);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
                <tr>
                    <td>'. $row['id'] .'</td>
                    <td>'. $row['name'] .'</td>
                    <td>'. $row['author_name'] .'</td>
                    <td>'. $row['created_at'] .'</td>
                    <td width="150px">
                        <a href="update-category.php?id='.$row['id'].'" class="btn btn-primary">Update</a>
                        <button type="button" remove-id="'. $row['id'] .'" class="btn btn-danger btn-remove" data-bs-toggle="modal" data-bs-target="#removeLogo">
                            Remove
                        </button>
                    </td>
                </tr>
            ';
        }
    }

function updateCategory() {
    if (isset($_POST['btn-update-category'])) {
        $postID = $_POST['postID'];
        $name = $_POST['title'];  // We're using 'title' from the form

        // Validate and sanitize input
        if (!empty($name)) {
            // Use prepared statement to prevent SQL injection
            $conn = connectionDB();  // Assuming connectionDB() is your database connection function

            // Prepare the SQL query
            $stmt = $conn->prepare("UPDATE `category` SET `name` = ?, `updated_at` = ? WHERE `id` = ?");
            $updatedAt = currentDate();  // Assuming currentDate() returns the current timestamp
            $stmt->bind_param("ssi", $name, $updatedAt, $postID); // Bind parameters
            
            // Execute the query
            if ($stmt->execute()) {
                echo 'Category updated successfully!';
            } else {
                echo 'Error updating category: ' . $stmt->error;
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
        } else {
            echo 'Please provide a name!';
        }
    }
}

    updateCategory();
    
    function getAllCategory() {
        $sqlStr = "
            SELECT * FROM category WHERE is_deleted = 1 ORDER BY id DESC 
        ";
        $result = connectionDB()->query($sqlStr);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
                <option value="'. $row['id'] .'">'. $row['name'] .'</option>
            ';
        }
    }

    // Add News
function addNews() {
    if (isset($_POST['btn-add-news'])) {
        if (!empty($_POST['title']) && !empty($_POST['description'])) {

            $conn = connectionDB();

            $title       = $_POST['title'];
            $categoryID  = $_POST['category'];
            $file        = $_FILES['thumbnail'];
            $thumbnail   = uploadFile($file);
            $description = $_POST['description'];
            $viewer      = 0;
            $authorID    = $_SESSION['uid'];
            $createdAt   = currentDate();
            $updatedAt   = currentDate();

            $pined = 0;
            if (!empty($_POST['pined'])) {
                $pined = 1;

                // Unpin all previous posts
                $conn->query("UPDATE `news` SET `pined` = 0 WHERE `pined` = 1");
            }

            $stmt = $conn->prepare("
                INSERT INTO `news` 
                    (`title`, `pined`, `thumbnail`, `viewer`, `description`, `author_id`, `category_id`, `is_deleted`, `created_at`, `updated_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)
            ");

            $stmt->bind_param(
                "sisisiiss",
                $title,
                $pined,
                $thumbnail,
                $viewer,
                $description,
                $authorID,
                $categoryID,
                $createdAt,
                $updatedAt
            );

            if ($stmt->execute()) {
                activityLog('news', 'insert');
                return 'Post inserted successfully';
            } else {
                return 'Error: ' . $stmt->error;
            }

            $stmt->close();
            $conn->close();
        } else {
            return 'Please input information!';
        }
    }
}


function listNews() {
    $sqlStr = "
        SELECT news.*, user.name, category.name AS cate_name 
        FROM `news` 
        INNER JOIN user ON news.author_id = user.id 
        INNER JOIN category ON news.category_id = category.id
        WHERE news.is_deleted = 1
        ORDER BY news.id DESC
    ";

    $result = connectionDB()->query($sqlStr);

    while ($row = mysqli_fetch_assoc($result)) {
        $id = htmlspecialchars($row['id']);
        $title = htmlspecialchars($row['title']);
        $pined = $row['pined'] ? '✔' : '';
        $thumbnail = htmlspecialchars($row['thumbnail']);
        $viewer = htmlspecialchars($row['viewer']);
        $author = htmlspecialchars($row['name']);
        $category = htmlspecialchars($row['cate_name']);
        $createdAt = htmlspecialchars($row['created_at']);

        echo '
            <tr>
                <td>' . $id . '</td>
                <td>' . $title . '</td>
                <td>' . $pined . '</td>
                <td><img src="assets/image/' . $thumbnail . '" class="img-thumbnail" width="80"></td>
                <td>' . $viewer . '</td>
                <td>' . $author . '</td>
                <td>' . $category . '</td>
                <td>' . $createdAt . '</td>
                <td width="150px">
                    <a href="update-news.php?id=' . $id . '" class="btn btn-primary btn-sm">Update</a>
                    <button type="button" remove-id="' . $id . '" class="btn btn-danger btn-sm btn-remove" data-bs-toggle="modal" data-bs-target="#removeLogo">
                        Remove
                    </button>
                </td>
            </tr>
        ';
    }
}

    
function updateNews(){
    global $conn; // Assuming you have a global DB connection
    
    if (isset($_POST['btn-update-news'])) {
        $postID = $_POST['postID'];
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $categoryID = $_POST['category_id'];  // Get category ID from the form
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        // Check for pined status
        $pined = !empty($_POST['pined']) ? 1 : 0;

        // If thumbnail is updated
        if ($_FILES['thumbnail']['name'] != '') {
            $file = $_FILES['thumbnail'];
            $fileName = uploadFile($file);  // Assuming uploadFile is a function to upload the image
        } else {
            $fileName = $_POST['oldThumbnail'];  // If not updated, use the old thumbnail
        }

        // SQL to update news
        $sqlStr = "
            UPDATE `news` SET
            `title` = '$title',
            `pined` = $pined,
            `thumbnail` = '$fileName',
            `description` = '$description',
            `category_id` = '$categoryID',
            `updated_at` = '". currentDate() ."'
            WHERE `id` = $postID
        ";

        // Execute the query
        $rs = exeQuery($sqlStr);
        
        if ($rs) {
            header('Location: list-news.php');  // Redirect to the news list page after successful update
        } else {
            echo 'Error updating the news post!';
        }
    }
}

    updateNews();
    function removeNews() {
        if(isset($_POST['btn-remove-news'])) {
            $postID = $_POST['postID'];
            echo $postID;
            $sqlStr = "UPDATE `news` SET `is_deleted`= 0 WHERE id = $postID";
            $rs     = exeQuery($sqlStr);
            if($rs) {
                header('Location: list-news.php');
            }
        }
    }
    removeNews();


