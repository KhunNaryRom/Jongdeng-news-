<?php 
    include('sidebar.php');
    $postID = $_GET['id'];
    $currentPost = getCurrentPost($postID, 'category');
    
    $title = isset($currentPost['name']) ? $currentPost['name'] : '';
?>
<div class="col-10">
    <div class="content-right">
        <div class="top">
            <h3>Update Category</h3>
        </div>
        <div class="bottom">
            <figure>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="postID" value="<?php echo $postID ?>">

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="btn-update-category">Save</button>
                    </div>
                </form>
            </figure>
        </div>
    </div>
</div>
</div>
</div>
</main>
</body>
</html>
