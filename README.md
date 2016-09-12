# Работа с последовательностями как строками и массивами

## Установка через composer
```
{
	"require":{
		"infrajs/sequence":"~1"
	}
}
```

## Использование

```php
Sequence::short(['test','param','name']); // 'test.param.name'
Sequence::right('test.param.name']); // ['test','param','name']
```