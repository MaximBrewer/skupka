<?php
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument ();
?>
<div class="vmzoomer-wrap">
	<?php
	if (!empty($this->product->images)) {
		$image = $this->product->images[0];
		?>
		<div class="vmzoomer-image-wrap">
            <div class="vmzoomer-image">
                <?php echo  $image->displayMediaFull("",true,"rel='vm-additional-images' class='product-zoom-image'",false); ?>    
            </div>
            <div class="lightbox-button"></div>
            <?php
            if(!empty($this->product->images[1])) { ?>
                <div class="next-button"></div>
                <div class="prev-button"></div>	
            <?php }?>
        </div>
        <?php if(!empty($this->product->images[1])) { ?>
        <div class="vmzoomer-additional-images">
            <?php
            $start_image = 0;
            for ($i = $start_image; $i < count($this->product->images); $i++) {
                $image = $this->product->images[$i];
                ?>
                <div class="additional-image-wrap">
                    <div class="item"><?php
                        echo $image->displayMediaThumb('class="product-image" style="cursor: pointer"',false,$image->file_description);
                        echo '<a href="'. $image->file_url .'"  class="product-image fresco image-'. $i .'" style="display:none;" title="'. $image->file_meta .'" rel="vm-additional-images" data-fresco-group="full-image" data-fresco-group-options=""></a>';
                            ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php } ?>
    <?php } ?>
</div>