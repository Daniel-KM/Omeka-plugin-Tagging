<?php
queue_js_string('
$("a#display-tagging-form").click(function(event){
    $("#tagging-form").fadeToggle();
    event.stopImmediatePropagation();
});
');
?>
<a href="#" id="display-tagging-form" class="button blue right" onclick="return false;"><?php echo $tagging_message; ?></a>
<?php echo $this->getTaggingForm($item); ?>
