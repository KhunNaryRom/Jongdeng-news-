<?php include('header.php'); ?>

<section class="news">
    <div class="container">
        <div class="top">
            <h2>News</h2>
            <form action="" method="post">
                <select name="filter" class="form-control">
                    <option selected disabled>Filter News</option>
                    <option value="">Sports</option>
                    <option value="">Technology</option>
                    <option value="">Entertainment</option>
                </select>
            </form>
        </div>

        <div class="row">
            <?php
                $newsList = getAllNews(); // Get all active news
                if (count($newsList) > 0) {
                    foreach ($newsList as $news) {
                        $date = date('d M, Y', strtotime($news['created_at']));
                        echo '
                            <div class="col-4" style="height: 450px">
                                <figure>
                                    <div class="thumbnail" style="height: 250px">
                                        <a href="article.php?id='. $news['id'] .'">
                                            <img src="admin/assets/image/'.$news['thumbnail'].'" alt="">
                                        </a>
                                    </div>
                                </figure>
                                <figcaption style="height: 200px">
                                    <h3>
                                        <a href="article.php?id='. $news['id'] .'">'. $news['title'] .'</a>
                                    </h3>
                                    <div>
                                        <img src="assets/icons/date.svg" alt="">
                                        <span>'. $date .'</span>
                                    </div>
                                </figcaption>
                            </div>
                        ';
                    }
                } else {
                    echo "<p>No news articles found.</p>";
                }
            ?>
        </div>

        <!-- Optional: Pagination UI (static for now) -->
        <div class="row">
            <div class="col-12">
                <ul class="pagination">
                    <li class="active"><a href="">1</a></li>
                    <li><a href="">2</a></li>
                    <li><a href="">3</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
