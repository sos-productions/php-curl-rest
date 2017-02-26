# php-curl-rest 2.0
Universal HTTP/FTP Curl Client with REST interface (plugins and commands autoloader)

This project tries to uniformize curl function calls using a REST style. I has for now two plugins : HTTP and FTP.
I have spend hard time to gather all the power of curl in one point integrating all snippets code I found ( buggy or working, with
doc or not, etc...), and adding a common wrapper to execute commands. Simply enjoy and feel free to contribute.

<style type="text/css">
		.phpinfodisplay {background-color: #ffffff; color: #000000;}
		.phpinfodisplay ,.phpinfodisplay  td,.phpinfodisplay  th,.phpinfodisplay  h1,.phpinfodisplay  h2 {font-family: sans-serif;}
		.phpinfodisplay pre {margin: 0px; font-family: monospace;}
		.phpinfodisplay a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
		.phpinfodisplay a:hover {text-decoration: underline;}
		.phpinfodisplay table,.table {border-collapse: collapse; }
		.phpinfodisplay .center {text-align: center;}
		.phpinfodisplay .center table { margin-left: auto; margin-right: auto; text-align: left;}
		.phpinfodisplay .center th { text-align: center !important; }
		.phpinfodisplay td, .td, .phpinfodisplay  th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
		.phpinfodisplay h1 {font-size: 150%;}
		.phpinfodisplay h2 {font-size: 125%;}
		.phpinfodisplay .p {text-align: left;}
		.phpinfodisplay .e {background-color: #ccccff; font-weight: bold; color: #000000;}
		.phpinfodisplay .h {background-color: #9999cc; font-weight: bold; color: #000000;}
		.phpinfodisplay .v {background-color: #cccccc; color: #000000;}
		.phpinfodisplay .vr {background-color: #cccccc; text-align: right; color: #000000;}
		.phpinfodisplay img {float: right; border: 0px;}
		.phpinfodisplay hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}</style>
		
<div class="phpinfodisplay">
<div align="center">
<table width="600" cellpadding="3" border="0">
<tbody>
<tr><td class="e">Author</td><td class="v">Olivier Lutzwiller, Michael C Brant</td></tr>
<tr><td class="e">Realease date</td><td class="v">26.02.2017</td></tr>
<tr><td class="e">Core API</td><td class="v">2.0</td></tr>
<tr><td class="e">Thanks to</td><td class="v">Daniel Stenberg</td></tr>
</tbody></table><br>
</div>
<div align="center">
<table width="600" cellpadding="3" border="0">
<tbody><tr><td class="v"><a href="http://www.all-informatic.com/"><img src="data:image/png;base64,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" alt="All Informatic logo" border="0"></a>
 This program makes use of Curl php made by Sterling Hughes. Installed 
version is 7.38.0 which supports 
dict/file/ftp/ftps/gopher/http/https/imap/imaps/ldap/ldaps/pop3/pop3s/rtmp/rtsp/scp/sftp/smtp/smtps/telnet/tftp.<br>Big thanks to Daniel Stenberg for Curl!</td></tr>
</tbody></table><br>
</div>
<div align="center">
<h2>Overview</h2>
<table width="600" cellpadding="3" border="0">
<tbody><tr><td class="e">plugins</td><td class="v">HTTP, FTP</td></tr>
<tr><td class="e"><a title="2 in total"> plugins details</a></td><td class="v"><div style="display:table;width:100%;" class="table">
<div style="display:table-row;" class="h">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="h">type</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="h">command</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="h">date</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="h">authors</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MPUT</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">GET</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">POST</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">PUT</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">DELETE</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MPOST</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MGET</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HEAD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MHEAD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">HTTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MDELETE</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">26.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Michael C Brant,Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MKD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">RMD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">NLIST</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">RAWLIST</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">MLSD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">GET</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">DOWNLOAD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">UPLOAD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">DELE</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">RNFR</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">PUT</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">RNTO</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">CWD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">PWD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">LS</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">CD</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">RETR</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
<div style="display:table-row;" class="r">
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">FTP</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">LSR</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">25.02.2017</div>
<div style="display:table-cell;text-align:center;border:#9999cc solid 2px" class="d">Olivier Lutzwiller</div>
</div>
</div></td></tr>
</tbody></table><br>
</div>
<div align="center">
<div style="width:600px; background:#000; height:1px;margin-bottom:1em;"></div>
</div>
<div align="center">
<h1>Plugins</h1>
<h2>HTTP</h2>
<table width="600" cellpadding="3" border="0">
<tbody><tr class="h"><th>Variables</th><th>Value</th></tr>

<tr><td class="e">MPUT</td><td class="v">1.0.0</td></tr>
<tr><td class="e">GET</td><td class="v">1.1.0</td></tr>
<tr><td class="e">POST</td><td class="v">1.0.0</td></tr>
<tr><td class="e">PUT</td><td class="v">1.0.0</td></tr>
<tr><td class="e">DELETE</td><td class="v">1.1.0</td></tr>
<tr><td class="e">MPOST</td><td class="v">1.0.0</td></tr>
<tr><td class="e">MGET</td><td class="v">1.0.0</td></tr>
<tr><td class="e">HEAD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">MHEAD</td><td class="v">1.0.0</td></tr>
<tr><td class="e">MDELETE</td><td class="v">1.0.0</td></tr>
</tbody></table><br>
<h2>FTP</h2>
<table width="600" cellpadding="3" border="0">
<tbody><tr class="h"><th>Variables</th><th>Value</th></tr>

<tr><td class="e">MKD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">RMD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">NLIST</td><td class="v">1.2.0</td></tr>
<tr><td class="e">RAWLIST</td><td class="v">1.2.0</td></tr>
<tr><td class="e">MLSD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">GET</td><td class="v">1.2.0</td></tr>
<tr><td class="e">DOWNLOAD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">UPLOAD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">DELE</td><td class="v">1.2.0</td></tr>
<tr><td class="e">RNFR</td><td class="v">1.2.0</td></tr>
<tr><td class="e">PUT</td><td class="v">1.2.0</td></tr>
<tr><td class="e">RNTO</td><td class="v">1.2.0</td></tr>
<tr><td class="e">CWD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">PWD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">LS</td><td class="v">1.2.0</td></tr>
<tr><td class="e">CD</td><td class="v">1.2.0</td></tr>
<tr><td class="e">RETR</td><td class="v">1.2.0</td></tr>
<tr><td class="e">LSR</td><td class="v">1.2.0</td></tr>
</tbody></table><br>
</div>
</div>
