# JSONInclude-PHP

## What is JSONInclude?
XML has [XInclude](http://www.w3.org/TR/xinclude/) which allows to combine XML from different files into one. There is no generic equivalent for JSON that I am aware of, so I came up with a simple idea for it. I believe having this can be useful if we want to dynamically combine JSON output, e.g. in an API, from different static files without duplicating information in those files.

Let's explain it with an example ... assume we have one file:

    {
     "key1" : "value1",
     "key2" : "@file2.txt"
    }

The "@" is an include symbol which indicates the string behind it should be treated as the filename for inclusion. The content of the second file is this:

    {
     "key1" : "value2"
    }

Loading the first file with a JSONInclude parser would return the following output:

    {
     "key1" : "value1",
     "key2" : {
      "key1" : "value2"
     }
    }

## What is JSONInclude-PHP?
A simple parser for JSONInclude as a reference implementation written in PHP.

## Who's behind it?
JSONInclude-PHP was created by Lukas Rosenstock. Contact me through my website at [lukasrosenstock.net](http://lukasrosenstock.net/).

## Terms
This software is released under the MPL - see LICENSE file for details.