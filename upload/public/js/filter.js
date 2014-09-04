function getUSStates() {

	var usStates = new Array();

	usStates["AL"] = "Alabama";
	usStates["AK"] = "Alaska";
	usStates["AZ"] = "Arizona";
	usStates["AR"] = "Arkansas";
	usStates["CA"] = "California";
	usStates["CO"] = "Colorado";
	usStates["CT"] = "Connecticut";
	usStates["DE"] = "Delaware";
	usStates["DC"] = "District Of Columbia";
	usStates["FL"] = "Florida";
	usStates["GA"] = "Georgia";
	usStates["HI"] = "Hawaii";
	usStates["ID"] = "Idaho";
	usStates["IL"] = "Illinois";
	usStates["IN"] = "Indiana";
	usStates["IA"] = "Iowa";
	usStates["KS"] = "Kansas";
	usStates["KY"] = "Kentucky";
	usStates["LA"] = "Louisiana";
	usStates["ME"] = "Maine";
	usStates["MD"] = "Maryland";
	usStates["MA"] = "Massachusetts";
	usStates["MI"] = "Michigan";
	usStates["MN"] = "Minnesota";
	usStates["MS"] = "Mississippi";
	usStates["MO"] = "Missouri";
	usStates["MT"] = "Montana";
	usStates["NE"] = "Nebraska";
	usStates["NV"] = "Nevada";
	usStates["NH"] = "New Hampshire";
	usStates["NJ"] = "New Jersey";
	usStates["NM"] = "New Mexico";
	usStates["NY"] = "New York";
	usStates["NC"] = "North Carolina";
	usStates["ND"] = "North Dakota";
	usStates["OH"] = "Ohio";
	usStates["OK"] = "Oklahoma";
	usStates["OR"] = "Oregon";
	usStates["PA"] = "Pennsylvania";
	usStates["RI"] = "Rhode Island";
	usStates["SC"] = "South Carolina";
	usStates["SD"] = "South Dakota";
	usStates["TN"] = "Tennessee";
	usStates["TX"] = "Texas";
	usStates["UT"] = "Utah";
	usStates["VT"] = "Vermont";
	usStates["VA"] = "Virginia";
	usStates["WA"] = "Washington";
	usStates["WV"] = "West Virginia";
	usStates["WI"] = "Wisconsin";
	usStates["WY"] = "Wyoming";

	return usStates;
}


$(function() {

	populateStates();


});

function populateStates() {
	//$("#geocountry option:selected").text();
	var selectedCountry = $("#geocountry option:selected").val();
	if (selectedCountry == "US") {
		populateStatesSelect();
	} else {
		var noSelectStates = '<input type="hidden" id="geostate" name="geostate" /> <strong>[ US Only Option ]</strong>';
		$("#state-select").html(noSelectStates);
	}
}

function hasCurrentState(currentStateArray, currentState) {

	for (var key in currentStateArray) {
		if (currentStateArray[key] == currentState) {
			return true;
		}
	}

	return false;
}

function registerStates() {

	currentStates = "";
	var comma = "";

	$("#geostate option:selected").each(
			function() {
				currentStates += comma + $(this).val();
				comma = ",";
			}
	);

}

function populateStatesSelect() {
	var selectStatesPre = '<select id="geostate" name="geostate[]"  onchange="registerStates();" multiple style="min-height: 200px">';
	var selectStates = "";
	var foundState = false;
	var states = getUSStates();
	var currentStateArray = currentStates.split(",");


	for (var key in states) {
		if (hasCurrentState(currentStateArray, key) == true) {
			foundState = true;
			selectStates += '<option value="' + key + '" selected="selected">' + states[key] + '</option>';
		} else {
			selectStates += '<option value="' + key + '">' + states[key] + '</option>';
		}
	}

	selectStates += '</select>';

	var allSelectStates = selectStatesPre;
	if (foundState == false) {
		allSelectStates += '<option value="" selected="selected">Choose State</option>';
	}
	allSelectStates += selectStates;

	$("#state-select").html(allSelectStates);
}
