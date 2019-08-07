"# cli" 

## Installation Instructions

The following instructions assumes that you are familiar with the necessary technologies required to carry out installation and that you have them already insalled in your machine (i.e you have git, composer, etc. installed).

Based on: 
* php 7.3.3
* symfony/console": "^4.3",
* symfony/filesystem": "^4.3",
* symfony/finder": "^4.3"


### Clone the repository:
```

$ git clone git@github.com:mokgosi/cli.git

```

### Install dependencies
```

$ composer update

```

### Run to optimize autoloader
```

$ composer dump -o

```


### You should be able to run the following commands from you console/terminal from your project root:
```

$ php bin\console.php add

$ php bin\console.php search

$ php bin\console.php delete 1234567

$ php bin\console.php edit 1234567

```

