$(document).ajaxError(function () {
    alert("AJAX error");
});

$(document).ready(function() {
    displayProvinces();

    var pageCount = 0;
    var currentStep, nextStep, prevStep;
    
    $(".next").click(function() {
        currentStep = $(this).parents('.form-step');
        nextStep = $(this).parents().next();
        nextStep.show();
        currentStep.hide();
    });

    $(".prev").click(function() {
        // debugger;
        currentStep = $(this).parents('.form-step');
        prevStep = $(this).parents().prev();
        prevStep.show();
        currentStep.hide();
    });

    
    // TODO: 
    // 1. implement progress bar
    // 2. validation needs to be done to move to the next step? validation done by client side or server side?
});

function displayProvinces() {
    const provinces = ['NL', 'PE', 'NS', 'NB', 'QC', 'ON', 'MB', 'SK', 'AB', 'BC', 'YT', 'NT', 'NU'];
    let optionList = '<option value="none">Select Province</option>';
    for (let i = 0; i < provinces.length; i++) {
        optionList += `<option value="${provinces[i]}">${provinces[i]}</option>`;
    }
    $("#provinceSelect").html(optionList);
}