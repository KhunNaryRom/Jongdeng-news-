<?php include('header.php') ?>
<section class="trending">
    <div class="container">
        <?php echo getPinedNews() ?>
    </div>
</section>

<section class="news">
    <div class="container">
        <div class="top">
            <h2>Latest News</h2>
        </div>
        <div class="row">
            <?php echo getLatestNews() ?>
        </div>
    </div>
</section>

<section class="news">
    <div class="container">
        <div class="top">
            <h2>Popular News</h2>
        </div>
        <div class="row">
            <?php echo getPopularnews()?>
        </div>
    </div>
</section>
<?php include('footer.php') ?>