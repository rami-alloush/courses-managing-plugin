participantNo = 1;

function AddParticipant() {
	var participantname = document.getElementById('part_code_source').value;
	var amountpaid		= document.getElementById('part_paid_source').value;
	var part_comp		= document.getElementById('part_comp_source').value;
	var part_cert		= document.getElementById('part_cert_source').value;
	if (participantname != "") { // Take only first 7 characters of the "Code | Name" combination
		document.getElementById('participants').innerHTML = document.getElementById('participants').innerHTML + "\
		<tr id='singleparticipantjs"+participantNo+"'>\
		<td><input readonly='readonly' type='text' value='"+participantname.substring(0,6)+"' id='part_code[]' name='part_code[]'/></td> \
		<td><input readonly='readonly' type='text' value='"+amountpaid+"' id='part_paid"+participantNo+"' name='part_paid[]' onBlur='NoEmpty(this.id)' onkeypress='validate(event)'/></td> \
		<td><input readonly='readonly' type='text' value='"+part_comp+"' id='part_comp"+participantNo+"' name='part_comp[]' onBlur='NoEmpty(this.id)'/></td> \
		<td><input readonly='readonly' type='text' value='"+part_cert+"' id='part_cert"+participantNo+"' name='part_cert[]' onBlur='NoEmpty(this.id)' onkeypress='validate(event)'/></td> \
		<td><input type='submit' value='x' id='"+participantNo+"' onclick='RemoveParticipantjs(this.id)'/></td></tr>";
		participantNo += 1;
		
		// Clear Fields
		document.getElementById('part_code_source').value = ""; //empty fields
		document.getElementById('part_paid_source').value = 0;
		document.getElementById('part_comp_source').value = "";
		document.getElementById('part_cert_source').value = 0;
	}
	else { alert('Please select a participant first!')
	}
}

function NoEmpty(id) {
	if (document.getElementById(id).value == "") {
	alert ('This field cannot be empty, you can enter 0 for now');
	document.getElementById(id).value = 0;
	}
}

function RemoveParticipant(id) {
document.getElementById('singleparticipant'+id).innerHTML = "";
}

function RemoveParticipantjs(id) {
document.getElementById('singleparticipantjs'+id).innerHTML = "";
}

 function clearMe(formfield){
  if (formfield.value=="0")
   formfield.value = ""
 }

function validate_eval(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-6]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

/*function AddParticipant2() { //this function was used to intesert participants without table and not used now
	var participantname= document.getElementById('part_code_source').value;
	var amountpaid= document.getElementById('part_paid_source').value;
	if (participantname != "") {
		document.getElementById('participants').innerHTML = document.getElementById('participants').innerHTML + "\
		<div id='singleparticipant"+participantNo+"'>\
		Participant Added: \
		<input type='text' value='"+participantname+"' id='part_code[]' name='part_code[]'/> \
		<input type='text' value='"+amountpaid+"' id='part_paid"+participantNo+"' name='part_paid[]'/> \
		<input type='submit' value='x' id='"+participantNo+"' onclick='RemoveParticipant(this.id)'/>\
		<br/></div>";	participantNo += 1;
	}
	else { alert('Please enter a participant first!')
	}
}*/