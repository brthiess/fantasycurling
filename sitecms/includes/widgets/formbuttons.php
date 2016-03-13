<footer id="cms-footer" class="resize">
	<button type="submit" name="save" value="save" class="button f_right"><i class="fa fa-check"></i>Save Changes</button>
    <?php if(ITEM_ID != "" && (!isset($row['deletable']) || $row['deletable'] == 1)){ ?>
    <button type="button" name="delete" value="delete" class="button delete"><i class="fa fa-trash"></i>Delete</button>
    <?php } ?>
    <a href="<?php echo PAGE_URL; ?>" class="cancel">Cancel</a>
</footer>