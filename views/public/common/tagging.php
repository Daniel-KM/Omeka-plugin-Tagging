<a href="#" id="display-tagging-form" class="button blue right" onclick="return false;"><?php echo $tagging_message; ?></a>
<?php echo $this->getTaggingForm($item); ?>
<script type="text/javascript">
    jQuery("a#display-tagging-form").click(function(event){
        jQuery("#tagging-form").fadeToggle();
        event.stopImmediatePropagation();
    });
</script>
