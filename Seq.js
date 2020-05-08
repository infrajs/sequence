/*
 * autosave template session state
 * */
let Seq = {
	seldom: '·',
	offen: '.',
	forr: function (el, callback) { //Бежим по массиву
		if (!(el instanceof Array)) return;
		let r, i, l;
		for (i = 0, l = el.length; i < l; i++) { //В callback нельзя удалять... так как i сместится
			if (el[i] == null) continue;
			r = callback(el[i], i, el); //callback,name,context,args,more
			if (r != null) return r;
		}
	},
	short: function (val, offen, seldom) {//Возвращает строку - короткая запись последовательности
		offen = offen || Seq.offen;
		seldom = seldom || Seq.seldom;
		if (typeof (val) == 'string') return val;
		if (!val || typeof (val) != 'object' || val.constructor != Array) val = [];
		var nval = [];
		if (val[0] == '') nval.push('');
		for (var i = 0, l = val.length; i < l; i++) {
			var s = String(val[i]);
			nval.push(s.replace(offen, seldom));
		};
		return nval.join(offen);
	},
	contain: function (search, subject) {
		return !Seq.forr(search, function (name, index) {
			if (name != subject[index]) return true;
		});
	},
	right: function (val, offen = Seq.offen, seldom = Seq.seldom) {//Возвращает массив - правильную запись последовательности
		if (!val || typeof (val) !== 'object' || val.constructor !== Array) {
			if (typeof (val) != 'string') val = '';
			val = val.split(offen);
			Seq.forr(val, function (s, i) {
				val[i] = s.replace(seldom, offen);//Знак offen используется часто и должна быть возможность его указать в строке без специального смысла.. вот для этого и используется знак seldom 
			});
			if (val[val.length - 1] === '') val.pop();
			if (val[0] === '') val.shift();
		}
		var res = [];
		for (var i = 0, l = val.length; i < l; i++) {
			var s = val[i];
			if (s === '' && res.length != 0 && res[i - 1] !== '') {
				res.pop();
			} else {
				res.push(s);
			}
		}
		return res;
	},
	add: function (obj, right, val) {
		var i = right.length;
		if (typeof (val) == 'undefined' || val === null) return obj;
		if (!obj || typeof (obj) != 'object' || obj.constructor != Array) obj = [];
		var need = Seq._getmake(obj, right, 0, i);
		if (~need.indexOf(val)) need.push(val);
		return obj;
	},
	set: function (obj, right, val) {
		var make = val == null ? false : true;
		var i = right.length - 1;
		if (i == -1) return val;
		if (make && (!obj || typeof (obj) !== 'object') && typeof (obj) !== 'function') obj = {};
		if (!make) var need = Seq.getr(obj, right, 0, i);
		else var need = Seq._getmake(obj, right, 0, i);
		if (!make && (need && typeof (need) == 'object')) delete need[right[i]];
		if (make) need[right[i]] = val;
		return obj;
	},
	get: (obj, short, def = null) => {
		let right = Seq.right(short);
		let res = Seq.getr(obj, right);
		return (res == null) ? def : res;
	},
	_getmake: function (obj, right, start = 0, end = right.length) {//получить из obj значение right до end брать начинаем с start
		if (typeof (start) === 'undefined') start = 0;
		if (typeof (end) === 'undefined') end = right.length;
		if (end === start) return obj;
		if (obj == null) return;

		if (((!obj[right[start]] || typeof (obj[right[start]]) !== 'object') && typeof (obj[right[start]]) !== 'function')) obj[right[start]] = {};
		if ((obj && typeof (obj) == 'object') || typeof (obj) == 'function') {
			if (((obj === location || (!obj.hasOwnProperty)) && obj[right[start]]) || obj.hasOwnProperty(right[start])) {
				//в ie у location есть свойство hasOwnProperty но все свойства не являются собственными у location. в ff у location нет метода hasOwnProperty
				return Seq._getmake(obj[right[start]], right, ++start, end);
			}
		}
	},
	getr: function (obj, right, start = 0, end = right.length) {//получить из obj значение right до end брать начинаем с start
		if (end === start) return obj;
		if (obj == null) return;
		if ((obj && typeof (obj) == 'object') || typeof (obj) == 'function') {
			if (((obj === location || (!obj.hasOwnProperty)) && obj[right[start]]) || obj.hasOwnProperty(right[start])) {
				//в ie у location есть свойство hasOwnProperty но все свойства не являются собственными у location. в ff у location нет метода hasOwnProperty
				return Seq.getr(obj[right[start]], right, ++start, end);
			}
		}
	}
}
export { Seq }
export default Seq
