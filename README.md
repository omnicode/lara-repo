
<p align="center">
<a href="https://travis-ci.org/omnicode/lara-repo"><img src="https://travis-ci.org/omnicode/lara-repo.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/omnicode/lara-repo"><img src="https://poser.pugx.org/omnicode/lara-repo/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/omnicode/lara-repo"><img src="https://poser.pugx.org/omnicode/lara-repo/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/omnicode/lara-repo"><img src="https://poser.pugx.org/omnicode/lara-repo/license.svg" alt="License"></a>
</p>

## Installation

Run the following command from you terminal:


 ```bash
 composer require "omnicode/lara-repo: 2.0.*"
 ```

or add this to require section in your composer.json file:

 ```
 "omnicode/lara-repo": "2.0.*"
 ```

then run ```composer update```


## Usage


First, create your RepositoryInterface interface that should extend
```LaraRepo\Contracts\RepositoryInterface``` 

e.g.

```php
<?php

namespace App\Repositories\Contracts;

use LaraRepo\Contracts\RepositoryInterface;

interface AccountRepositoryInterface extends RepositoryInterface
{
    
}

```

Next, create your repository class. Note that your repository class should extend ```LaraRepo\Eloquent\AbstractRepository```  and have ```modelClass()``` method

e.g.
```php
<?php

namespace App\Repositories\Eloquent;

use LaraRepo\Eloquent\AbstractRepository;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Models\Account;

class AccountRepository extends AbstractRepository  implements AccountRepositoryInterface
{
    public function modelClass()
    {
        return Account::class;
    }
}
```

```modelClass()``` method is used to identify the model class


It is suggested to use LaraModel repository to utilize all the functionality

 ```
 "omnicode/lara-model": "2.0.*"
 ```
and use

And you can create ```App\Models\Account``` model

```php
<?php

namespace App\Models;

use LaraModel\Models\LaraModel;

class Account extends LaraModel
{
    
}
```


Next Bind it in ServiceProvider.

```php
<?php

namespace App\Providers;

use App\Repositories\Contracts\AccountUserRepositoryInterface;
use App\Repositories\Eloquent\AccountRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AccountUserRepositoryInterface::class, AccountRepository::class);
    }
}

```


And finally, use the repository in your controller or service layer

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\AccountRepositoryInterface as AccountRepository;

class AccountsController extends Controller
{

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * AccountsController constructor.
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $items = $this->accountRepository->all(); 
        return view('accounts.index', compact('items'));
    }
}

```

## Available Methods

The following methods are available:

##### LaraRepo\Contracts\RepositoryInterface

```php
    public function makeModel();
    public function getModel();
    public function getModelQuery();
    public function resetModelQuery();
    public function getTable();
    public function getKeyName();
    public function fixColumns($columns, $table = null, $prefix = null);
    public function getFillableColumns();
    public function getIndexableColumns($full = false, $hidden = true, $group = self::GROUP);
    public function getShowableColumns($full = false, $hidden = true, $group = self::GROUP);
    public function getSearchableColumns();
    public function getListableColumns();
    public function getSortableColumns($column = null, $group = self::GROUP);
    public function getStatusColumn();
    public function setSortingOptions($column = null, $order = 'asc', $group = self::GROUP);
    public function getRelations();
    public function saveAssociated($data, $options = [], $model = null);
    public function create(array $data);
    public function createWith(array $data, $attribute, $value);
    public function increment($column, $value);
    public function update(array $data, $id, $attribute = "id");
    public function updateBased(array $data, array $conditions);
    public function destroy($id);
    public function destroyBy($attribute, $value);
    public function all($columns = null);
    public function first($columns = null);
    public function last($columns = null);
    public function find($id, $columns = null);
    public function findForShow($id, $columns = null);
    public function findBy($attribute, $value, $columns = null);
    public function findAllBy($attribute, $value, $columns = null);
    public function findAttribute($id, $attribute);
    public function findFillable($id);
    public function findAllFillable($attribute, $value);
    public function findFillableWith($id, $related = []);
    public function findFillableWhere($id, $field, $value, $cmp = '=');
    public function findList($active = true, $listable = null);
    public function findListBy($attribute, $value, $active = true, $listable = null);
    public function paginate($perPage = 15, $columns = null, $group = self::GROUP);
    public function paginateWhere($attribute = '', $value = '', $cmp = '=');
    public function findCount($attribute = null, $value = null, $cmp = '=');
    public function exists($id);
    public function existsWhere($attribute, $value);
}
```

##### LaraRepo\Contracts\CriteriaInterface

```php
    public function getCriteria();
    public function getByCriteria(Criteria $criteria);
    public function resetScope();
    public function pushCriteria(Criteria $criteria);
    public function skipCriteria($status = true);
    public function applyCriteria();
