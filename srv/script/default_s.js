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

function doClickShitsujyo(code, name)
{
	document.formq.op.value = 'men';
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

function doClickKoma(num, base_data)
{
	var selectValue = document.getElementById('time'+num).innerHTML;
	if (selectValue != base_data) {
		document.getElementById('time'+num).innerHTML = base_data;
		document.getElementById('span'+num).className = '';
		document.getElementById('chkClicktime'+num).value = 0;
	} else {
		document.getElementById('time'+num).innerHTML = '<font class=f-red>申し込む</font>';
		document.getElementById('span'+num).className = 'chks';
		document.getElementById('chkClicktime'+num).value = 1;
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
				doClickKoma(i, document.getElementById('hiddenMark'+i).value);
		}
	}
}

function moveDayWeek(page)
{
	document.selectTime.op.value = 'daily';
	document.selectTime.page_no.value = page;
	document.selectTime.action = 'index.php';
	document.selectTime.submit();
}

function doClickFuzoku(num, base_data)
{
	var selectValue = document.getElementById('fuzoku'+num).innerHTML;
	if (selectValue != base_data) {
		document.getElementById('fuzoku'+num).innerHTML = base_data;
		document.getElementById('span'+num).className = 'nochks';
		document.getElementById('chkClick'+num).value = 0;
	} else {
		document.getElementById('fuzoku'+num).innerHTML = '<font class=\"f-red\">申し込む</sfont>';
		document.getElementById('span'+num).className = 'chks';
		document.getElementById('chkClick'+num).value = 1;
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

function doClickType(type)
{
	document.setCondition.op.value = 'jyouken';
	document.setCondition.Type.value = type;
	document.setCondition.submit();
}
