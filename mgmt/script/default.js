function calcFeeWithGenmen(fee, rate, type)
{
	var tmpfee = fee*(100-rate)/100;
	switch (eval(type)) {
		case 1:
			return Math.ceil(tmpfee);
		case 2:
			return Math.round(tmpfee);
		case 3:
			return Math.floor(tmpfee/10)*10;
		case 4:
			return Math.ceil(tmpfee/10)*10;
		case 5:
			return Math.round(tmpfee/10)*10;
		default:
			return Math.floor(tmpfee);
	}
}

function calcFeeWithTax(fee, rate)
{
	return Math.floor(fee*(100+rate)/100);
}

function setTarget(target)
{
	document.forma.target = target;
}

function submitToMySelf()
{
	document.forma.target = '_self';
	document.forma.submit();
}

function openUrl(url, window_name)
{
	var now = new Date();
	var time = now.getTime();
	window.open(url+'&random='+time, window_name);
}

function clickDay(name, ver, year, month, day)
{
	onChangeMonth(name, ver, month);
	onChangeDay(name, ver, day);
	onChangeYear(name, ver, year);
	window.close();
}

function onChangeYear(name, ver, id)
{
	var item = ver + 'Year';
	if (window.opener.document.forms[name].elements[item] == undefined) {
		return;
	}

	for (var i = 0; i < window.opener.document.forms[name].elements[item].options.length; ++i)
	{
		if (id == window.opener.document.forms[name].elements[item].options[i].value) {
			window.opener.document.forms[name].elements[item].options.selectedIndex = i;
			break;
		}
	}
}

function onChangeMonth(name, ver, id)
{
	var item = ver + 'Month';
	for (var i = 0; i < window.opener.document.forms[name].elements[item].options.length; ++i)
	{
		if (id == (window.opener.document.forms[name].elements[item].options[i].value - 0)) {
			window.opener.document.forms[name].elements[item].options.selectedIndex = i;
			break;
		}
	}
}

function onChangeDay(name, ver, id)
{
	var strday;
	var item = ver + 'Day';
	if (id < 10) {
		strday = 0 + id;
	} else {
		strday = id;
	}
	for (var i = 0; i < window.opener.document.forms[name].elements[item].options.length; ++i)
	{
		if (strday == window.opener.document.forms[name].elements[item].options[i].value) {
			window.opener.document.forms[name].elements[item].options.selectedIndex = i;
			break;
		}
	}
}

function openReserveInfo(YoyakuNum)
{
	var url = 'index.php?op=rsv_04_01_detail&YoyakuNum='+YoyakuNum;
	var param = 'width=600,height=360,menubar=no,toolbar=no,scrollbars=no,resizable=yes';

	window.open(url, 'reserve_info', param);
}

function openMailWindow(num, kbn)
{ 
	var url = 'index.php?op=rsv_popup_mail&YoyakuNum='+num+'&YoyakuKbn='+kbn;
	var param = 'width=480,height=640,menubar=no,toolbar=no,scrollbars=yes,resizable=yes';

	window.open(url, 'mainWindow', param);
}

function openUserMail(id)
{ 
	var url = 'index.php?op=usr_popup_mail&UserID='+id;
	var param = 'width=480,height=640,menubar=no,toolbar=no,scrollbars=yes,resizable=yes';

	window.open(url, 'mainWindow', param);
}

function openNinzuDetail(windowName, key)
{ 
	var url = 'index.php?op=popup_ninzu';
	var param = 'width=480,height=320,menubar=no,toolbar=no,scrollbars=no,resizable=yes';

	if (key != '') url += '&key='+key;

	window.open(url, windowName, param);
}

function openUserList()
{ 
	var url = 'index.php?op=popup_user';
	var param = 'width=650,height=700,menubar=no,toolbar=no,scrollbars=yes,resizable=yes';

	window.open(url, 'userList', param);
}

