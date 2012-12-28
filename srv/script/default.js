function gotoPage(op, target, action)
{
	document.formx.op.value = op;
	document.formx.target = '_self';
	if (target != '') document.formx.target = target;
	if (action != '') document.formx.action = action;
	document.formx.submit();
}

function doClickShisetsuClass(code, name)
{
	document.formq.op.value = 'shisetsu';
	document.formq.ShisetsuClassCode.value = code;
	document.formq.ShisetsuClassName.value = name;
	document.formq.submit();
}

function doClickShisetsu(ccode, scode, name)
{
	document.formq.op.value = 'shitsujyo';
	document.formq.ShisetsuClassCode.value = ccode;
	document.formq.ShisetsuCode.value = scode;
	document.formq.ShisetsuName.value = name;
	document.formq.submit();
}

function doClickShitsujyo(code, name, dest)
{
	document.formq.op.value = dest;
	document.formq.ShitsujyoCode.value = code;
	document.formq.ShitsujyoName.value = name;
	document.formq.submit();
}

function doClickMen(code, name)
{
	document.formq.op.value = 'monthly';
	document.formq.CombiNo.value = code;
	document.formq.CombiName.value = name;
	document.formq.submit();
}

function doClickEvent(scode, ym)
{
	document.formq.op.value = 'event';
	document.formq.ShisetsuCode.value = scode;
	document.formq.UseYM.value = ym;
	document.formq.submit();
}

function doClickKoma(num,base_data,multi)
{
	var i, n;
	var selectValue = document.getElementById('time'+num).innerHTML;
	if (selectValue != base_data) {
		document.getElementById('time'+num).innerHTML = base_data;
		for (i=0; i < multi; ++i) {
			n = num + i;
			document.getElementById('hdntime'+n).value="";
			document.getElementById('chkClicktime'+n).value="0";
		}
	} else {
		document.getElementById('time'+num).innerHTML = "<span class=f-red>申し込む</span>";
		for (i=0; i < multi; ++i) {
			n = num + i;
			document.getElementById('hdntime'+n).value=document.getElementById('hdnnametime'+n).value;
			document.getElementById('chkClicktime'+n).value="1";
		}
	}
}

function doCheckKoma(num, multi)
{
	var i;
	var clickValue = '';
	var fromClick = '';
	var toClick = '';
	var endToClick = 0;

	for (i=0; i<document.getElementById('Komasu').value; ++i)
	{
		clickValue = document.getElementById('chkClicktime'+i).value;
		if (fromClick == '' && clickValue == '1') {
			fromClick='chkClicktime'+i;
			toClick='chkClicktime'+i;
			document.getElementById('timeFrom').value = document.getElementById('hdntimefrom'+i).value;
			document.getElementById('timeTo').value = document.getElementById('hdntimeto'+i).value;
		} else if (fromClick != '' && endToClick != 1 && clickValue == '1') {
			toClick='chkClicktime'+i;
			document.getElementById('timeTo').value = document.getElementById('hdntimeto'+i).value;
		} else if (fromClick != '' && toClick != '' && clickValue == '0') {
			endToClick = 1;
		} else if (fromClick != '' && toClick != '' && clickValue == '1' && endToClick==1) {
			alert("申し込み時間が連続していない状態です。連続した時間にして下さい。");
			clearAllKoma(multi);
			return false;
		}
		if (i == document.getElementById('Komasu').value -1 && toClick != '' && endToClick == 0) {
			endToClick = 1;
		}
	}
	if (endToClick == 0) {
		alert("時間が選択されておりません。時間を選択して下さい。");
		return false;
	}
	document.selectTime.op.value = 'fuzoku';
	document.selectTime.submit();
}

function clearAllKoma(multi)
{
	for (var i=0; i<document.getElementById('Komasu').value; i++)
	{
		if (i%multi == 0) {
			if (document.getElementById('chkClicktime'+i).value == '1')
				doClickKoma(i, document.getElementById('hiddenMark'+i).value, 1);
		}
	}
}

