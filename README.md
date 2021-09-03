#Система типов

> Обозначения
> * `~` - беззнаковый для чисел. Например `~Float`
> * `?` - допускает `null`. Например `String?`
> * `=` - в случае `null` будет присвоено значение. Например `Float? = 0.0`   


## Скаляры (Scalar)

### String

```js
{ 
    name: "String",
    min: ~Integer? = 0, 
    max: ~Integer? = 255,
}
```

### Integer

```js
{
    name: "Integer",
    min: Integer = -2147483647,
    max: Integer = 2147483647,
}
```

### Float
    
```js
{
    name: "Float",
    min: Float? = PHP_FLOAT_MIN,   
    max: Float? = PHP_FLOAT_MAX, 
    step: Float? = 0.01,
}
```

### Boolean
```js
{
    name: "Boolean",
}
```


### Text
```js
{ 
    name: "Text",
    min: ~Integer? = 0, // Символы
    max: ~Integer? = 21844, // Символы
}
```

### Wiki
То же самое, что `Text`


## Generics

### List
```js
{
    name: "List",
    max: ~Integer?, // null расценивается как "бес передела сверху"
    of: File|Dictionary|Enum // Списком чего именно является данный тип
}
```
#### примеры
```js
// Список коротких строковых значений
{
    name: "List",
    max: 5,
    of: {
      name: "String"
      max: null
      min: null,
    } 
}
```
```js
// Список из значений перечисления
{
    name: "List",
    max: 5,
    of: {
      name: "Enum"
      of: "123e4567-e89b-12d3-a456-426655440000"
    } 
}
```

### Dictionary
```js
{
    name: "Dictionary",
    of: UUID // Идентификатор словаря
}
```

### Enum
```js
{
    name: "Enum",
    of: UUID // Идентификатор перечисления
}
```

### File
```js
{
    name: "File",
    max: ~Integer? // Килобайты
}
```

