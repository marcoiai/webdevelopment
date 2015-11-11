# Doctrine 2 Tips and Tricks

Here i'll be putting some tips, tricks and code for Doctrine 2.

## Doctrine Generic Paginator
A paginator class that accepts either DQL and SQL builders and automatically paginates it.

<a href="https://github.com/marcoiai/webdevelopment/blob/master/Doctrine2/DoctrinePaginator.php">DoctrinePaginator</a>

How To use:

## DQL CAST
Follow steps bellow to implement support for CAST function on your project:

1 - Download it's file (Cast.php) from THIS repo;

2 - Setup string_functions directive (usually located at config.yml):

```
doctrine:
    orm:
        entity_managers:
            default:
                dql:
                    string_functions:
                        Cast:
                            Path\Namespace\DQL\Cast
```


Clean cache and use it on your DQL queries:
SELECT CAST(column AS TYPE)

That's it!
