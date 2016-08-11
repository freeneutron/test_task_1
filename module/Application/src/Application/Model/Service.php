<?php

namespace Application\Model;

class Service{
	static $codemirror;
	static $codemirror_lib = '/js/codemirror/codemirror-2.34';
	static $codemirror_i = 0;

	static function dump($o,$f=0){
		$s = print_r($o,1);
		if($f == 1)echo"<pre>$s</pre>";
		if($f == 2)error_log($s);
		if($f == 3)error_log(iconv($s,'utf-8','windows-1251'));
		if($f == 4)error_log(urlencode($s));
		return$s;
	}
	static function json($code){
		$s = '<pre>'.json_encode((array)$code,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
		return $s;
	}
	static function yaml($code){
		$ret = array('');
		if(!self::$codemirror){
			self::$codemirror = 1;
			$codemirror_lib = self::$codemirror_lib;
			$ret[]= "<style>";
			$ret[]= "    @import '$codemirror_lib/lib/codemirror.css';";
			$ret[]= "</style>";
        	$ret[]= "<script src='$codemirror_lib/lib/codemirror.js'></script>";
        	$ret[]= "<script src='$codemirror_lib/mode/yaml/yaml.js'></script>";
		}
		$i = self::$codemirror_i++;
		//$ret[]= "<textarea id='code_$i'>$code</textarea>";
		//$ret[]= "<script>CodeMirror(document.getElementById('code_$i'),{mode: 'yaml'});</script>";
		$code = str_replace(array('"',"\r\n","\n"),array('\"','\n','\n'),$code);
		$ret[]= "<div id='code_$i' style='height:100%;'></div>";
		$ret[]= "<script>
			var code = CodeMirror(document.getElementById('code_$i'), {
				value: \"$code\",
				mode: 'yaml',
				lineNumbers:true,
				height: 'auto', overflow: 'visible',
				readOnly: true
			});
			code.setSize(null,code.getScrollInfo().height);
		</script>";
		return join("\n",$ret);
	}
	static function detectUTF8($string){
		return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
		)+%xs', $string)?'UTF-8':'WINDOWS-1251';
	}
}
