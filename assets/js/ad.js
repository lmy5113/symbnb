$("#add-image").click(function () { // get index
    const index = +$("#widget-counter").val();
    // get prototype of entry

    const tmpl = $("#ad_images").data('prototype').replace(/__name__/g, index);
    // insert template
    $("#ad_images").append(tmpl);
    $("#widget-counter").val(index +1);      
    handleDeleteButtons();
});
function handleDeleteButtons() {
$('button[data-action="delete"]').click(function() {
    const target = $(this).data('target');
    $(target).remove();
})
}

function updateCounter() {
const count = +$("ad_images div.form-group").length;

$("#widget-counter").val(count);
}
updateCounter();