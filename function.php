<?php
    function connectionDB() {
        $con = new mysqli('','root','','jongdeng_news');
        return $con;
    }

    // Get Logo
    function getlogo() {
        $sqlStr = "SELECT * FROM `website_logo` WHERE `pined` = 1 AND is_deleted = 1";
        $result = connectionDB()->query($sqlStr);
        $row    = mysqli_fetch_assoc($result);
        return $row['thumbnail'];
    }

    // Get All News
    function getAllNews($onlyActive = true) {
        $con = connectionDB();

        // Build SQL query
        $sql = "SELECT * FROM news WHERE is_deleted = 1 ORDER BY id DESC";

        // Execute query
        $result = $con->query($sql);

        // Collect all rows
        $newsList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $newsList[] = $row;
        }

        return $newsList;
    }

    // Get Pined News
    function getPinedNews() {
        $sqlStr = "SELECT * FROM `news` WHERE `pined` = 1 AND is_deleted = 1";
        $result = connectionDB()->query($sqlStr);
        $row    = mysqli_fetch_assoc($result);
        echo '
            <div class="row">
                <div class="col-6">
                    <figcaption>
                        <h2>'. $row['title'] .'</h2>
                        <a href="article.php?id='. $row['id'] .'">FIND OUT MORE</a>
                    </figcaption>
                </div>
                <div class="col-6">
                    <div class="thumbnail">
                        <img src="admin/assets/image/'.$row['thumbnail'].'" class="pined-news">
                    </div>
                </div>
            </div>
        ';
    }

    function getArticleById($id) {
    $con = connectionDB();

    // Sanitize and convert ID to integer
    $id = intval($id);

    // Increment view count
    $con->query("UPDATE news SET viewer = viewer + 1 WHERE id = $id AND is_deleted = 1");

    // Fetch the article
    $sql = "SELECT * FROM news WHERE id = $id AND is_deleted = 1";
    $result = $con->query($sql);

    // Return article data or null
    return ($result && $result->num_rows > 0) ? mysqli_fetch_assoc($result) : null;
}
    // Get Latest News 
    function getLatestNews() {
        $sqlStr = "SELECT * FROM `news` WHERE is_deleted = 1 ORDER BY id DESC LIMIT 3";
        $result = connectionDB()->query($sqlStr);
        while($row = mysqli_fetch_assoc($result)) {
            $date       = date_create($row['created_at']);
            $dateFormat = date_format($date, 'd M,Y');
            echo '
                <div class="col-4 " style="height: 400px;">
                    <figure>
                        <div class="thumbnail" style="height: 200px;">
                            <a href="article.php?id='. $row['id'] .'">
                                <img src="admin/assets/image/'.$row['thumbnail'].'" alt="">
                            </a>
                        </div>
                    </figure>
                    <figcaption style="height: 200px;">
                        <h3>
                            <a href="">'. $row['title'] .'</a>
                        </h3>
                        <div>
                            <img src="assets/icons/date.svg" alt="">
                            <span>'. $dateFormat .'</span>
                        </div>
                    </figcaption>
                </div>
            ';
        }
    }

function getPopularnews() {
    $sqlStr = "SELECT * FROM `news` WHERE is_deleted = 1 ORDER BY viewer DESC LIMIT 3";
    $result = connectionDB()->query($sqlStr);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $date       = date_create($row['created_at']);
        $dateFormat = date_format($date, 'd M,Y');

        echo '
            <div class="col-4 " style="height: 400px;">
                <figure>
                    <div class="thumbnail" style="height: 200px;">
                        <a href="article.php?id='. $row['id'] .'">
                            <img src="admin/assets/image/'.$row['thumbnail'].'" alt="">
                        </a>
                    </div>
                </figure>
                <figcaption style="height: 200px;">
                    <h3>
                        <a href="">'. $row['title'] .'</a>
                    </h3>
                    <div>
                            <img src="assets/icons/date.svg" alt="">
                        <span>'. $dateFormat .'</span>
                    </div>
                </figcaption>
            </div>
        ';
    }
}
