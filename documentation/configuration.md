# Configuration
php-dry can be configured via it's xml configuration. Pass the path to your configuration via the `--config` option
to php-dry.

An example of a valid configuration:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<php-dry xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://github.com/LeoVie/php-dry/tree/main/xsd/php-dry.xsd"
         silent="false"
         minTokenLength="50"
         minSimilarTokensPercentage="80"
         enableLcsAlgorithm="false"
         countOfParamSets="10"
         enableConstructNormalization="false"
         enableCleanCodeScoring="true"
         phpDocumentorReportPath="/tmp/phpDocumentorReport"
         phpDocumentorExecutablePath="/usr/bin/phpDocumentor.phar"
         cachePath="/tmp/php-dry-cache">

    <report>
        <cli/>
        <html directory="reports"/>
        <json filepath="reports/php-dry.json"/>
    </report>
</php-dry>
```

## `report`
In which format should php-dry report detected clones?

There are [examples](examples/report-format) for each report format.

### `cli`
- Attributes: none

### `html`
- Attributes: `directory` \
php-dry will automatically create a directory for the html report and all required resources in this directory.

### `json`
- Attributes: `filepath`

## `php-dry`
Attributes:

### `silent`
- Possible values: `true`, `false`
- Default: `false`

Should command be a silent as possible?

This can be useful, if you are only interested in the html or json report anyhow and don't look at the cli output.

### `minTokenLength`
- Possible values: An integer value `>= 1`

How many tokens should a clone instances contain at least?

Use this, to prevent reporting very small clone instances. For example, probably you don't want to detect the `setUid`
method of all your entities as clones.

### `minSimilarTokensPercentage`
- Possible values: An integer value between `1` and `100`
- Default: `80`

How many similar tokens (in same order) in relation to total token length should be in two methods to treat them as type-3 clone instances?

**Example:**\
Method A: `a -> b -> c -> d -> e` \
Method B: `a -> x -> c -> d`

The token sequence `a -> c -> d` exists in both methods. The similar tokens percentage is calculated as follows:

`(length(similar_token_sequence) / max(length(method_a), length(method_b))) * 100`, so in this example: \
`(3 / 5) * 100 = 60`

If you specify `--min_similar_tokens_percentage = 60`, these two methods will be reported as type-3 clone instances. \
If you specify `--min_similar_tokens_percentage = 61`, they won't.

### `enableLcsAlgorithm`
- Possible values: `true`, `false`
- Default: `false`

Should the longest common subsequence (LCS) algorithm should be used instead of PHP's `similar_text` method?

For deciding, how many similar tokens (in same order) two methods have, there exist two approaches:
- using PHP's built-in `similar_text` method, which is fast, but not almost accurate
- or using the longest common subsequence (LCS) algorithm, which is very slow, but more accurate

Using the LCS algorithm is very (very very) slow at the moment. You should use this only, if you really need it.

### `countOfParamSets`
- Possible values: An integer value between `>= 1`
- Default: `10`

When deciding, if two methods are type-4 clone instances, both methods will be called with same parameters
and their results get compared. If the results are same, they are considered to be type-4 clone instances.

As methods can be completely different, but return same result randomly for an input, they should be called
multiple times. Only if the methods return the same result for _every_ input, they should be considered to be
type-4 clone instances.

You can interpret this option as: How many times should both methods get called with different parameters
until considering them to be type-4 clone instances (if they both return same result on each run)?

### `enableConstructNormalization`
- Possible values: `true`, `false`
- Default: `false`

Detect clones by normalizing language constructs?

An approach to detect type-4 clones is to normalize language constructs on all methods and then comparing
the normalized codes. For example, every `array_map` call can get transformed into a `foreach` loop,
which can be transformed into a `for` loop, which can be transformed into a `while` loop.

This is very (very very) slow at the moment. You should use this only, if you really need it.

### `enableCleanCodeScoring`
- Possible values: `true`, `false`
- Default: `true`

Create scores for type-4 clones depending on their code cleanness?

php-dry can rate found type-4 clone instances by their code cleanness. You can disable this, if you don't care
about this rating.

### `phpDocumentorReportPath`
- Possible values: A valid path to a directory
- Default: `/tmp/phpDocumentorReport`

php-dry uses [phpDocumentor](https://www.phpdoc.org/) for preprocessing PHP files. Specify, where the report of
phpDocumentor should get stored. This path has to merely exist at execution time.

### `phpDocumentorExecutablePath`
- Possible values: A valid path to an executable of phpDocumentor. \
    Can be e.g. the phpDocumentor phar, the phpDocumentor binary or a `docker run` command
- Default: `tools/phpDocumentor.phar`

Where is phpDocumentor located?

### `cachePath`
- Possible values: A valid path to a directory.
- Default: `.`

Where should php-dry store its cache?