<fieldset>
	<legend class="dcf-legend dcf-txt-md">Image</legend>
	<div class="details">
		<?php if ($context->event->imagemime != NULL): ?>
            <div class="dcf-mb-4">
                <img src="<?php echo $base_frontend_url ?>images/<?php echo $context->event->id; ?>" alt="image for event <?php echo $context->event->id; ?>">
                <?php if(!$context->on_main_calendar): ?>
                    <br>
                    <input type="checkbox" name="remove_image" id="remove-image">
                    <label for="remove-image" class="dcf-txt-sm">Remove Image</label>
                <?php endif; ?>
            </div>
		<?php endif; ?>
        <label class="dcf-label" for="imagedata"><span class="required-for-main-calendar dcf-required" style="display: none">* </span>Image Upload</label>
		<input class="dcf-mt-4 dcf-input-file" style="font-size: 10px;" type="file" name="imagedata" id="imagedata" accept="image/gif, image/jpeg, image/png, image/avif, image/webp" aria-label="Select an Image">
        <div class="dcf-mt-4 dcf-form-help">
            <p>Do not include text on image. Any text should be included in title and description of event. Image should be descriptive of event.</p>
        </div>
        <?php
            $croppedImageHiddenAttr = 'hidden';
            $croppedImageData = '';
            $croppedImageSrc = 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
            if (!empty($context->post['cropped_image_data'])) {
	            $croppedImageHiddenAttr = '';
	            $croppedImageData = $context->post['cropped_image_data'];
	            $croppedImageSrc = $context->post['cropped_image_data'];
            }
        ?>
        <div id="cropped-image-container" class="dcf-mt-4 dcf-txt-xs"  <?php echo $croppedImageHiddenAttr; ?>>
            <fieldset>
                <legend class="dcf-legend dcf-txt-md">Pending Image</legend>
                <input type="hidden" id="cropped-image-data" name="cropped_image_data" <?php echo $croppedImageData ?>>
                <img id="cropped-image" src="<?php echo $croppedImageSrc; ?>" alt="Preview of pending cropped image upload">
            </fieldset>
        </div>
	</div>
</fieldset>
<div class="dcf-modal" id="image-modal" hidden>
	<div class="dcf-modal-wrapper">
		<div class="dcf-modal-header">
			<h3>Image Crop</h3>
			<button class="dcf-btn-close-modal">Close</button>
		</div>
		<div class="dcf-modal-content">
            <ul class="dcf-mb-4" id="cropper-errors" hidden></ul>
            <div id="cropper-content">
                <div class="dcf-mb-4">
                    <p class="dcf-txt-sm">To crop your image, position and size the blue square to the area to be cropped and press the <strong>Crop</strong> button.</p>
                </div>
                <div class="img-container">
                    <div class="dcf-grid dcf-col-gap-vw dcf-row-gap-4">
                        <div class="dcf-col-100% dcf-col-75%-start@md">
                            <img class="source-image" id="source-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Source image to crop.">
                        </div>
                        <div class="dcf-col-100% dcf-col-25%-end@md">
                            <div class="preview-image"></div>
                        </div>
                    </div>
                </div>
                <div class="dcf-mt-4">
                    <button type="button" class="dcf-btn dcf-btn-secondary" id="cancel-crop-btn">Cancel</button>
                    <button type="button" class="dcf-btn dcf-btn-primary" id="crop-btn">Crop</button>
                </div>
            </div>
		</div>
	</div>
</div>
<?php
$page->addScript($base_frontend_url .'templates/default/html/js/event-image.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);
?>
