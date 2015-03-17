## LeezyPheanstalkBundle Command Line Tools

The LeezyPheanstalkBundle provides a number of command line utilities. 
Commands are available for the following tasks:

1. Delete a job.
2. Flush a tube.
3. List available tubes.
4. Pause a tube.
5. Peek a tube to get the first ready/burried job and associated data.
6. Peek a job and get associated data.
7. Put a new job in a tube.
8. Get statistics about beanstalkd server.
9. Get statistics about a job.
10. Get statistics about a tube.
11. Get next ready job.

**Note:**

```
You must have correctly installed and configured the LeezyPheanstalkBundle before using 
these commands.
```

### Delete a job

``` bash
$ php app/console leezy:pheanstalk:delete-job 42
```

### Flush a tube.

``` bash
$ php app/console leezy:pheanstalk:flush-tube your-tube
```

**Note:**

```
When you flush a tube, it will be removed from the beanstalkd server.
```

### List available tubes.

``` bash
$ php app/console leezy:pheanstalk:list-tube
```

**Note:**

```
Tubes that are display contains at least one job.
```

### Pause a tube.

``` bash
$ php app/console leezy:pheanstalk:pause-tube your-tube
```

### Peek a tube to get the first ready job and associated data.

``` bash
$ php app/console leezy:pheanstalk:peek-tube your-tube
```

### Peek a tube to get the first burried job and associated data.

``` bash
$ php app/console leezy:pheanstalk:peek-tube -b your-tube
```

### Peek a job and get associated data.

``` bash
$ php app/console leezy:pheanstalk:peek 42
```

### Put a new job in a tube.

``` bash
$ php app/console leezy:pheanstalk:put your-tube "Hello world - I am a job"
```

### Get statistics about beanstalkd server.

``` bash
$ php app/console leezy:pheanstalk:stats
```

### Get statistics about a job.

``` bash
$ php app/console leezy:pheanstalk:stats-job 42
```

### Get statistics about a tube.

``` bash
$ php app/console leezy:pheanstalk:stats-tube your-tube
```

### Get next ready job.

``` bash
$ php app/console leezy:pheanstalk:next-ready your-tube --details
```