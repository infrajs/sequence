<?php
namespace infrajs\sequence;

class Sequence {
	const INFRA_SEQ_SELDOM = '·';
	const INFRA_SEQ_OFFEN = '.';
	public static function short($val, $offen = self::INFRA_SEQ_OFFEN, $seldom = self::INFRA_SEQ_SELDOM)
	{
		//Возвращает строку - короткая запись последовательности
		if (is_string($val) == 'string') return $val;
		if (!is_array($val)) $val = array();
		foreach ($val as $i => $s){ 
			$val[$i] = str_replace($offen, $seldom, $s);
		}
		return implode($offen, $val);
	}
	public static function contain($search, $subject)
	{
		//Нужно вернуть всё что после совпадения
		//При полном соответствии, возвращается массив, что в php будет означать false... нужна строгая проверка false чтобы убедиться что search не содержится в subject

		foreach($search as $index => $name){
			if (!isset($subject[$index])) return false;
			if ($name != $subject[$index]) return false; //Весь search не был найден в subject. Если всё равно будет null и сюда не попадаем
		}

		$len = sizeof($search);//Нужно взять всё начинается с len в subject и вернуть.
		return array_slice($subject, $len);
	}
	public static function right($val, $offen = self::INFRA_SEQ_OFFEN, $seldom = self::INFRA_SEQ_SELDOM)
	{
		//Возвращает массив - правильную запись последовательности

		if (!is_array($val)) {
			if (!is_string($val)) {
				$val = '';
			}
			$val = explode($offen, $val);

			foreach($val as $i=>$s){
				$val[$i] = str_replace($seldom, $offen, $val[$i]);
			}

			if ($val[sizeof($val) - 1] === '') {
				array_pop($val);
			}
			if (isset($val[0]) && $val[0] === '') {
				array_shift($val);
			}

			$val = array_values($val);
		}
		$res = array();
		for ($i = 0, $l = sizeof($val);$i < $l;++$i) {
			$s = $val[$i];
			if ($s === '' && sizeof($res) != 0 && (!isset($res[$i - 1]) || $res[$i - 1] !== '')) {
				//Сами себя не должны отменять
				array_pop($res);
			} else {
				$res[] = $s;
			}
		}

		return $res;
	}
	public static function &set(&$obj, $right, &$val)
	{
		$make = !is_null($val);
		$i = sizeof($right) - 1;
		if ($i == -1) {
			$obj = &$val;
			return $obj;
		}
		if ($make && !is_array($obj)) {
			$obj = array();
		}
		$need = &Sequence::get($obj, $right, 0, $i, $make);
		if (!$make && is_array($need)) {
			unset($need[$right[$i]]);
		}
		if ($make) {
			$need[$right[$i]] = &$val;
		}

		return $obj;
	}
	public static function &get(&$obj, $right, $start = 0, $end = null, $make = false)
	{
		//получить из obj значение right до end(не включая) брать начинаем с start
		if (is_null($end)) {
			$end = sizeof($right);
		}
		$r = null;
		if ($end === $start) {
			return $obj;
		}
		if (is_null($obj)) {
			return $r;
		}//Даже если make мы не изменим ссылку null на obj в javascript так что и тут так

		if (is_array($obj)) {
			if ($make && (!isset($obj[$right[$start]])||!is_array($obj[$right[$start]]))) {
				$obj[$right[$start]] = array();
			}
			if ($make || (isset($obj[$right[$start]]) && !is_null($obj[$right[$start]]))) {
				//Если передать несуществующее свойство в функцию принимающую ссылку то это свойство начнёт существовать
				return Sequence::get($obj[$right[$start]], $right, ++$start, $end, $make);
			}
		} elseif (is_object($obj)) {
			$name = $right[$start];
			if ($make && !is_array($obj->$name)) {
				$obj->$name = array();
			}
			if (property_exists($obj, $name)) {
				//К методам объектов обращаться не можем
				return Sequence::get($obj->$name, $right, ++$start, $end, $make);
			}
		} else {
			return $r;
		}

		return $r;
			/*
		if(is_null($end))$end=sizeof($right);
		if($end===$start)return $obj;
		if(is_null($obj))return;
		
		if(is_array($obj)){
			if($make&&!is_array($obj[$right[$start]]))$obj[$right[$start]]=array();
			if(array_key_exists($right[$start],$obj)){
				return Sequence::get($obj[$right[$start]],$right,++$start,$end,$make);
			}
		}else if(is_object($obj)){
			if($make&&!is_array($obj->$$right[$start]))$obj->$$right[$start]=array();
			if(property_exists($obj,$right[$start])){//К методам объектов обращаться не можем
				return Sequence::get($obj->$right[$start],$right,++$start,$end,$make);
			}
		}else{
			return NULL;
		}*/
	}
}