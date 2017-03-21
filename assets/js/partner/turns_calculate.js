/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!Array.prototype.indexOf) {
Array.prototype.indexOf = function (elt /*, from*/) {
        var len = this.length >>> 0;
        var from = Number(arguments[1]) || 0;
        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
        if (from < 0) from += len;

        for (; from < len; from++) {
            if (from in this && this[from] === elt) return from;
        }
        return -1;
    };
}
	
function runScript(e) {
	if (e.keyCode == 13) {
		calculate();
		return false;
	}
	
	allowedKeys = [46, 8, 9, 27, 13, 190];
	if (allowedKeys.indexOf(e.keyCode) !== -1 ||
		 // Allow: Ctrl+A
		(e.keyCode == 65 && e.ctrlKey === true) || 
		 // Allow: home, end, left, right
		(e.keyCode >= 35 && e.keyCode <= 39)) {
			 // let it happen, don't do anything
			 return;
	}
	// Ensure that it is a number and stop the keypress
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		 if (e.preventDefault) { e.preventDefault(); } else { e.returnValue = false; }
	}
}

function calculate(){
	var b3 = document.getElementById("element_1").value;
	var b4 = document.getElementById("element_2").value;
	var b6 = 0;
	
	if(isNaN(b3) || b3<0 || b3==""){
		alert("Please enter a valid number");
		return false;
	}
	
	if(b3 < 400){
		alert("Loan Amount can't be lower than $400");
		return false;
	}
	else if(b3 < 601){
		b6 = 75;
	}
	else if(b3 < 801){
		b6 = 95;
	}
	else if(b3 < 1001){
		b6 = 115;
	}
	else if(b3 < 1501){
		b6 = 135;
	}
	else if(b3 < 2001){
		b6 = 145;
	}
	else if(b3 < 3501){
		b6 = 155;
	}
	else if(b3 < 4251){
		b6 =175;
	}
	else if(b3 < 5251){
		b6 = 185;
	}
	else if(b3 < 6251){
		b6 = 195;
	}
	else if(b3 < 7501){
		b6 = 205;
	}
	else{
		alert("Loan Amount can't be higher than $7500");
		return false;
	}
	document.getElementById("element_3").value = b6;
	
				
	var b7 = Math.round(log10(b6/(b6-(b3*b4)/12))/ log10(1+b4/12));
	document.getElementById("element_4").value = b7;
	
	var b9 = b7*b6;
	document.getElementById("element_5").value = b9;
	
	var b10 = b9-b3;
	document.getElementById("element_6").value = Math.round(b10);
	
	var b11 = (b10/b7).toFixed(2);
	document.getElementById("element_7").value = b11;
	
	
}

function clear_all(){
}

function log10(val) {
	return Math.log(val) / Math.LN10;
}

function numberWithCommas(val) {
	return String(val).split("").reverse().join("")
			.replace(/(\d{3}\B)/g, "$1,")
			.split("").reverse().join("");
}


