# yii2-ar-query-cache
Active Records chain query cache

## Installation

Recommended installation via [composer](http://getcomposer.org/download/):

```
composer require venomousboy/yii2-ar-query-cache
```

## Usage

In the model needs necessary to redefine method find() and add custom_token in it for requests caching.

For example: class User extends \yii\db\ActiveRecord has method find(): 
 
```php
public static function find()
{
    return Yii::createObject(
        CachedActiveQuery::class,
        [get_called_class(), 'custom_token']
    );
}
```

Call model query:

```php
public function findByCondition($model, array $condition = [])
{
    return $model::find()->where($condition)->cache(3600)->all();
}


public function get($model, $id)
{
    return $model::find()->where(['id' => $id])->cache(3600)->one();
}
```

In relation:

```php
public function getOrder()
{
    return $this->hasOne(Category::class, ['id' => 'category_id'])->cache(3600);
}
```
