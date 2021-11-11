$(document).ajaxError(function () {
	// alert("AJAX error");
});

$(document).ready(function() {
	showProvinces();

	var validationCount = 0;
	// console.log(validationCount);
	
	// $(".next").click(function() {
	// 	currentStep = $(this).parents('.form-step');
	// 	nextStep = $(this).parents().next();
	// 	nextStep.show();
	// 	currentStep.hide();
	// });

	$(".prev").click(function() {
		currentStep = $(this).parents('.form-step');
		prevStep = $(this).parents().prev();
		prevStep.show();
		currentStep.hide();
	});

	$("#step1").click(function (e) {
		validateDetails(e);
		// validationCount += 1;
		// console.log(validationCount);
	});

	$("#step2").click(function (e) {
		validateLocation(e);
		// validationCount += 1;
		// console.log(validationCount);
	});
	
	// TODO: 
	// 1. implement progress bar
	// 2. handle internal errors (no validation errors)
});

function validateDetails(e) {
	const title = $("input[name=title]").val();
	const price = $("input[name=price]").val();
	const bedrooms = $("input[name=bedrooms]").val();
	const bathrooms = $("input[name=bathrooms]").val();
	const buildingYear = $("input[name=buildingYear]").val();
	const lotArea = $("input[name=lotArea]").val();
	const description = $("textarea[name=description]").val();

	const detailsObj = {
		title: title,
		price: price,
		bedrooms: bedrooms,
		bathrooms: bathrooms,
		buildingYear: buildingYear,
		buildingYear: buildingYear,
		lotArea: lotArea,
		description: description
	};
	const jsonString = JSON.stringify(detailsObj);
	ajaxPostRequest('details', jsonString, e);
}

function validateLocation(e) {
	const streetAddress = $("input[name=streetAddress]").val();
	const appartmentNo = $("input[name=appartmentNo]").val();
	const city = $("input[name=city]").val();
	const province = $("select[name=province] option:selected").val();
	const postalCode = $("input[name=postalCode]").val();
	// console.log(province);

	const locationObj = {
		streetAddress: streetAddress,
		appartmentNo: appartmentNo,
		city: city,
		province: province,
		postalCode: postalCode
	};
	const jsonString = JSON.stringify(locationObj);
	ajaxPostRequest('location', jsonString, e);
}

function ajaxPostRequest(endpoint, jsonString, e) {
	$.ajax({
		url: `/ajax/addpropertyval/${endpoint}`,
		type: "POST",
		data: jsonString,
		dataType: "json",
		error: function(jqxhr, status, errorThrown) {
			// Reset error mssages from previous call
			removeErrorMsg();
			showErrorMsg(jqxhr);
		},
		success: function(result) {
			if (result) showNextStep(e);
		}
	});
}

function showNextStep(e) {
	const currentStep = $(e.target).parents('.form-step');
	const nextStep = $(e.target).parents().next();
	nextStep.show();
	currentStep.hide();
}

// FIXME: error message for Price is not showing 
function showErrorMsg(jqxhr) {
	if (jqxhr.status == 400) { // validation failed
		const errorList = jqxhr.responseJSON;
		// TODO: add red outline in input box
		// Create error message nodes and display it under its input box
		for (const error in errorList) {
			let errorMsg = `<span id="errorMsg${error[0].toUpperCase() + error.slice(1)}" class="errorMsg">${errorList[error]}</span>`;
			if (error === 'description') {
				$('textarea[name=description]').after(errorMsg);
			}
			if (error === 'province') {
				$('select[name=province]').after(errorMsg);
			}
			$(`input[name=${error}]`).after(errorMsg);
		}
	}
}

function removeErrorMsg() {
	const errorMsg = $("span.errorMsg");
	if (errorMsg) {
		errorMsg.remove();
	}
}

function showProvinces() {
	const provinces = ['NL', 'PE', 'NS', 'NB', 'QC', 'ON', 'MB', 'SK', 'AB', 'BC', 'YT', 'NT', 'NU'];
	let optionList = '<option value="none">- Select -</option>';
	for (let i = 0; i < provinces.length; i++) {
		optionList += `<option value="${provinces[i]}">${provinces[i]}</option>`;
	}
	$("#provinceSelect").html(optionList);
}