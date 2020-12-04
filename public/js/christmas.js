$(document).ready(function() {

    $(".displayResults").click(function () {
        $(".giftTo").show();
        $(".displayResults").hide();
        $(".hiddenResults").show();
    });

    $(".hiddenResults").click(function () {
        $(".giftTo").hide();
        $(".hiddenResults").hide()
        $(".displayResults").show();

    });

});