function openCalendar(ver, name)
{ 
	var param = 'width=370,height=380,menubar=no,toolbar=no,scrollbars=no,resizable=yes';
	var url = 'index.php?op=popup_calendar&ver=' + ver + '&name=' + name;

	var idx, y = '', m = '';
	var item = ver + 'Year';
	if (document.forms[name].elements[item] != undefined) {
		idx = document.forms[name].elements[item].options.selectedIndex;
		y = document.forms[name].elements[item].options[idx].value;
		item = ver + 'Month';
		idx = document.forms[name].elements[item].options.selectedIndex;
		m = document.forms[name].elements[item].options[idx].value;
		url += '&y='+y+'&m='+m;
	}

	if (ver == 'From') {
		window.open(url, 'calendarFrom', param);
	} else {
		window.open(url, 'calendarTo', param);
	}
}

function openPermitPdf(type, num, scd)
{
	var now = new Date();
	var time = now.getTime();
	var url = 'index.php?op=rsv_pdf_permit&type='+type+'&scd='+scd+'&random='+time+'&YoyakuNumAll='+num;
	var obj = document.getElementById('noout');
	if (obj != undefined) {
		if (obj.checked == true) url += '&noout=1'
	}
	window.open(url, type);
}

function openReceiptPdf(num, type)
{
	var d = new Date();
	var time = d.getTime();
	var curYear = '', curMonth = '', curDay = '';
	if (document.forma.RecYear == undefined) {
		curYear = d.getYear();
		if (curYear < 1900) curYear += 1900;
		curMonth = d.getMonth()+1;
		curDay = d.getDate();
	} else {
		curYear = document.forma.RecYear.value;
		curMonth = document.forma.RecMonth.value;
		curDay = document.forma.RecDay.value;
	}
	var dateStr = '&Year='+curYear+'&Month='+curMonth+'&Day='+curDay;
	var url = 'index.php?op=rsv_pdf_receipt&random='+time+'&YoyakuNumAll='+num+dateStr;
	if (type == 1) url += '&mode=single';
	var obj = document.getElementById('noout');
	if (obj != undefined) {
		if (obj.checked == true) url += '&noout=1'
	}
	window.open(url, 'ryosyu');
}

function gotoUserPage(f)
{
	var UserID = f.UserID.value;
	if(UserID) {
		window.open('index.php?op=usr_02_01_03_ref&UserID='+UserID+'&refonly=1#basic');
	}
}

function checkRadio(value)
{
	var i, j, l, item, obj;

	for (i = 0; i < 12; ++i)
	{
		item = 'openkbnval['+i+']';
		l = document.forma.elements[item].length;
		for (j = 0; j < l; ++j)
		{
			obj = document.forma.elements[item][j];
			obj.checked = obj.value == value ? true : false;
		}
	}
}

function alternateCheckBox(form, param)
{
	var item = param+'[]';
	var i, n;
	if (form.elements[item].length) {
		n = form.elements[item].length;
		for (i=0; i<n; ++i)
		{
			if (form.elements[item][i].checked == true) {
				form.elements[item][i].checked = false;
			} else {
				form.elements[item][i].checked = true;
			}
		}
	} else {
		if (form.elements[item].checked == true) {
			form.elements[item].checked = false;
		} else {
			form.elements[item].checked = true;
		}
	}
}

function setPassWord(len, type)
{
	var i, n;
	var seedStr = '';
	var pwdStr = '';

	switch (type) {
		case 2:
			seedStr += 'ABCEDFGHJKLMNPQRSTUVWXYZ';
		case 1:
			seedStr += 'abcdefghijkmnoprstuvwxyz';
		case 0:
			seedStr += '0123456789';
	}

	for (i=0; i<len; ++i) {
		n = Math.floor(Math.random()*10000)%seedStr.length;
		pwdStr += seedStr.charAt(n);
	}
	document.formb.pwd.value = pwdStr;
}

// 3桁区切り
function addKeta( n )
{
	var l, m = '';
	n = '' + n;
	while ( (l = n.length) > 3 ) {
		m = "," + n.substr( l - 3, 3 ) + m;
		n = n.substr( 0, l - 3 );
	}
	n = '' + n + m;
	if (n.substr(0,1)=='-' && n.substr(1,1) == ',') {
		n = n.substring(0,1) + n.substring(2,n.length)
	}

	return n;
}

function deleteKeta( n )
{
	var l , m;
	while ((l = n.indexOf(',')) > 0) {
		n = n.substring(0,l) + n.substring(l+1,n.length);
	}
	return n;
}

function submitTo(form, op)
{
	form.op.value=op;
	form.submit();
}

function set_val(obj, val)
{
	obj.value = val;
}
