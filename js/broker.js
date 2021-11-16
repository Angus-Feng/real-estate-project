var validationCount = 0;

$(document).ajaxError(function () {
	// alert("AJAX error");
});

$(document).ready(function() {
	showProvinces();
	console.log();
	
	$(".prev").click(function(e) {
		showPrevStep(e);
	});

	$("#step1").click(function (e) {
		validateDetails(e);
	});
 
	$("#step2").click(function (e) {
		validateLocation(e);
	});

	$("form").submit(function(e) {
		e.preventDefault();
		$.ajax({
			url: `/addproperty`,
			type: "POST",
			data: new FormData(this),
			processData: false,
			contentType: false,
			error: function(jqxhr, status, errorThrown) {
				httpErrorHandler(jqxhr, status, errorThrown);
			},
			success: function(propertyId) {
				// display last step
				$('#pills-submit').removeClass('fade');
				$("#submitStep").hide();
				$("#pills-submit").show();
				const html = '<p class="center-text">A new property added successfully!</p>'
									+ `<div class="btn-center"><a href=myproperty/${propertyId}>`
									+ '<button class="btn btn-a" type="button">View the Property</button></div>';
				$("#lastStep").append(html);
				// move active pills tab
				$('#pills-images-tab').removeClass('active');
				$('#pills-submit-tab').removeClass('disabled').addClass('active');
				// disabled all tabs except last one 
				$('.form-nav-list li a').not(":last").addClass('disabled');
			}
		});
	});
});

function httpErrorHandler(jqxhr, status, errorThrown) {
	if (jqxhr.status == 403) { // authentication failed
		alert("Authentication failed");
	} else { // other error - inform the user
		alert("AJAX error: " + jqxhr.responseText + ", status: " + jqxhr.status);
	}
}

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
	ajaxValidationRequest('details', jsonString, e);
}

function validateLocation(e) {
	const streetAddress = $("input[name=streetAddress]").val();
	const appartmentNo = $("input[name=appartmentNo]").val();
	const city = $("input[name=city]").val();
	const province = $("select[name=province] option:selected").val();
	const postalCode = $("input[name=postalCode]").val();

	const locationObj = {
		streetAddress: streetAddress,
		appartmentNo: appartmentNo,
		city: city,
		province: province,
		postalCode: postalCode
	};
	const jsonString = JSON.stringify(locationObj);
	ajaxValidationRequest('location', jsonString, e);
}

function ajaxValidationRequest(endpoint, jsonString, e) {
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
			if (result) {
				showNextStep(e);
				removeErrorMsg();
			}
		}
	});
}

function showNextStep(e) {
	console.log(validationCount);
	validationCount++;
	console.log(validationCount);
	
	const targetId = $(e.target).parents('.tab-pane').attr('id');
			
	// display next step
	$(`#${targetId}`).removeClass('show active');
	$(`#${targetId}`).next().removeClass('fade').addClass('show active');
	// $(e.target).parents().next('.fade').removeClass('fade').addClass('active show');
	
	// move active pills tab
	$(`#${targetId}-tab`).removeClass('active');
	$(`#${targetId}-tab`).parents().next('.nav-item').children('a').removeClass('disabled').addClass('active');
}

function showPrevStep(e) {
	const currentStep = $(e.target).parents('.tab-pane');
	const prevStep = $(e.target).parents('.tab-pane').prev();
	currentStep.removeClass('show active').addClass('fade');
	prevStep.removeClass('fade').addClass('show active');
}

function showErrorMsg(jqxhr) {
	if (jqxhr.status == 400) { // validation failed
		const errorList = jqxhr.responseJSON;
		// Create error message nodes and display it under its input box
		for (const error in errorList) {
			let errorMsg = `<span id="errorMsg${error[0].toUpperCase() + error.slice(1)}" class="errorMsg err-msg">${errorList[error]}</span>`;
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