# Introduction 

Missing data is always a problem with data analysis and data mining. The `cleandata` package give you methods to solve this data missing issue.

# Installation

1. Download the zip file
2. Copy the `cleandata` into `koolreport/packages` folder.

# Documentation

The missing value normally comes to KoolReport in form of `null` value. We solve this by either __drop the row__ or __fill new value for it__.

## DropNull

The `DropNull` process will drop the row which has `null` value or meet certain number of `null` occurrences.

Let look at an example:

```
$this->src('db')
->query("select * from customers")
->pipe(new DropNull())
->pipe($this->dataStore('clean_data'));
```

Above is simplest example of using `DropNull` process. All the row which has `null` value will be dropped. As a result, return data will be those __customers__ with full informations.

### Target a certain columns only

Sometime you only drop the row if some certain columns has `null` values:

```
->pipe(new DropNull(array(
    "targetColumns"=>array("salary","tax")
)))
```

### Exclude some columns

If you want to target all columns except some because it is not important, you do:

```
->pipe(new DropNull(array(
    "excludedColumns"=>array("address","city")
)))
```

### Target specific type of columns

For example, You can target `number` columns only, if any of those columns has `null` value, the row will be dropped:

```
->pipe(new DropNull(array(
    "targetColumnType"=>"number"
)))
```

You can target to other column types which are `string`,`date`,`datetime`,`time`

### Threshold

For example, if data row contains more than 2 `null` values, drop the row:

```
->pipe(new DropNull(array(
    "thresh"=>3,
)))
```

### Targeted value

What if you do not want to drop `null` value but the `0` value. The missing data to you is the `0` value, you can do

```
->pipe(new DropNull(array(
    "targetValue"=>0,
)))
```

Of course, you can set any target values regardless number type or string type. The default value of `targetValue` is `null`.

### Stricly Null

By default the the `null` could be empty string or `0` value. To enable strict comparison of both value and type, you set the following:

```
->pipe(new DropNull(array(
    "strict"=>true,
)))
```


## FillNull

The `FillNull` value is another method of cleaning data. We do not drop row with `null` value, rather we fill `null` value with the new value.

```
->pipe(new FillNull(array(
    "newValue"=>0
)))
```

Above code will fill all the `null` value with `10`.

### Targeted value

What if you want to target at `0` value, you can do:"

```
->pipe(new FillNull(array(
    "targetValue"=>0,
    "newValue"=>10,
)))
```

### Fill missing value with MEDIAN and MEAN

In above example, we fill missing value with the value we want. However the better method is to fill them with mean or median of the column values. This solution seems more elegant. You can do:

```
->pipe(new FillNull(array(
    "newValue"=>FillNull::MEAN,
)))
```
For median, you do

```
->pipe(new FillNull(array(
    "newValue"=>FillNull::MEDIAN,
)))
```

### Target some specific columns

You can apply fulling action to some of specified columns:

```
->pipe(new FillNull(array(
    "targetColumns"=>array("salary","tax"),
)))
```

### Exclude some columns

Some columns are not important and missing value does not affect, you can do:

```
->pipe(new FillNull(array(
    "excludedColumns"=>array("lastname","gender"),
)))
```

### Target some specific column type

If you want you can apply the the fill to certain `number` columns:

```
->pipe(new FillNull(array(
    "targetColumnType"=>"number"
)))
```

### Strictly Null

By default the the `null` could be empty string or `0` value. To enable strict comparison of both value and type, you set the following:

```
->pipe(new FillNull(array(
    "strict"=>true,
)))
```


## Support


Please use our forum if you need support, by this way other people can benefit as well. If the support request need privacy, you may send email to us at __support@koolreport.com__.