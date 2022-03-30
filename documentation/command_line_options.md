# `--report-format`
- Possible values: `cli`, `json`, `html`
- Default: `cli`

In which format should php-dry report detected clones?

There are [examples](examples/report-format) for each report format.

# `--html-report-filepath`
- Possible values: A path to an html file

Where to save the html report?

This is only relevant, if `report-format` is `html`.

# `--reports_directory`
- Possible values: A path to a directory

Where to save the json report?

This is only relevant, if `report-format` is `json`.

# `--silent`
- Possible values: `true`, `false`
- Default: `false`

Should command be a silent as possible?

This can be useful, if you are only interested in the html or json report anyhow and don't look at the cli output.

# `--min_token_length`
- Possible values: An integer value `>= 1`

How many tokens should a clone instances contain at least?

Use this, to prevent reporting very small clone instances. For example, probably you don't want to detect the `setUid`
method of all your entities as clones.

# `--min_similar_tokens_percentage`
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

# `--enable_lcs_algorithm`
- Possible values: `true`, `false`
- Default: `false`

Should the longest common subsequence (LCS) algorithm should be used instead of PHP's `similar_text` method?

For deciding, how many similar tokens (in same order) two methods have, there exist two approaches:
- using PHP's built-in `similar_text` method, which is fast, but not almost accurate
- or using the longest common subsequence (LCS) algorithm, which is very slow, but more accurate

Using the LCS algorithm is very (very very) slow at the moment. You should use this only, if you really need it.

# `--count_of_param_sets_for_type4_clones`
- Possible values: An integer value between `>= 1`
- Default: `10`

When deciding, if two methods are type-4 clone instances, both methods will be called with same parameters
and their results get compared. If the results are same, they are considered to be type-4 clone instances.

As methods can be completely different, but return same result randomly for an input, they should be called
multiple times. Only if the methods return the same result for _every_ input, they should be considered to be
type-4 clone instances.

You can interpret this option as: How many times should both methods get called with different parameters
until considering them to be type-4 clone instances (if they both return same result on each run)?

# `--enable_construct_normalization`
- Possible values: `true`, `false`
- Default: `false`

Detect clones by normalizing language constructs?

An approach to detect type-4 clones is to normalize language constructs on all methods and then comparing
the normalized codes. For example, every `array_map` call can get transformed into a `foreach` loop,
which can be transformed into a `for` loop, which can be transformed into a `while` loop.

This is very (very very) slow at the moment. You should use this only, if you really need it.