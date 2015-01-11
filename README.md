#Database class

## Installation

1) Add the Database.php file to your project
2) Initialisze the class
```
$db = new Database($host, $user, $passwd, $name);
```
3) You can specify options for PDO when you use the connect method, for exemple :
```
$db->connect([PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);
```
If you don't need any options, you can call the connect method without any argument :
```
$db->connect();
```

## Usage

### Select
```
$db->select("MyTable");
```
To specify which field you need you can add an array :
```
$db->select("MyTable", ["field1", "field2"]);
```

### Where
There is 2 method to add a **where** clause :
```
$db->where(["field1=3", "field2='lorem'"]);
```
or
```
$db->where(["field1=3"]);
$db->where(["field2='lorem'"]);
```

### Insert
```
$db->insert("MyTable", [["field1" => 3],["field2" => "lorem"]]);
```

### Update
```
$db->update("MyTable");
```

### Set
There is 2 method to add a **set** :
```
$db->set(["field1=3", "field2='lorem'"]);
```
or
```
$db->set(["field1=3"]);
$db->set(["field2='lorem'"]);
```

### Limit
```
$db->limit(1);
```
or
```
$db->limit(1, 5);
```

### Group by / Order by
```
$db->groupby("name");
$db->orderby("name ASC");
```

### Execute
```
$db->execute();
```
if it's a select it will return the result so you'll need to store it :
```
$result = $db->execute();
$data = $result->fetchAll();
```