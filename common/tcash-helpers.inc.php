<?php

function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text)
{
	echo html($text);
}

function markdownout($text)
{
	echo markdown2html($text);
}

function devmode()
{
	if ($_SERVER['HTTP_HOST'] == 'localhost')
	{
		return true;
	}
}

function raspi() {
    if ($_SERVER['HTTP_HOST'] == 'raspberrypi') {
        return true;
    }
}

function raspi2() {
    if ($_SERVER['HTTP_HOST'] == 'raspi2') {
        return true;
    }
}

function setdevmodeparams()
{
	if (devmode())
	{
		$_SESSION['devmode'] = true;
		$_SESSION['envname'] = '[DEV]';
	}
	elseif (raspi()) 
	{
		$_SESSION['devmode'] = false;
		$_SESSION['envname'] = '[RASPI]';
	}
	elseif (raspi2()) 
	{
		$_SESSION['devmode'] = false;
		$_SESSION['envname'] = '[RASPI2]';
	}
    else 
    {
        $_SESSION['devmode'] = false;
        $_SESSION['envname'] = '';
    }
}


function safesessionstart()
{
	if(session_id() == '')
	{
		session_start();
	}
}


function markdown2html($text)
{
	$text = html($text);

	//convert strong emphasis
	$text = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $text);
	$text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

	//convert emphasis
	$text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
	$text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);

	//str_replace is more efficient than preg_replace
	$text = str_replace("\r\n", "\n", $text);
	$text = str_replace("\r", "\n", $text);
	$text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';
	$text = str_replace("\n", '<br>', $text);

	//hyperlinks = [linked text](link URL)
	$text = preg_replace('/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i', '<a href="$2">$1</a>', $text);

	return $text;
}

