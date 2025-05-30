<?php 
    include('header.php'); 
    
    if (isset($_GET['id'])) {
        $article = getArticleById($_GET['id']); // Get + count view

        if ($article) {
?>
<section class="article">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <figure>
                    <div class="thumbnail">
                        <img src="admin/assets/image/<?php echo $article['thumbnail']; ?>" alt="">
                    </div>
                </figure>
                <figcaption>
                    <h3><?php echo $article['title']; ?></h3>
                    <div class="date">
                        <img src="assets/icons/date.svg" alt="">
                        <h6><?php echo date('d-M-Y', strtotime($article['created_at'])); ?></h6>
                    </div>
                    <div class="date">
                        <img src="assets/icons/eye.svg" alt="">
                        <h6><?php echo $article['viewer']; ?></h6>
                    </div>
                    <div class="line"></div>
                    <div class="description">
                        <?php echo nl2br($article['description']); ?>
                    </div>
                </figcaption>
            </div>
        </div>
    </div>
</section>

<!-- Optional: Related News section (still static) -->
<section class="news">
    <div class="container">
        <div class="top">
            <h2>Related News</h2>
        </div>
        <div class="row">
            <div class="col-4">
                <figure>
                    <div class="thumbnail">
                        <a href="">
                            <img src="https://placehold.co/300" alt="">
                        </a>
                    </div>
                </figure>
                <figcaption>
                    <h3>
                        <a href="">1990 World Cup Finals 3rd Shirt</a>
                    </h3>
                    <div>
                        <img src="assets/icons/date.svg" alt="">
                        <span>19 Jun, 2023</span>
                    </div>
                </figcaption>
            </div>
        </div>
    </div>
</section>

<?php 
        } else {
            echo "<div class='container'><p>Article not found.</p></div>";
        }
    } else {
        echo "<div class='container'><p>No article ID provided.</p></div>";
    }

    include('footer.php'); 
?>