```

##### LaraRepo\Contracts\TransactionInterface

```php
    public function startTransaction();
    public function commitTransaction();
    public function rollbackTransaction();
```

### Example usage


Create a new account repository:

e.g.
```php
    
    $this->accountRepository->create($request->all());
    
    $this->accountRepository->createWith($request->all(), 'name', $name);
    
    it is equivalent 
    
    $data = array_merge($request->all(), ['name' => $name])
    $this->accountRepository->create($data);
    
    //if your model extends LaraModel/Models/LaraModel you can use saveAssociated Method for saving new account
    //or update existing model with relations
    $this->accountRepository->saveAssociated($data, $relations);
    
    //for updating with relations
    $this->accountRepository->saveAssociated($data, $relations, $account);
```

Update existing account:

```php
    // update based on id
    $this->accountRepository->update($request->all(), $accountId);

    // update based on attribute
    $this->accountRepository->update($request->all(), $attribute, 'attribute');
    
    // for increment attribute with value
    $this->accountRepository->increment($attribute, $value);
    
    //for decrement attribute with value
    $this->accountRepository->decrement($attribute, $value);
```

Delete account:

```php
    // delete by id
    $this->accountRepository->destroy($accountId);
```

Find account(s) and specify columns with you want to fetch

```php
    // find by accountId
    $this->accountRepository->find($accountId, $columns);
    
    // find by attribute (first occurence)
    $this->accountRepository->findBy($attribute, $value, $columns);
    
    // find by attribute (all occurence)
    $this->accountRepository->findAllBy($attribute, $value, $columns);
    
    // to find and return exact attribute 
    $this->accountRepository->findAttribute($accountId, $attribute);
    
    // to find account by accountId with fillable columns
    $this->accountRepository->findFillable($accountId);
    
    // to find account by attribute with fillable columns
    $this->accountRepository->findFillableWhere($id, $attribute, $value, $cmp = '=')

    // to find all accounts by attribute with fillable columns use
    $this->accountRepository->findAllFillable($attribute, $value);

    // to find account with relations
    $this->accountRepository->findFillableWith($id, $related = [])
    

    // to find all accounts
    $this->accountRepository->all($columns)
    
    // to find first account
    $this->accountRepository->first($columns = null)
    
    //for find last account
    $this->accountRepository->last($columns = null)
    
    // if your model extends LaraModel\Models\LaraModel
    // and $showable property is specified you can use
    $this->accountRepository->findForShow($id, $columns = null)
    
    // if your model extends LaraModel\Models\LaraModel
    // and specifed $listable property you can use
    // for find accounts list 
    //[
    //      1 => 'account1',
    //      2 => 'account2'
    //]
    //
    $this->accountRepository->findList($active = true, $listable = null)
    
    // or you can use to find by attribute
    $this->accountRepository->findListBy($attribute, $value, $active = true, $listable = null)
    
    
    // to check if account exists with id
    $this->accountRepository->exists($id);
    
    // to find count of rows matching the given critera
    $this->accountRepository->findCount($attribute, $value, $cmp);
    
    // to check if item exists with given attribute and value
    $this->accountRepository->existsWhere($attribute, $value);
    
    // for pagination
    $this->accountRepository->paginate();
    
    // for pagination based by condition
    $this->accountRepository->paginateWhere($attribute, $value);
```

model related methods

```php

    // to get the binded model    
    $this->account->getModel();

    // get current model query
    $this->account->getModelQuery();

    // to reset model query    
    $this->account->resetModelQuery();

    // to get model's table name
    $this->account->getTable();

    // to get model table's primary key
    $this->account->getKeyName();

    // to get model's fillable columns
    $this->account->getFillableColumns();

```
if your model extends LaraModel\Models\LaraModel you can also use this methods
```php
    // returns columns including table name
    $this->accountRepository->fixColumns($columns, $table = null, $prefix = null);

    // returns indexable columns
    $this->account->getIndexableColumns($full = false, $hidden = true, $group = self::GROUP);

    // returns columns based on $showable property
    $this->accountRepository->getShowableColumns($full = false, $hidden = true, $group = self::GROUP);

    // returns serchable columns
    $this->accountRepository->getSearchableColumns();

    // returns listable columns
    $this->accountRepository->getListableColumns();

    // returns sortable columns
    $this->accountRepository->getSortableColumns($column = null, $group = self::GROUP);

    // returns status column
    $this->accountRepository->getStatusColumn();

    // add sorting options in columns
    $this->accountRepository->setSortingOptions($column = null, $order = 'asc', $group = self::GROUP);

    // returns model relations
    $this->accountRepository->getRelations();

