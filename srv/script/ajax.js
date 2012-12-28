function submitZipCode()
{
	var code1 = document.getElementById('PostNo1').value;
	var code2 = document.getElementById('PostNo2').value;

	if (code1 == '' || code2 == '') {
		alert('郵便番号を入力してください。');
		return false;
	} else if (code1.match(/[^0-9]/) || code2.match(/[^0-9]/)) {
		alert('郵便番号は半角数字で入力してください。');
		return false;
	}

	var url = "index.php?op=ajax&cmd=zipcode&code="+code1+code2;

	getByAjax(url, setPostalAddress);

	return true;
}

function setPostalAddress(request)
{
	var res, err, addr;

	res = request.responseXML;

	addr = res.documentElement.getElementsByTagName('address');

	err = addr[0].getAttribute('error');
	if (err == '1') {
		document.getElementById('posterror').innerHTML = addr[0].firstChild.nodeValue;
		document.getElementById('Adr1').value = '';
		return;
	}
	document.getElementById('posterror').innerHTML = '';
	document.getElementById('Adr1').value = addr[0].firstChild.nodeValue;
}

function setUserStatus(request)
{
	var res;

	res = request.responseXML;

	var userid = res.documentElement.getElementsByTagName('UserID');
	var name = res.documentElement.getElementsByTagName('NameSei');
	var namekana = res.documentElement.getElementsByTagName('NameSeiKana');
	var status = res.documentElement.getElementsByTagName('Status');
	var usekbn = userid[0].getAttribute('UseKbn');
	var kbn = status[0].getAttribute('UserJyoutaiKbn');
	var flg = status[0].getAttribute('useCheckFlag');

	var Obj = document.forma;

	Obj.NameSei.value = ''
	Obj.NameSeiKana.value = '';
	Obj.UserJyoutai.value = '';

	Obj.UserID.value = userid[0].firstChild ? userid[0].firstChild.nodeValue : '';
	if (kbn != '') {
		Obj.NameSei.value = name[0].firstChild ? name[0].firstChild.nodeValue : '';
		Obj.NameSeiKana.value = namekana[0].firstChild ? namekana[0].firstChild.nodeValue : '';
	}
	if (kbn != '1') {
		Obj.UserJyoutai.value = status[0].firstChild.nodeValue;
	}
	Obj.UseKbn.value = usekbn;
	Obj.UserJyoutaiKbn.value = kbn;
	Obj.useCheckFlag.value = flg;
}


function getByAjax(url, callback)
{
	var request;

	if (window.ActiveXObject) {
		try {request = new ActiveXObject('MSXML2.XMLHTTP');}
		catch (e) {
			try {request = new ActiveXObject('Microsoft.XMLHTTP');}
			catch (e2) {return;}
		}
	} else request = new XMLHttpRequest;

	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			request.onreadystatechange = doNothing;
			callback(request);
		}
	};

	request.open('GET', url, true);
	request.send(null);
}

function doNothing()
{
}
