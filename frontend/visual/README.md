# Визуальный поиск

## Project setup
**NODE VERSION: 14**
```
npm install
```

### Compiles and hot-reloads for development
```
npm run dev
```

### Compiles and minifies for production
```
npm run build
```

### Run your tests
```
npm run test
```

### Lints and fixes files
```
npm run lint
```

### Настройки визуального
Опции, заданные через data атрибуты:

```
data-overlay="[Boolean]" - (по умолчанию false) - добавлять ли overlay на визуальный
```

\* — «звездочкой» помечены обязательные data атрибуты

### Типы данных

#### Ракурсы
```
perspective: {
    plan  : string, // path to img 
    points: [
        {
            id: number|string,
            active: boolean,
            top : number, // percent (0-100)
            left: number, // percent (0-100)
            deg : number, // degree (-360-360)
        }
    ]
}
```

### TODO
* [x] Разобраться с isTouch в store и в компонентах (props). Желательно вынести в store и убрать везде из props'ов