function moveDayWeek(navidate)
{
	document.selectTime.op.value = 'daily';
	document.selectTime.UseDate.value = navidate;
	document.selectTime.action = 'index.php';
	document.selectTime.submit();
}

function doClickFuzoku(selectedFuzoku,base_data)
{
	var selectValue = document.getElementById(selectedFuzoku).innerHTML;
	if (selectValue != base_data) {
		document.getElementById(selectedFuzoku).innerHTML = base_data;
		document.getElementById('chkClick'+selectedFuzoku).value="0";
	} else {
		document.getElementById(selectedFuzoku).innerHTML = "<span class=\"f-red\">申し込む</span>";
		document.getElementById('chkClick'+selectedFuzoku).value="1";
	}
}

function doCheckFuzoku()
{
	document.selectFozuku.op.value = 'mokuteki';
	document.selectFozuku.submit();
}

function doClickMokuteki(code, name)
{
	document.formq.op.value = 'apply_conf';
	document.formq.MokutekiCode.value = code;
	document.formq.MokutekiName.value = name;
	document.formq.submit();
}

function doClickCalendar(month, day)
{
	document.setCondition.op.value = 'jyouken';
	document.setCondition.UseYM.value = month;
	document.setCondition.UseDay.value = day;
	document.setCondition.submit();
}

function doClickType(op, type)
{
	document.setCondition.op.value = op;
	document.setCondition.Type.value = type;
	document.setCondition.submit();
}

function doClickSort(type)
{
	document.forma.sort.value = type;
	document.forma.submit();
}

var focusObj = '';
var fontObjName = '';

function setFocusObj(val)
{
	focusObj = val;
}

function addField(number, id, pass, idMin, passMin)
{
	if (focusObj == 'pw') {
		if (document.account.PasswordTextBox.value.length < pass) {
			document.account.PasswordTextBox.value = document.account.PasswordTextBox.value + number.toString();
		}
	} else {
		if (document.account.UserIdTextBox.value.length < id) {
			document.account.UserIdTextBox.value = document.account.UserIdTextBox.value + number.toString();
		} else {
			if (document.account.PasswordTextBox.value.length < pass) {
				document.account.PasswordTextBox.value = document.account.PasswordTextBox.value + number.toString();
			}
		}
	}
}

function backspaceField()
{
	if (document.account.PasswordTextBox.value.length > 0) {
		var pw = document.account.PasswordTextBox.value;
		document.account.PasswordTextBox.value = pw.substring(0, pw.length - 1);
	} else {
		if (document.account.UserIdTextBox.value.length > 0) {
			var ui = document.account.UserIdTextBox.value;
			document.account.UserIdTextBox.value = ui.substring(0, ui.length -1);
		}
	}
}

function clearField()
{
	document.account.UserIdTextBox.value = '';
	document.account.PasswordTextBox.value = '';
	document.account.UserIdTextBox.focus();
}

function setObjColor(val)
{
	document.getElementById(val).style.color = 'red';
	if (fontObjName != '' && fontObjName != val)
		document.getElementById(fontObjName).style.color = 'black';
	fontObjName = val;
}

function setObj(val)
{
	focusObj = val;
	document.getElementById(val).focus();
}

function addField_Ninzu(number)
{
	if (focusObj == '') {
		setObjColor('UseNinzuTitle');
		setObj('UseNinzu');
	}
	if (document.getElementById(focusObj)) {
		var maxlength = 6;
		if (focusObj == 'UseNinzu') maxlength = 11;
		if (document.getElementById(focusObj).value.length < maxlength) {
			document.getElementById(focusObj).value += number.toString();
		}
	}
}

function backspaceField_Ninzu()
{
	if (focusObj != '')
		if (document.getElementById(focusObj)) {
			var str = document.getElementById(focusObj).value;
			document.getElementById(focusObj).value = str.substring(0,str.length-1);
		}
}

function clearField_Ninzu()
{
	if (focusObj != '')
		if (document.getElementById(focusObj))
			document.getElementById(focusObj).value = '';
}
