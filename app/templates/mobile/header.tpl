<?xml version="1.0" encoding="shift_jis"?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=shift_jis">
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="robots" content="noarchive">
<meta name="copyright" content="Copyright 2010-2012 ZiWAVE Co., Ltd." />
<title>{$smarty.const._SYSTEM_NAME_}</title>
</head>
<body>
<div style="font-size:x-small;">

<div align="center">
<a name="haeder" id="haeder"></a>
<h1 style="font-size:medium;background:#e4ff00; text-align:center; padding: 2px;border:1px solid #9ee600; margin:0;">{$smarty.const._SYSTEM_NAME_}</h1>
{if ! $TOP_LINK}
<img src="image/mobile/logo.gif" alt="{$smarty.const._SYSTEM_NAME_}" /><br />
{/if}
{if $condition}
<h2 style="font-size:x-small;background:#f2ea8f; text-align:center; padding: 2px;border:1px solid #d9ce51;margin-top:8px;">
{$condition}
</h2>
{/if}
</div>

{if $message}<div style="padding: 8px 0;">{$message}</div>{/if}

<div style="padding:0 2px;">
<!--contents-->
