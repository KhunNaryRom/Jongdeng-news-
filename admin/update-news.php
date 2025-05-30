<?php 
    include('sidebar.php');
    $postID = $_GET['id'];
    $currentPost = getCurrentPost($postID, 'news');

    $isCheck = '';
    if ($currentPost['pined'] == 1) {
        $isCheck = 'checked';
    }

    $title = isset($currentPost['title']) ? $currentPost['title'] : '';
    $description = isset($currentPost['description']) ? $currentPost['description'] : '';
    $categoryID = isset($currentPost['category_id']) ? $currentPost['category_id'] : '';
?>
<div class="col-10">
    <div class="content-right">
        <div class="top">
            <h3>Update News</h3>
        </div>
        <div class="bottom">
            <figure>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="postID" value="<?php echo $postID ?>">
                    
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $title ?>"> <br>
                    
                    <input type="hidden" name="oldThumbnail" value="<?php echo $currentPost['thumbnail'] ?>">

                    <div class="form-group">
                        <label>Pined</label>
                        <input type="checkbox" name="pined" value="1" <?php echo $isCheck; ?> class="form-check-input">
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select name="category_id" class="form-control">
                            <!-- Populate the categories here -->
                            <?php 
                                // Assuming getCategories() is a function that retrieves all categories from the DB
                                $categories = getAllCategory(); 
                                foreach ($categories as $category) {
                                    $selected = ($category['id'] == $categoryID) ? 'selected' : '';
                                    echo "<option value='{$category['id']}' {$selected}>{$category['name']}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thumbnail</label> <br>
                        <img src="assets/image/<?php echo $currentPost['thumbnail']; ?>" class="img-update">
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="5" cols="50"><?php echo $description; ?></textarea> <br>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="btn-update-news">Save</button>
                    </div>
                </form>
            </figure>
        </div>
    </div>
</div>