```

Criteria methods
```php

    $this->accountRepository->getCriteria();

    $this->accountRepository->getByCriteria(Criteria $criteria);

    $this->accountRepository->resetScope();

    $this->accountRepository->pushCriteria(Criteria $criteria);

    $this->accountRepository->skipCriteria($status = true);

    $this->accountRepository->applyCriteria();
```

Transaction methods
```php

    $this->accountRepository->startTransaction();
    
    $this->accountRepository->commitTransaction();
    
    $this->accountRepository->rollbackTransaction();
```


##### LaraRepo\Contracts\Criteria\Criteria

```php
    public abstract function apply($modelQuery, RepositoryInterface $repository);
```

## Criteria

Criteria is a simple way to apply specific condition, or set of conditions to the query

## Available Criteria

The following criteria are available:

```php
    LaraRepo\Criteria\Distinct\DistinctCriteria
    LaraRepo\Criteria\Group\GroupByCriteria             __construct($columns)
    LaraRepo\Criteria\Has\HasCriteria                   __construct($columns, $value, $cmp = '=')
    LaraRepo\Criteria\Join\InnerJoinCriteria            __construct($relation, $column = '', $values = [])
    LaraRepo\Criteria\Join\InnerJoinRelationCriteria    __construct($relation, $otherKeys = [])
    LaraRepo\Criteria\Join\LeftJoinCriteria             __construct($relation, $column = '', $values = [])
    LaraRepo\Criteria\Limit\LimitCriteria               __construct($limit)
    LaraRepo\Criteria\Offset\OffsetCriteria             __construct($offset)
    LaraRepo\Criteria\Order\SortCriteria                __construct($column, $order = 'asc', $fixColumns = true)
    LaraRepo\Criteria\Search\SearchCriteria             __construct($value, $columns = null, $table = null)
    LaraRepo\Criteria\Select\SelectCriteria             __construct($columns = [], $table = null)
    LaraRepo\Criteria\Select\SelectFillableCriteria     __construct($include = [], $exclude = [])             
    LaraRepo\Criteria\Select\SelectCriteria             __construct($relation, $columns, $prefix = true)
    LaraRepo\Criteria\Where\ActiveCriteria
    LaraRepo\Criteria\Where\BetweenCriteria             __construct($column, $from, $to)
    LaraRepo\Criteria\Where\OrWhereCriteria             __construct($attribute, $value, $cmp = '=') 
    LaraRepo\Criteria\Where\WhereCriteria               __construct($attribute, $value, $cmp = '=')
    LaraRepo\Criteria\Where\WhereHasRelationCriteria    __construct($relation, $where = [])
    LaraRepo\Criteria\Where\WhereInCriteria             __construct($attribute, $values = [])
    LaraRepo\Criteria\Where\WhereRelationCriteria       __construct($relation, $attribute, $value, $cmp = '=')
    LaraRepo\Criteria\With\RelationCriteria             __construct($relations = [])
    LaraRepo\Criteria\With\WithCountCriteria             __construct($relations)
```

You can make your own Criteria. Your criteria class MUST extend the abstract ```LaraRepo\Criteria\Criteria``` class.

For exapmle:
```php
<?php
namespace YourNamespace;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class NewCriteria extends Criteria
{
    
    public function __construct()
    {
        //your code
    }

    /***
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        //your code here
        return $modelQuery;
    }

}

```

Now, inside you controller class you call pushCriteria method:

```php
<?php

namespace App\Http\Controllers;

use LaraRepo\Criteria\Where\WhereCriteria;
use App\Repositories\Contracts\AccountRepositoryInterface as AccountRepository;

class AccountsController extends Controller
{

    /**
     * @var AccountRepository
     */
    protected $repository;

    /**
     * AccountsController constructor.
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->repository = $accountRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->repository->pushCriteria(new WhereCriteria('id', '15', '>'));
        $items = $this->repository->all(); 
        return view('accounts.index', compact('items'));
    }
}

```


## Credits

This package is largely inspired by [this](https://github.com/prettus/l5-repository) great package by @andersao
